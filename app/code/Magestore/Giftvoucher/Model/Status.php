<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model;

/**
 * Giftvoucher Status Model
 */
class Status extends \Magento\Framework\DataObject implements \Magento\Framework\Data\OptionSourceInterface
{

    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DISABLED = 3;
    const STATUS_USED = 4;
    const STATUS_EXPIRED = 5;
    const STATUS_DELETED = 6;
    const STATUS_REFUNDED = 7;
    const STATUS_NOT_SEND = 0;
    const STATUS_SENT_EMAIL = 1;
    const STATUS_SENT_OFFICE = 2;

    /**
     * Get the gift code's status options as array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        return array(
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_DISABLED => __('Disabled'),
            self::STATUS_USED => __('Used'),
            self::STATUS_EXPIRED => __('Expired'),
            self::STATUS_REFUNDED => __('Refunded'),
        );
    }

    /**
     * Get the email's status options as array
     *
     * @return array
     */
    public static function getOptionEmail()
    {
        return array(
            self::STATUS_NOT_SEND => __('Not Send'),
            self::STATUS_SENT_EMAIL => __('Sent via Email'),
            self::STATUS_SENT_OFFICE => __('Send via Post Office'),
        );
    }

    /**
     * Get the gift code's status options
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
     * Email options
     *
     * @return array
     */
    public static function getEmailOptions()
    {
        $options = array();
        foreach (self::getOptionEmail() as $value => $label) {
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
