<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Api\Data;

/**
 * Interface GiftCardOptionInterface
 * @api
 */
interface GiftCardOptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get gift card recipient name.
     *
     * @return string
     */
    public function getRecipientName();

    /**
     * Set gift card recipient name.
     *
     * @param string $value
     * @return $this
     */
    public function setRecipientName($value);
    /**
     * Get gift card recipient email.
     *
     * @return string
     */
    public function getRecipientEmail();

    /**
     * Set gift card recipient email.
     *
     * @param string $value
     * @return $this
     */
    public function setRecipientEmail($value);
    /**
     * Get gift card message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set gift card message.
     *
     * @param string $value
     * @return $this
     */
    public function setMessage($value);
    /**
     * Get gift card day to send.
     *
     * @return string
     */
    public function getDayToSend();

    /**
     * Set gift card day to send.
     *
     * @param string $value
     * @return $this
     */
    public function setDayToSend($value);
    /**
     * Get gift card time zone to send.
     *
     * @return string
     */
    public function getTimezoneToSend();

    /**
     * Set gift card time zone to send.
     *
     * @param string $value
     * @return $this
     */
    public function setTimezoneToSend($value);
    /**
     * Get gift card recipient address.
     *
     * @return string
     */
    public function getRecipientAddress();

    /**
     * Set gift card recipient address.
     *
     * @param string $value
     * @return $this
     */
    public function setRecipientAddress($value);
    /**
     * Get gift card notify success.
     *
     * @return int
     */
    public function getNotifySuccess();

    /**
     * Set gift card notify success.
     *
     * @param int $value
     * @return $this
     */
    public function setNotifySuccess($value);
    /**
     * Get gift card send friend.
     *
     * @return int
     */
    public function getSendFriend();

    /**
     * Set gift card send friend.
     *
     * @param int $value
     * @return $this
     */
    public function setSendFriend($value);
    /**
     * Get gift card template id.
     *
     * @return int
     */
    public function getGiftcardTemplateId();

    /**
     * Set gift card send friend.
     *
     * @param int $value
     * @return $this
     */
    public function setGiftcardTemplateId($value);

    /**
     * Get gift card customer name.
     *
     * @return string
     */
    public function getCustomerName();

    /**
     * Set gift card customer name.
     *
     * @param string $value
     * @return $this
     */
    public function setCustomerName($value);
    /**
     * Get gift card template image.
     *
     * @return string
     */
    public function getGiftcardTemplateImage();

    /**
     * Set gift card template image.
     *
     * @param string $value
     * @return $this
     */
    public function setGiftcardTemplateImage($value);
    /**
     * Get gift card custom template image.
     *
     * @return string
     */
    public function getGiftcardUseCustomImage();

    /**
     * Set gift card custom template image.
     *
     * @param string $value
     * @return $this
     */
    public function setGiftcardUseCustomImage($value);
    /**
     * Get amount.
     *
     * @return string
     */
    public function getAmount();

    /**
     * Set amount.
     *
     * @param string $value
     * @return $this
     */
    public function setAmount($value);
    /**
     * Get gift card amount.
     *
     * @return string
     */
    public function getGiftcardAmount();

    /**
     * Set gift card amount.
     *
     * @param string $value
     * @return $this
     */
    public function setGiftcardAmount($value);
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Giftvoucher\Api\Data\GiftCardOptionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftCardOptionExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Magestore\Giftvoucher\Api\Data\GiftCardOptionExtensionInterface $extensionAttributes
    );
}
