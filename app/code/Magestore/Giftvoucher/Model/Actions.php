<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model;

/**
 * Giftvoucher Actions Model
 */
class Actions extends \Magento\Framework\DataObject
{

    const ACTIONS_CREATE = 1;
    const ACTIONS_UPDATE = 2;
    const ACTIONS_MASS_UPDATE = 3;
    const ACTIONS_EMAIL = 4;
    const ACTIONS_SPEND_ORDER = 5;
    const ACTIONS_REFUND = 6;
    const ACTIONS_REDEEM = 7;
    const ACTIONS_CANCEL = 8;

    /**
     * Get model option as array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return array(
            self::ACTIONS_CREATE => __('Create'),
            self::ACTIONS_UPDATE => __('Update'),
            self::ACTIONS_MASS_UPDATE => __('Mass update'),
            self::ACTIONS_SPEND_ORDER => __('Spent on order'),
            self::ACTIONS_REFUND => __('Refund'),
            self::ACTIONS_REDEEM => __('Redeem'),
            self::ACTIONS_CANCEL => __('Cancel'),
        );
    }

    /**
     *
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
     *
     * @param int $actionId
     * @return string
     * @throws \Exception
     */
    public static function getActionLabel($actionId)
    {
        $optionArray = self::getOptionArray();
        if (isset($optionArray[$actionId])) {
            return $optionArray[$actionId];
        }
        throw new \Exception(__('There is no available gift card history action'));
    }
}
