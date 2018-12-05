<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Giftcard;

use Magestore\Giftvoucher\Api\Data\GiftCardOptionInterface;

/**
 * Gift Card Option Model
 * @codeCoverageIgnore
 */
class Option extends \Magento\Framework\Model\AbstractExtensibleModel implements GiftCardOptionInterface
{
    /**#@+
     * Constants
     */
    const KEY_RECIPIENT_NAME = 'recipient_name';
    const KEY_RECIPIENT_EMAIL = 'recipient_email';
    const KEY_MESSAGE = 'message';
    const KEY_DAY_TO_SEND = 'day_to_send';
    const KEY_TIMEZONE_TO_SEND = 'timezone_to_send';
    const KEY_RECIPIENT_ADDRESS = 'recipient_address';
    const KEY_NOTIFY_SUCCESS = 'notify_success';
    const KEY_SEND_FRIEND = 'send_friend';
    const KEY_GIFTCARD_TEMPLATE_ID = 'giftcard_template_id';
    const KEY_CUSTOMER_NAME = 'customer_name';
    const KEY_GIFTCARD_TEMPLATE_IMAGE = 'giftcard_template_image';
    const KEY_GIFTCARD_CUSTOM_IMAGE = 'giftcard_use_custom_image';
    const KEY_AMOUNT = 'amount';
    const KEY_GIFTCARD_AMOUNT = 'giftcard_amount';

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function getRecipientName(){
        return $this->getData(self::KEY_RECIPIENT_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientName($value){
        return $this->setData(self::KEY_RECIPIENT_NAME, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getRecipientEmail(){
        return $this->getData(self::KEY_RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientEmail($value){
        return $this->setData(self::KEY_RECIPIENT_EMAIL, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getMessage(){
        return $this->getData(self::KEY_MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($value){
        return $this->setData(self::KEY_MESSAGE, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getDayToSend(){
        return $this->getData(self::KEY_DAY_TO_SEND);
    }

    /**
     * {@inheritdoc}
     */
    public function setDayToSend($value){
        return $this->setData(self::KEY_DAY_TO_SEND, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getTimezoneToSend(){
        return $this->getData(self::KEY_TIMEZONE_TO_SEND);
    }

    /**
     * {@inheritdoc}
     */
    public function setTimezoneToSend($value){
        return $this->setData(self::KEY_TIMEZONE_TO_SEND, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getRecipientAddress(){
        return $this->getData(self::KEY_RECIPIENT_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientAddress($value){
        return $this->setData(self::KEY_RECIPIENT_ADDRESS, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getNotifySuccess(){
        return $this->getData(self::KEY_NOTIFY_SUCCESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setNotifySuccess($value){
        return $this->setData(self::KEY_NOTIFY_SUCCESS, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getSendFriend(){
        return $this->getData(self::KEY_NOTIFY_SUCCESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSendFriend($value){
        return $this->setData(self::KEY_SEND_FRIEND, $value);
    }
    /**
     * {@inheritdoc}
     */

    public function getGiftcardTemplateId(){
        return $this->getData(self::KEY_GIFTCARD_TEMPLATE_ID);
    }

    /**
     * {@inheritdoc}
     */

    public function setGiftcardTemplateId($value){
        return $this->setData(self::KEY_GIFTCARD_TEMPLATE_ID, $value);
    }
    /**
     * {@inheritdoc}
     */

    public function getCustomerName(){
        return $this->getData(self::KEY_CUSTOMER_NAME);
    }

    /**
     * {@inheritdoc}
     */

    public function setCustomerName($value){
        return $this->setData(self::KEY_CUSTOMER_NAME, $value);
    }
    /**
     * {@inheritdoc}
     */

    public function getGiftcardTemplateImage(){
        return $this->getData(self::KEY_GIFTCARD_TEMPLATE_IMAGE);
    }

    /**
     * {@inheritdoc}
     */

    public function setGiftcardTemplateImage($value){
        return $this->setData(self::KEY_GIFTCARD_TEMPLATE_IMAGE, $value);
    }

    /**
     * {@inheritdoc}
     */

    public function getGiftcardUseCustomImage(){
        return $this->getData(self::KEY_GIFTCARD_CUSTOM_IMAGE);
    }

    /**
     * {@inheritdoc}
     */

    public function setGiftcardUseCustomImage($value){
        return $this->setData(self::KEY_GIFTCARD_CUSTOM_IMAGE, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getData(self::KEY_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setAmount($value)
    {
        return $this->setData(self::KEY_AMOUNT, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getGiftcardAmount()
    {
        return $this->getData(self::KEY_GIFTCARD_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftcardAmount($value)
    {
        return $this->setData(self::KEY_GIFTCARD_AMOUNT, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Magestore\Giftvoucher\Api\Data\GiftCardOptionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
