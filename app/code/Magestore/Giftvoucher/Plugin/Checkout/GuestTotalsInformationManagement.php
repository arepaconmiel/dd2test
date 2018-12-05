<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Plugin\Checkout;
/**
 * Class GuestTotalsInformationManagement
 * @package Magestore\Giftvoucher\Plugin\Checkout
 */
class GuestTotalsInformationManagement
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
     * @param \Magento\Checkout\Model\GuestTotalsInformationManagement $totalsInformationManagement
     * @param $paymentDetails
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @return mixed
     */
    public function aroundCalculate(
        \Magento\Checkout\Model\GuestTotalsInformationManagement $totalsInformationManagement,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    )
    {
        $totals = $proceed($cartId, $totalsInformationManagement);
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