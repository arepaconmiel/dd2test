<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source\GiftTemplate;

/**
 * Class StatusOptions
 * @package Magestore\Giftvoucher\Model\Source\GiftTemplate
 */
class StatusOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     *
     */
    const STATUS_ENABLE = 1;
    /**
     *
     */
    const STATUS_DISABLE = 2;

    /**
     * Get the gift card's type
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::STATUS_ENABLE => __('Enable'),
            self::STATUS_DISABLE => __('Disable'),
        ];
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
