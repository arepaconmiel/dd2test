<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

/**
 * CMS block interface.
 * @api
 */
interface GiftvoucherInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const GIFTVOUCHER_ID = 'giftvoucher_id';
    const GIFT_CODE = 'gift_code';
    const BALANCE = 'balance';
    const CURRENCY = 'currency';
    const STATUS = 'status';
    const EXPIRED_AT = 'expired_at';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const RECIPIENT_NAME = 'recipient_name';
    const RECIPIENT_EMAIL = 'recipient_email';
    const RECIPIENT_ADDRESS = 'recipient_address';
    const MESSAGE = 'message';
    const STORE_ID = 'store_id';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const DAY_TO_SEND = 'day_to_send';
    const IS_SENT = 'is_sent';
    const SHIPPED_TO_CUSTOMER = 'shipped_to_customer';
    const CREATED_FORM = 'created_form';
    const TEMPLATE_ID = 'template_id';
    const DESCRIPTION = 'description';
    const GIFTVOUCHER_CONMENTS = 'giftvoucher_comments';
    const EMAIL_SENDER = 'email_sender';
    const NOTIFY_SUCCESS = 'notify_success';
    const GIFTCARD_CUSTOM_IMAGE = 'giftcard_custom_image';
    const GIFTCARD_TEMPLATE_ID = 'giftcard_template_id';
    const GIFTCARD_TEMPLATE_IMAGE = 'giftcard_template_image';
    const ACTIONS_SERIALIZED = 'actions_serialized';
    const TIMEZONE_TO_SEND = 'timezone_to_send';
    const DAY_STORE = 'day_store';
    const USED = 'used';
    const SET_ID = 'set_id';
    /**#@-*/


    /**
     * Get ID
     *
     * @return int|null
     */
    public function getGiftvoucherId();

    /**
     * Set ID
     *
     * @param int $giftvoucherId
     * @return GiftvoucherInterface
     */
    public function setGiftvoucherId($giftvoucherId);

    /**
     * Get Gift code
     *
     * @return string|null
     */
    public function getGiftCode();

    /**
     * Set Gift code
     *
     * @param string $giftCode
     * @return GiftvoucherInterface
     */
    public function setGiftCode($giftCode);

    /**
     * Get Gift code balance
     *
     * @return string|null
     */
    public function getBalance();

    /**
     * Set Gift code balance
     *
     * @param string $balance
     * @return GiftvoucherInterface
     */
    public function setBalance($balance);

    /**
     * Get Gift code currency
     *
     * @return string|null
     */
    public function getCurrency();

    /**
     * Set Gift code currency
     *
     * @param string $currency
     * @return GiftvoucherInterface
     */
    public function setCurrency($currency);

    /**
     * Get Gift code status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Gift code status
     *
     * @param int $status
     * @return GiftvoucherInterface
     */
    public function setStatus($status);

    /**
     * Get Gift code status
     *
     * @return string|null
     */
    public function getExpiredAt();

    /**
     * Set Gift code $expiredAt
     *
     * @param string $expiredAt
     * @return GiftvoucherInterface
     */
    public function setExpiredAt($expiredAt);

    /**
     * Get Gift code $customerId
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set Gift code $customerId
     *
     * @param int $customerId
     * @return GiftvoucherInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get Gift code $customerName
     *
     * @return string|null
     */
    public function getCustomerName();

    /**
     * Set Gift code $customerName
     *
     * @param string $customerName
     * @return GiftvoucherInterface
     */
    public function setCustomerName($customerName);

    /**
     * Get Gift code $customerEmail
     *
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set Gift code $customerEmail
     *
     * @param $customerEmail
     * @return GiftvoucherInterface
     * @internal param string $customerName
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Get Gift code $recipientName
     *
     * @return string|null
     */
    public function getRecipientName();

    /**
     * Set Gift code $recipientName
     *
     * @param string $recipientName
     * @return GiftvoucherInterface
     */
    public function setRecipientName($recipientName);

    /**
     * Get Gift code $customerEmail
     *
     * @return string|null
     */
    public function getRecipientEmail();

    /**
     * Set Gift code $recipientEmail
     *
     * @param string $recipientEmail
     * @return GiftvoucherInterface
     */
    public function setRecipientEmail($recipientEmail);

    /**
     * Get Gift code $recipientAddress
     *
     * @return string|null
     */
    public function getRecipientAddress();

    /**
     * Set Gift code $recipientAddress
     *
     * @param string $recipientAddress
     * @return GiftvoucherInterface
     */
    public function setRecipientAddress($recipientAddress);

    /**
     * Get Gift code $message
     *
     * @return string|null
     */
    public function getMessage();

    /**
     * Set Gift code $message
     *
     * @param string $message
     * @return GiftvoucherInterface
     */
    public function setMessage($message);

    /**
     * Get Gift code $storeId
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set Gift code $storeId
     *
     * @param int $storeId
     * @return GiftvoucherInterface
     */
    public function setStoreId($storeId);

    /**
     * Get Gift code $conditionsSerialized
     *
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * Set Gift code $conditionsSerialized
     *
     * @param string $conditionsSerialized
     * @return GiftvoucherInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * Get Gift code $dayToSend
     *
     * @return string|null
     */
    public function getDayToSend();

    /**
     * Set Gift code $dayToSend
     *
     * @param string $dayToSend
     * @return GiftvoucherInterface
     */
    public function setDayToSend($dayToSend);

    /**
     * Get Gift code $isSent
     *
     * @return string|null
     */
    public function getIsSent();

    /**
     * Set Gift code $isSent
     *
     * @param string $isSent
     * @return GiftvoucherInterface
     */
    public function setIsSent($isSent);

    /**
     * Get Gift code $shippedToCustomer
     *
     * @return int|null
     */
    public function getShippedToCustomer();

    /**
     * Set Gift code $shippedToCustomer
     *
     * @param int $shippedToCustomer
     * @return GiftvoucherInterface
     */
    public function setShippedToCustomer($shippedToCustomer);

    /**
     * Get Gift code $createdForm
     *
     * @return string|null
     */
    public function getCreatedForm();

    /**
     * Set Gift code $createdForm
     *
     * @param string $createdForm
     * @return GiftvoucherInterface
     */
    public function setCreatedForm($createdForm);

    /**
     * Get Gift code $templateId
     *
     * @return int|null
     */
    public function getTemplateId();

    /**
     * Set Gift code $templateId
     *
     * @param int $templateId
     * @return GiftvoucherInterface
     */
    public function setTemplateId($templateId);

    /**
     * Get Gift code $description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Set Gift code $description
     *
     * @param string $description
     * @return GiftvoucherInterface
     */
    public function setDescription($description);

    /**
     * Get Gift code $giftvoucherComments
     *
     * @return string|null
     */
    public function getGiftvoucherComments();

    /**
     * Set Gift code $giftvoucherComments
     *
     * @param string $giftvoucherComments
     * @return GiftvoucherInterface
     */
    public function setGiftvoucherComments($giftvoucherComments);

    /**
     * Get Gift code $emailSender
     *
     * @return int|null
     */
    public function getEmailSender();

    /**
     * Set Gift code $emailSender
     *
     * @param int $emailSender
     * @return GiftvoucherInterface
     */
    public function setEmailSender($emailSender);

    /**
     * Get Gift code $notifySuccess
     *
     * @return int|null
     */
    public function getNotifySuccess();

    /**
     * Set Gift code $notifySuccess
     *
     * @param int $notifySuccess
     * @return GiftvoucherInterface
     */
    public function setNotifySuccess($notifySuccess);
    /**
     * Get Gift code $giftcardCustomImage
     *
     * @return int|null
     */
    public function getGiftcardCustomImage();

    /**
     * Set Gift code $giftcardCustomImage
     *
     * @param int $giftcardCustomImage
     * @return GiftvoucherInterface
     */
    public function setGiftcardCustomImage($giftcardCustomImage);
    /**
     * Get Gift code $giftcardTemplateId
     *
     * @return int|null
     */
    public function getGiftcardTemplateId();

    /**
     * Set Gift code $giftcardTemplateId
     *
     * @param int $giftcardTemplateId
     * @return GiftvoucherInterface
     */
    public function setGiftcardTemplateId($giftcardTemplateId);

    /**
     * Get Gift code $giftcardTemplateImage
     *
     * @return string|null
     */
    public function getGiftcardTemplateImage();

    /**
     * Set Gift code $giftcardTemplateImage
     *
     * @param string $giftcardTemplateImage
     * @return GiftvoucherInterface
     */
    public function setGiftcardTemplateImage($giftcardTemplateImage);

    /**
     * Get Gift code $actionsSerialized
     *
     * @return string|null
     */
    public function getActionsSerialized();

    /**
     * Set Gift code $actionsSerialized
     *
     * @param string $actionsSerialized
     * @return GiftvoucherInterface
     */
    public function setActionsSerialized($actionsSerialized);

    /**
     * Get Gift code $timezoneToSend
     *
     * @return string|null
     */
    public function getTimezoneToSend();

    /**
     * Set Gift code $timezoneToSend
     *
     * @param string $timezoneToSend
     * @return GiftvoucherInterface
     */
    public function setTimezoneToSend($timezoneToSend);

    /**
     * Get Gift code $dayStore
     *
     * @return string|null
     */
    public function getDayStore();

    /**
     * Set Gift code $dayStore
     *
     * @param string $dayStore
     * @return GiftvoucherInterface
     */
    public function setDayStore($dayStore);

    /**
     * Get Gift code $used
     *
     * @return int|null
     */
    public function getUsed();

    /**
     * Set Gift code $used
     *
     * @param int $used
     * @return GiftvoucherInterface
     */
    public function setUsed($used);

    /**
     * Get Gift code $setId
     *
     * @return int|null
     */
    public function getSetId();

    /**
     * Set Gift code $setId
     *
     * @param int $setId
     * @return GiftvoucherInterface
     */
    public function setSetId($setId);
}
