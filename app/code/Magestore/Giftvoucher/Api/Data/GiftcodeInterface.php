<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

/**
 * Interface GiftcodeInterface
 * @package Magestore\Giftvoucher\Api\Data
 */
interface GiftcodeInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const GIFT_CODE = 'gift_code';
    const BALANCE = 'balance';

    /**
     * Get Gift Code
     *
     * @return string
     */
    public function getGiftCode();

    /**
     * Get gift code balance
     *
     * @return string
     */
    public function getBalance();


    /**
     *  Set gift code
     * @param string $giftCode
     * @return string
     */
    public function setGiftCode($giftCode);

    /**
     *  Set gift code
     * @param string $balance
     * @return string
     */
    public function setBalance($balance);
}
