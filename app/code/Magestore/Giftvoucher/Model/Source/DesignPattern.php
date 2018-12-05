<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Source;

/**
 * Class Designpattern
 * @package Magestore\Giftvoucher\Model\Source
 */
class DesignPattern extends \Magento\Framework\DataObject
{
    /**
     *
     */
    const PATTERN_LEFT = 1;
    /**
     *
     */
    const PATTERN_TOP = 2;
    /**
     *
     */
    const PATTERN_CENTER = 3;

    /**
     * Get model option as array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return array(
            self::PATTERN_LEFT => __('Left'),
            self::PATTERN_TOP => __('Top'),
            self::PATTERN_CENTER => __('Center'),
        );
    }

    /**
     * @return array
     */
    public static function getOptions()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return self::getOptions();
    }
}
