<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

/**
 * Interface GiftcodeDiscountInterface
 * @package Magestore\Giftvoucher\Api\Data
 */
interface GiftcodeDiscountInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const CODE = 'code';
    const DISCOUNT = 'discount';

    /**
     * Get Gift Code
     *
     * @return string
     */
    public function getCode();

    /**
     * Get gift code discount
     *
     * @return string
     */
    public function getDiscount();


    /**
     *  Set gift code
     * @param string $code
     * @return string
     */
    public function setCode($code);

    /**
     *
     *  Set gift code
     * @param string $discount
     * @return string
     */
    public function setDiscount($discount);
}
