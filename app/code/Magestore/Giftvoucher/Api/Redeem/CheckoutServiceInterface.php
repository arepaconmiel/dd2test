<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Redeem;

/**
 * Interface CheckoutServiceInterface
 * @package Magestore\Giftvoucher\Api\Redeem
 */
interface CheckoutServiceInterface
{
    /**
     * Get Existed Gift Card codes
     *
     * @param int $cartId
     * @return \Magestore\Giftvoucher\Api\Data\GiftcodeInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getExistedGiftCodes($cartId);

    /**
     * Get using Gift Card codes
     *
     * @param int $cartId
     * @return \Magestore\Giftvoucher\Api\Data\GiftcodeDiscountInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUsingGiftCodes($cartId);

    /**
     * Get quote
     *
     * @param int $cartId
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getQuote($cartId);

    /**
     * @param int $cartId
     * @param \Magestore\Giftvoucher\Api\Data\GiftcodeDiscountInterface[] $addedCodes
     * @param string $existedCode
     * @param string $newCode
     * @return \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface
     */
    public function applyCodes($cartId, $addedCodes = [], $existedCode = '', $newCode = '');

    /**
     * @param int $cartId
     * @return \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface
     */
    public function removeCodes($cartId);

    /**
     * @param int $cartId
     * @param string $giftCode
     * @return \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface
     */
    public function removeCode($cartId, $giftCode = '');

    /**
     * @param int $cartId
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftVoucher
     * @return $this
     */
    public function addVoucherToQuote($cartId, \Magestore\Giftvoucher\Model\Giftvoucher $giftVoucher);

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    public function processOrderPlaceAfter(\Magento\Sales\Api\Data\OrderInterface $order);
}
