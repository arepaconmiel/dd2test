<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source;

/**
 * Class GiftCardTypeOptions
 * @package Magestore\Giftvoucher\Model\Source
 */
class GiftCardTypeOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     *
     */
    const TYPE_PHYSICAL = 1;
    /**
     *
     */
    const TYPE_VIRTUAL = 2;
    /**
     *
     */
    const TYPE_COMBINE= 3;
    /**
     * Get the gift card's type
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return array(
            self::TYPE_PHYSICAL => __('Physical'),
            self::TYPE_VIRTUAL => __('Virtual'),
            self::TYPE_COMBINE=> __('Combine'),
        );
    }

    /**
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => __('Physical'),
                    'value' => self::TYPE_PHYSICAL
                ),
                array(
                    'label' => __('Virtual'),
                    'value' => self::TYPE_VIRTUAL
                ),
                array(
                    'label' => __('Combine'),
                    'value' => self::TYPE_COMBINE
                ),
            );
        }
        if ($withEmpty) {
            array_unshift($this->_options, array(
                'value' => '',
                'label' => __('-- Please Select --'),
            ));
        }
        return $this->_options;
    }
}
