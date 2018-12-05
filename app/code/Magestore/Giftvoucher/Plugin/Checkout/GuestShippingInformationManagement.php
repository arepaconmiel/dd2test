<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Plugin\Checkout;

/**
 * Class GuestShippingInformationManagement
 * @package Magestore\Giftvoucher\Plugin\Checkout
 */
class GuestShippingInformationManagement
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
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\GuestShippingInformationManagement $shippingInformationManagement
     * @param $paymentDetails
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return mixed
     */
    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\GuestShippingInformationManagement $shippingInformationManagement,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    )
    {
        $paymentDetails = $proceed($cartId, $addressInformation);
        $totals = $paymentDetails->getTotals();
        if ($totals->getBaseDiscountAmount()) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->get($cartId);
            $baseGiftVoucherDiscount = $quote->getBaseGiftVoucherDiscount();
            if($baseGiftVoucherDiscount){
                $totals->setBaseDiscountAmount($totals->getBaseDiscountAmount() + $baseGiftVoucherDiscount);
                $totals->setDiscountAmount($totals->getDiscountAmount() + $quote->getGiftVoucherDiscount());
            }
        }
        return $paymentDetails->setTotals($totals);
    }
}