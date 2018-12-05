<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source\Giftvoucher;

/**
 * Class TemplatesOptions
 * @package Magestore\Giftvoucher\Model\Source\Giftvoucher
 */
class TemplatesOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\Collection
     */
    private $templates;

    /**
     * @param \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\Collection $templates
     */
    public function __construct(\Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\Collection $templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->templates as $template) {
            $options[] = [
                'value' => $template->getId(),
                'label' => $template->getTemplateName(),
            ];
        }
        return $options;
    }
}
