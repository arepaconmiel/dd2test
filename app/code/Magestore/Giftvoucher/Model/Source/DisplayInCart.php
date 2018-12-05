<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Source;

/**
 * Giftvoucher Displayincart Model
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class DisplayInCart implements \Magento\Framework\Option\ArrayInterface
{
    
    /**
     * Get model option as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $positions = array(
            'amount' => __('Gift Card value'),
            'giftcard_template_id' => __('Gift Card template'),
            'customer_name' => __('Sender name'),
            'recipient_name' => __('Recipient name'),
            'recipient_email' => __('Recipient email address'),
            'recipient_ship' => __('Ship to recipient'),
            'recipient_address' => __('Recipient address'),
            'message' => __('Custom message'),
            'day_to_send' => __('Day to send'),
            'timezone_to_send' => __('Time zone'),
        );
        $options = array();

        foreach ($positions as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }
        return $options;
    }
}
