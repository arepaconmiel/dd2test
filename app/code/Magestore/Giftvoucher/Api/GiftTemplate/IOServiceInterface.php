<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\GiftTemplate;

/**
 * Interface IOServiceInterface
 * @package Magestore\Giftvoucher\Api\GiftTemplate
 */
interface IOServiceInterface
{
    /**
     *
     * @param string $designPattern
     * @return string
     */
    public function getTemplateContent($designPattern);
    
    /**
     * Get template file system
     *
     * @param int $templateId
     * @return string
     */
    public function getTemplateFile($templateId);
    
    /**
     * Get list of available gift card templates
     *
     * @return array
     */
    public function getAvailableTemplates();
}
