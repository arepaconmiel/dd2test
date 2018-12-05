<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Plugin\Checkout;
/**
 * Class GuestPaymentInformationManagement
 * @package Magestore\Giftvoucher\Plugin\Checkout
 */
class GuestPaymentInformationManagement
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
     * @param \Magento\Checkout\Model\PaymentInformationManagement $paymentInformationManagement
     * @param $paymentDetails
     * @param $cartId
     * @return $paymentDetails
     */
    public function aroundGetPaymentInformation(
        \Magento\Checkout\Model\GuestPaymentInformationManagement $paymentInformationManagement,
        \Closure $proceed,
        $cartId
    )
    {
        $paymentDetails = $proceed($cartId);
        $totals = $paymentDetails->getTotals();
        if ($totals->getBaseDiscountAmount()) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $baseGiftVoucherDiscount = $quote->getBaseGiftVoucherDiscount();
            if ($baseGiftVoucherDiscount) {
                $totals->setBaseDiscountAmount($totals->getBaseDiscountAmount() + $baseGiftVoucherDiscount);
                $totals->setDiscountAmount($totals->getDiscountAmount() + $quote->getGiftVoucherDiscount());
            }
        }
        return $paymentDetails->setTotals($totals);
    }
}