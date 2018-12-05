<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Plugin\Quote\GuestCart;
/**
 * Class GuestCartTotalRepository
 * @package Magestore\Giftvoucher\Plugin\Quote\GuestCart
 */
class GuestCartTotalRepository
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
     * @param \Magento\Checkout\Model\PaymentInformationManagement $paymentInformationManagement
     * @param $paymentDetails
     * @param $cartId
     * @return $paymentDetails
     */
    public function aroundGet(
        \Magento\Quote\Model\GuestCart\GuestCartTotalRepository $guestCartTotalRepository,
        \Closure $proceed,
        $cartId
    )
    {
        $quoteTotals = $proceed($cartId);
        if ($quoteTotals->getBaseDiscountAmount()) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->quoteRepository->get($cartId);
            $baseGiftVoucherDiscount = $quote->getBaseGiftVoucherDiscount();
            if($baseGiftVoucherDiscount){
                $quoteTotals->setBaseDiscountAmount($quoteTotals->getBaseDiscountAmount() + $baseGiftVoucherDiscount);
                $quoteTotals->setDiscountAmount($quoteTotals->getDiscountAmount() + $quote->getGiftVoucherDiscount());
            }
        }
        return $quoteTotals;
    }
}