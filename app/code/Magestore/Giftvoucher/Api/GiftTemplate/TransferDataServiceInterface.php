<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\GiftTemplate;

/**
 * Interface TransferDataServiceInterface
 * @package Magestore\Giftvoucher\Api\GiftTemplate
 */
interface TransferDataServiceInterface
{
    /**
     * Get print data
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftcode
     * @return array
     */
    public function toPrintData($giftcode);
}
