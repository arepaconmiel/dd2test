<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source;

/**
 * Giftvoucher Aftertax Model
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class AfterTax extends \Magento\Framework\DataObject
{
    
    /**
     * Get model option as array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return array(
            0 => 'Before tax',
            1 => 'After tax',
        );
    }

    /**
     * @return array
     */
    public function toOptionArray()
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
}
