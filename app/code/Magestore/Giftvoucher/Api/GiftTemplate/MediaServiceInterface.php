<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\GiftTemplate;

/**
 * Interface MediaServiceInterface
 * @package Magestore\Giftvoucher\Api\GiftTemplate
 */
interface MediaServiceInterface
{
    /**
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface|\Magestore\Giftvoucher\Model\GiftTemplate $giftTemplate
     * @return
     */
    public function updateMedia(\Magestore\Giftvoucher\Model\GiftTemplate $giftTemplate);
    
    /**
     * Get images json from gift Template
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $model
     * @return string
     */
    public function getImagesJson($model);
    
    /**
     *
     * @param string $image
     * @return string
     */
    public function getImageUrl($image);
    
    /**
     * Get first image url from gift Template
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $giftTemplate
     * @return string
     */
    public function getFirstImageUrl($giftTemplate);
}
