<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Total\Quote;

/**
 * Class GiftvoucherAbstract
 * @package Magestore\Giftvoucher\Model\Total\Quote
 */
class GiftvoucherAbstract extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magestore\Giftvoucher\Service\Redeem\CalculationService
     */
    protected $calculationService;

    /**
     * @var \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface
     */
    protected $giftCodeManagementService;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $taxConfig;

    /**
     * GiftvoucherAbstract constructor.
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magestore\Giftvoucher\Service\Redeem\CalculationService $calculationService
     * @param \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface $giftCodeManagementService
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magestore\Giftvoucher\Service\Redeem\CalculationService $calculationService,
        \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface $giftCodeManagementService,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Tax\Model\Config $taxConfig
    )
    {
        $this->helper = $helper;
        $this->calculationService = $calculationService;
        $this->giftCodeManagementService = $giftCodeManagementService;
        $this->priceCurrency = $priceCurrency;
        $this->taxConfig = $taxConfig;
    }

    public function calculateDiscount(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total,
        $isApplyGiftAfterTax = false
    )
    {
        $address = $shippingAssignment->getShipping()->getAddress();
        if (!$this->calculationService->validateQuote($quote, $address)) {
            return $this;
        }
        if (!$codes = $quote->getGiftVoucherGiftCodes()) {
            return false;
        }
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        $codesArray = array_unique(explode(',', $codes));

        if ($quote->getGiftVoucherGiftCodesMaxDiscount()) {
            $giftMaxUseAmount = unserialize($quote->getGiftVoucherGiftCodesMaxDiscount());
        }

        if (!isset($giftMaxUseAmount) || !is_array($giftMaxUseAmount)) {
            $giftMaxUseAmount = array();
        }

        $usableGiftCodes = $this->giftCodeManagementService->getUsableGiftCodeCollection($codesArray);

        $this->calculationService->initTotals($items, $usableGiftCodes, $address, $isApplyGiftAfterTax);

        $codesBaseDiscount = $codesDiscount = [];

        $currencyRate = $this->priceCurrency->convert(1, false, false);
        $store = $quote->getStore();

        $allowDiscountShipping = $this->helper->getStoreConfig('giftvoucher/general/use_for_ship', $quote->getStoreId());

        foreach ($codesArray as $key => $code) {
            if (!$usableGiftCodes[$code]) {
                $codesBaseDiscount[] = 0;
                $codesDiscount[] = 0;
                continue;
            }

            $totalBaseDiscount = $totalDiscount = 0;

            /** @var \Magestore\Giftvoucher\Model\Giftvoucher $giftCode */
            $giftCode = $usableGiftCodes[$code];

            $maxBaseDiscount = $giftCode->getBaseBalance($quote->getStoreId());

            if (array_key_exists($code, $giftMaxUseAmount)) {
                $maxBaseDiscount = min($maxBaseDiscount, floatval($giftMaxUseAmount[$code]) / $currencyRate);
            }


            if ($itemsTotal = $this->calculationService->getGiftCodeItemsTotal($code)) {
//                $maxBaseDiscount = min($maxBaseDiscount, $itemsTotal['base_items_price']);
                $baseItemsPrice = 0;
                /** @var \Magento\Quote\Model\Quote\Item\AbstractItem $item */
                foreach ($items as $item) {
                    if ($item->getParentItemId()) {
                        continue;
                    }

                    if ($item->isDeleted() || $item->getProduct()->getTypeId() == 'giftvoucher' || !$giftCode->getActions()->validate($item)) {
                        continue;
                    }

                    $qty = $item->getTotalQty();
                    $itemPrice = $this->calculationService->getItemPrice($item);
                    $baseItemPrice = $this->calculationService->getItemBasePrice($item);
                    $itemPriceAfterDiscount = $itemPrice * $qty - $item->getDiscountAmount();

                    $baseDiscountAmount = $item->getBaseDiscountAmount();
                    $baseItemPriceAfterDiscount = $baseItemPrice * $qty - $baseDiscountAmount;
                    if ($isApplyGiftAfterTax) {
                        $itemPriceAfterDiscount += $item->getTaxAmount();
                        $baseItemPriceAfterDiscount += $item->getBaseTaxAmount();
                    }
                    $baseItemsPrice += $baseItemPriceAfterDiscount + $item->getBaseGiftVoucherDiscount();
                    if ($baseItemsPrice == $itemsTotal['base_items_price']) {
                        $baseGiftCardDiscountAmount = $maxBaseDiscount - $totalBaseDiscount;
                    } else {
                        $discountRate = ($baseItemPriceAfterDiscount + $item->getBaseGiftVoucherDiscount()) / $itemsTotal['base_items_price'];
                        $baseGiftCardDiscountAmount = $maxBaseDiscount * $discountRate;
                    }

                    $baseGiftCardDiscountAmount = min($baseGiftCardDiscountAmount, $baseItemPriceAfterDiscount);
                    $baseGiftCardDiscountAmount = $this->priceCurrency->round($baseGiftCardDiscountAmount);

                    $giftCardDiscountAmount = $this->priceCurrency->convert($baseGiftCardDiscountAmount, $store);
                    $giftCardDiscountAmount = min($giftCardDiscountAmount, $itemPriceAfterDiscount);
                    $giftCardDiscountAmount = $this->priceCurrency->round($giftCardDiscountAmount);

                    $item->setBaseGiftVoucherDiscount($item->getBaseGiftVoucherDiscount() + $baseGiftCardDiscountAmount)
                        ->setGiftVoucherDiscount($item->getGiftVoucherDiscount() + $giftCardDiscountAmount)
                        ->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $baseGiftCardDiscountAmount)
                        ->setMagestoreDiscount($item->getMagestoreDiscount() + $giftCardDiscountAmount)
                        ->setBaseDiscountAmount($item->getBaseDiscountAmount() + $baseGiftCardDiscountAmount)
                        ->setDiscountAmount($item->getDiscountAmount() + $giftCardDiscountAmount);

                    $totalBaseDiscount += $baseGiftCardDiscountAmount;
                    $totalDiscount += $giftCardDiscountAmount;
                }
                if ($maxBaseDiscount > $totalBaseDiscount && $allowDiscountShipping) {
                    $shippingAmount = $address->getShippingAmountForDiscount();
                    if ($shippingAmount !== null) {
                        $baseShippingAmount = $address->getBaseShippingAmountForDiscount();
                    } else {
                        $baseShippingAmount = $address->getBaseShippingAmount();
                    }
                    $baseShippingAmount = $baseShippingAmount - $address->getBaseShippingDiscountAmount();
                    // Fix duplicate shipping when use multiple gift codes
                    $baseShippingAmount -= $total->getMagestoreBaseDiscountForShipping();
                    if ($isApplyGiftAfterTax) {
                        $baseShippingAmount += $address->getBaseShippingTaxAmount();
                    }
                    $baseDiscountShipping = $maxBaseDiscount - $totalBaseDiscount;
                    $baseDiscountShipping = min($baseDiscountShipping, $baseShippingAmount);
                    $baseDiscountShipping = $this->priceCurrency->round($baseDiscountShipping);
                    $discountShipping = $this->priceCurrency->convert($baseDiscountShipping);
                    $discountShipping = $this->priceCurrency->round($discountShipping);

                    $total->setBaseGiftvoucherDiscountForShipping($total->getBaseGiftvoucherDiscountForShipping() + $baseDiscountShipping);
                    $total->setGiftvoucherDiscountForShipping($total->getGiftvoucherDiscountForShipping() + $discountShipping);
                    $total->setMagestoreBaseDiscountForShipping($total->getMagestoreBaseDiscountForShipping() + $baseDiscountShipping);
                    $total->setMagestoreDiscountForShipping($total->getMagestoreDiscountForShipping() + $discountShipping);
                    $total->setBaseShippingDiscountAmount(max(0, $total->getBaseShippingDiscountAmount() + $baseDiscountShipping));
                    $total->setShippingDiscountAmount(max(0, $total->getShippingDiscountAmount() + $discountShipping));
                    $totalBaseDiscount += $baseDiscountShipping;
                    $totalDiscount += $discountShipping;
                }
            }
            $codesBaseDiscount[] = $totalBaseDiscount;
            $codesDiscount[] = $totalDiscount;
        }

        $baseGiftVoucherDiscount = array_sum($codesBaseDiscount);
        $giftVoucherDiscount = array_sum($codesDiscount);

        $codesBaseDiscountString = implode(',', $codesBaseDiscount);
        $codesDiscountString = implode(',', $codesDiscount);

        $total->setBaseGiftVoucherDiscount($baseGiftVoucherDiscount);
        $total->setGiftVoucherDiscount($giftVoucherDiscount);
        $total->setMagestoreBaseDiscount($total->getMagestoreBaseDiscount() + $baseGiftVoucherDiscount);
        $total->setMagestoreDiscount($total->getMagestoreDiscount() + $giftVoucherDiscount);
        $total->setCodesBaseDiscount($codesBaseDiscountString);
        $total->setCodesDiscount($codesDiscountString);
        $total->setBaseDiscountAmount($total->getBaseDiscountAmount() - $baseGiftVoucherDiscount);
        $total->setDiscountAmount($total->getDiscountAmount() - $giftVoucherDiscount);
        $total->setBaseSubtotalWithDiscount($total->getBaseSubtotalWithDiscount() - $baseGiftVoucherDiscount);
        $total->setSubtotalWithDiscount($total->getSubtotalWithDiscount() - $giftVoucherDiscount);

        $quote->setBaseGiftVoucherDiscount($total->getBaseGiftVoucherDiscount());
        $quote->setGiftVoucherDiscount($total->getGiftVoucherDiscount());
        $quote->setMagestoreBaseDiscount($total->getMagestoreBaseDiscount());
        $quote->setMagestoreDiscount($total->getMagestoreDiscount());
        $quote->setGiftVoucherGiftCodes($codes);
        $quote->setGiftVoucherGiftCodesDiscount($codesDiscountString);
        $quote->setCodesBaseDiscount($codesBaseDiscountString);
        $quote->setCodesDiscount($codesDiscountString);
        $quote->setBaseGiftvoucherDiscountForShipping($total->getBaseGiftvoucherDiscountForShipping());
        $quote->setGiftvoucherDiscountForShipping($total->getGiftvoucherDiscountForShipping());
        $quote->setMagestoreBaseDiscountForShipping($total->getMagestoreBaseDiscountForShipping());
        $quote->setMagestoreDiscountForShipping($total->getMagestoreDiscountForShipping());

        $total->setTotalAmount($this->getCode(), (string)-$giftVoucherDiscount);
        $total->setBaseTotalAmount($this->getCode(), (string)-$baseGiftVoucherDiscount);
    }

    /**
     * Fetch (Retrieve data as array)
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = [];
        $giftVoucherDiscount = $total->getGiftVoucherDiscount();
        if ($giftVoucherDiscount != 0) {
            $result = [
                'code' => $this->getCode(),
                'title' => __('Gift Card'),
                'value' => -$giftVoucherDiscount,
                'gift_codes' => $quote->getGiftVoucherGiftCodes(),
                'codes_base_discount' => $quote->getCodesBaseDiscount(),
                'codes_discount' => $quote->getCodesDiscount()
            ];
        }
        return $result;
    }
}
