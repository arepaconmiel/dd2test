<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Plugin\Checkout;
/**
 * Class TotalsInformationManagement
 * @package Magestore\Giftvoucher\Plugin\Checkout
 */
class TotalsInformationManagement
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * PaymentInformationManagement constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    )
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\TotalsInformationManagement $totalsInformationManagement
     * @param $totals
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @return mixed
     */
    public function aroundCalculate(
        \Magento\Checkout\Model\TotalsInformationManagement $totalsInformationManagement,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    )
    {
        $totals = $proceed($cartId, $addressInformation);
        if ($totals && $totals->getBaseDiscountAmount()) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $baseGiftVoucherDiscount = $quote->getBaseGiftVoucherDiscount();
            if ($baseGiftVoucherDiscount) {
                $totals->setBaseDiscountAmount($totals->getBaseDiscountAmount() + $baseGiftVoucherDiscount);
                $totals->setDiscountAmount($totals->getDiscountAmount() + $quote->getGiftVoucherDiscount());
            }
        }
        return $totals;
    }
}