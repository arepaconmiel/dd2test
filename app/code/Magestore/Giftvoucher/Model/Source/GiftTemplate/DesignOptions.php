<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source\GiftTemplate;

/**
 * Class DesignOptions
 * @package Magestore\Giftvoucher\Model\Source\GiftTemplate
 */
class DesignOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface
     */
    protected $giftTemplateIOService;


    /**
     * DesignOptions constructor.
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $giftTemplateIOService
     */
    public function __construct(\Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $giftTemplateIOService)
    {
        $this->giftTemplateIOService = $giftTemplateIOService;
    }
    
    /**
     * Get the gift card's type
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $templates = $this->giftTemplateIOService->getAvailableTemplates();
        if (count($templates)) {
            foreach ($templates as $template) {
                $options[$template] = $template;
            }
        }
         return $options;
    }

    /**
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (is_null($this->_options)) {
            $this->_options = [];
            foreach ($this->getOptionArray() as $value => $label) {
                $this->_options[] = [
                    'label' => $label,
                    'value' => $value
                ];
            }
        }
        if ($withEmpty) {
            array_unshift($this->_options, [
                'value' => '',
                'label' => __('-- Please Select --'),
            ]);
        }
        return $this->_options;
    }
}
