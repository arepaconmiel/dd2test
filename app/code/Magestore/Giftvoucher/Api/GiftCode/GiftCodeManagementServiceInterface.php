<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\GiftCode;

/**
 * Interface GiftCodeManagementServiceInterface
 * @package Magestore\Giftvoucher\Api\GiftCode
 */
interface GiftCodeManagementServiceInterface
{
    /**
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return array
     */
    public function getGiftCodesFromOrderItem($orderItem);

    /**
     * Refund gift code
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftVoucherInterface $giftCode
     * @param int $orderIncrementId
     * @return $this
     */
    public function refundGiftCode($giftCode, $orderIncrementId);

    /**
     * Cancel gift code
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftVoucherInterface $giftCode
     * @param int $orderIncrementId
     * @return $this
     */
    public function cancelGiftCode($giftCode, $orderIncrementId);

    /**
     * Check gift code
     *
     * @param string $giftCode
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface || null
     */
    public function check($giftCode);

    /**
     * Check gift code
     *
     * @param string $giftCode
     * @return \Magestore\Giftvoucher\Api\Data\HistoryInterface[] || null
     */
    public function checkHistory($giftCode);
    /**
     * Send email
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftcodeSendEmailJsonInterface $data
     * @return boolean
     */
    public function sendEmail($data);


    /**
     * Get gift code from gift code array
     * Return an array with key is gift code and value is gift code model
     *
     * @param array $giftcode
     * @return array
     */
    public function getUsableGiftCodeCollection($giftcode = []);
}
