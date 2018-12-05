<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Service\Redeem;

/**
 * Class CalculationService
 * @package Magestore\Giftvoucher\Service\Redeem
 */
class CalculationService implements \Magestore\Giftvoucher\Api\Redeem\CalculationServiceInterface
{
    /**
     * @var array
     */
    protected $giftCodeItemsTotal = [];

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helper;

    /**
     * CalculationService constructor.
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     */
    public function __construct(
        \Magestore\Giftvoucher\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @param int $storeId
     * @return bool
     */
    public function isApplyGiftAfterTax($storeId)
    {
        $applyGiftAfterTax = (bool) $this->helper->getGeneralConfig('apply_after_tax', $storeId);
        return $applyGiftAfterTax;
    }

    /**
     * @param $quote
     * @return $this
     */
    public function clearData($quote)
    {
        $quote->setGiftVoucherGiftCodes('');
        $quote->setGiftVoucherGiftCodesDiscount('');
        $quote->setGiftVoucherGiftCodesMaxDiscount('');
        $quote->setCodesBaseDiscount('');
        $quote->setCodesDiscount('');
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param $address
     * @return bool
     */
    public function validateQuote(\Magento\Quote\Model\Quote $quote, $address)
    {
        if (!$quote->getGiftVoucherGiftCodes()) {
            return false;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return false;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return false;
        }
        if ($quote->getCouponCode() && !$this->helper->getGeneralConfig('use_with_coupon')) {
            $this->clearData($quote);
            return false;
        }
        return true;
    }

    /**
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return bool
     */
    public function validateGiftCode(\Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher, $quote, $address)
    {
        $storeId = $this->helper->getStoreId($quote);
        if ($giftvoucher->getStatus() != \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE
            || $giftvoucher->getBalance() == 0
            || !$giftvoucher->validate($address)
            || !(!$giftvoucher->getStoreId() || !$storeId || $storeId == $giftvoucher->getStoreId())
        ) {
            return false;
        }
        return true;
    }

    /**
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher
     * @param int $customerId
     * @return bool
     */
    public function validateCustomer(\Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher, $customerId)
    {
        if (!($giftvoucher instanceof \Magestore\Giftvoucher\Model\Giftvoucher)) {
            return false;
        }
        if (!$giftvoucher->getId()) {
            return false;
        }
        $shareCard = intval($this->helper->getGeneralConfig('share_card'));
        if ($shareCard < 1) {
            return true;
        }
        $customersUsed = $giftvoucher->getCustomerIdsUsed();
        if ($shareCard > count($customersUsed) || in_array($customerId, $customersUsed)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Calculate quote totals for each giftCode and save results
     *
     * @param $items
     * @param $giftCodes
     * @param $address
     * @param bool $afterTax
     * @return $this
     */
    public function initTotals($items, $giftCodes, $address, $isApplyGiftAfterTax = false)
    {
        foreach ($giftCodes as $giftCode) {
            if ($this->validateGiftCode($giftCode, $address->getQuote(), $address)
                && $this->validateCustomer($giftCode, $address->getQuote()->getCustomerId())
            ) {
                $totalItemsPrice = 0;
                $totalBaseItemsPrice = 0;
                $validItemsCount = 0;
                foreach ($items as $item) {
                    //Skipping child items to avoid double calculations
                    if ($item->getParentItemId()) {
                        continue;
                    }
                    if ($item->isDeleted() || $item->getProduct()->getTypeId() == 'giftvoucher' || !$giftCode->getActions()->validate($item)) {
                        continue;
                    }

                    $qty = $item->getTotalQty();
                    $totalItemsPrice += $this->getItemPrice($item) * $qty - $item->getDiscountAmount();
                    $totalBaseItemsPrice += $this->getItemBasePrice($item) * $qty - $item->getBaseDiscountAmount();
                    if($isApplyGiftAfterTax){
                        $totalItemsPrice += $item->getTaxAmount();
                        $totalBaseItemsPrice += $item->getBaseTaxAmount();
                    }
                    $validItemsCount++;
                }

                $this->giftCodeItemsTotal[$giftCode->getGiftCode()] = [
                    'items_price' => $totalItemsPrice,
                    'base_items_price' => $totalBaseItemsPrice,
                    'items_count' => $validItemsCount,
                ];
            }
        }
        return $this;
    }

    /**
     * Get gift code items total
     *
     * @param $code
     * @return array|null
     */
    public function getGiftCodeItemsTotal($code)
    {
        if (isset($this->giftCodeItemsTotal[$code]))
            return $this->giftCodeItemsTotal[$code];
        return null;
    }

    /**
     * Return item base price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return float
     */
    public function getItemBasePrice($item)
    {
        $price = $item->getBaseDiscountCalculationPrice();
        return ($price !== null) ? $price : $item->getBaseCalculationPrice();
    }

    /**
     * Return item price
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return float
     */
    public function getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return $price === null ? $item->getCalculationPrice() : $price;
    }
}
