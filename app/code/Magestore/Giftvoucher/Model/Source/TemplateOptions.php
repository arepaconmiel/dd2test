<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Source;

/**
 * Class TemplateOptions
 * @package Magestore\Giftvoucher\Model\Source
 */
class TemplateOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magestore\Giftvoucher\Model\GiftTemplate
     */
    protected $_giftcardTemplate;

    /**
     * TemplateOptions constructor.
     * @param \Magestore\Giftvoucher\Model\GiftTemplate $giftcardTemplate
     */
    public function __construct(
        \Magestore\Giftvoucher\Model\GiftTemplate $giftcardTemplate
    ) {
        $this->_giftcardTemplate = $giftcardTemplate;
    }


    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $templates = $this->_giftcardTemplate->getCollection()
            ->addFieldToFilter('status', '1');
        $listTemplate = array();
        foreach ($templates as $template) {
            $listTemplate[] = array('label' => $template->getTemplateName(),
                'value' => $template->getId());
        }
        return $listTemplate;
    }

    /**
     * Get model option as array
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (is_null($this->_options)) {
            $this->_options = $this->getAvailableTemplate();
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => __('-- Please Select --'),
            ));
        }
        return $options;
    }

    /**
     * @return mixed
     */
    public function getDefaultData()
    {
        $templates = $this->getAvailableTemplate();
        return $templates[0]['value'];
    }
}
