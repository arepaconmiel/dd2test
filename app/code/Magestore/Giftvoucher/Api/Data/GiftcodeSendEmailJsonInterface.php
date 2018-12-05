<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

/**
 * Interface GiftcodeSendEmailJsonInterface
 * @package Magestore\Giftvoucher\Api\Data
 */
interface GiftcodeSendEmailJsonInterface
{
    const GIFT_CODE = 'gift_code';
    const TYPE = 'type';
    /**
     * Get Gift Code
     * @param string $giftCode
     * @return $this
     */
    public function setGiftCode($giftCode);

    /**
     * Get gift code
     *
     * @return string
     */
    public function getGiftCode();

    /**
     * Get type
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get gift type
     *
     * @return string
     */
    public function getType();
}
