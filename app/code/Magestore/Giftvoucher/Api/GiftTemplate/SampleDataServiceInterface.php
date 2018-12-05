<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\GiftTemplate;

/**
 * Interface SampleDataServiceInterface
 * @package Magestore\Giftvoucher\Api\GiftTemplate
 */
interface SampleDataServiceInterface
{
    /**
     *
     * @return string
     */
    public function getLogo();
    
    /**
     *
     * @return string
     */
    public function getMessageSample();
    
    /**
     *
     * @return float
     */
    public function getGiftCardValueSample();
    
    /**
     *
     * @return string
     */
    public function getBarcodeFileSample();
    
    /**
     *
     * @return string
     */
    public function getExpiredDataSample();
    
    /**
     *
     * @return string
     */
    public function getGiftCodeSample();
    
    /**
     *
     * @return string
     */
    public function getNotesSample();
    
    /**
     *
     * @return string
     */
    public function getTextColorSample();
    /**
     *
     * @return string
     */
    public function getStyleColorSample();
    
    /**
     *
     * @return array
     */
    public function getSampleData();
}
