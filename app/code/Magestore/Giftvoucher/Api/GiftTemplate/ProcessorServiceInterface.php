<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\GiftTemplate;

/**
 * Interface ProcessorServiceInterface
 * @package Magestore\Giftvoucher\Api\GiftTemplate
 */
interface ProcessorServiceInterface
{
    /**
     * Get HTML preview of gift card or gift template
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher|null $giftCode
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $giftTemplate
     * @return string
     */
    public function preview($giftCode, $giftTemplate);
    
    /**
     * Print-out gift code to a gift card HTML
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftCode
     * @return string
     */
    public function printGiftCodeHtml($giftCode);
}
