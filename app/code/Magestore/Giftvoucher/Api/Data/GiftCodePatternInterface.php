<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

/**
 * Gift Code Pattern interface.
 * @api
 */
interface GiftCodePatternInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const TEMPLATE_ID = 'template_id';
    const TYPE = 'type';
    const TEMPLATE_NAME = 'template_name';
    const PATTERN = 'pattern';
    const BALANCE = 'balance';
    const CURRENCY = 'currency';
    const EXPIRED_AT = 'expired_at';
    const AMOUNT = 'amount';
    const DAY_TO_SEND = 'day_to_send';
    const STORE_ID = 'store_id';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const IS_GENERATED = 'is_generated';
    const GIFTCARD_TEMPLATE_ID = 'giftcard_template_id';
    const GIFTCARD_TEMPLATE_IMAGE = 'giftcard_template_image';
    /**#@-*/

    /**
     * Get Template Id
     *
     * @return int|null
     */
    public function getTemplateId();

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType();
    /**
     * Get Template Name
     *
     * @return string|null
     */
    public function getTemplateName();

    /**
     * Get Pattern
     *
     * @return string|null
     */
    public function getPattern();

    /**
     * Get Balance
     *
     * @return number|null
     */
    public function getBalance();

    /**
     * Get Currency
     *
     * @return string|null
     */
    public function getCurrency();

    /**
     * Get Expired At
     *
     * @return date|null
     */
    public function getExpiredAt();

    /**
     * Get Amount
     *
     * @return number|null
     */
    public function getAmount();

    /**
     * Get Day To Send
     *
     * @return number|null
     */
    public function getDayToSend();

    /**
     * Get Store Id
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Get Conditions Serialized
     *
     * @return string|null
     */
    public function getConditionsSerialized();

    /**
     * Get Is Generated
     *
     * @return int|null
     */
    public function getIsGenerated();

    /**
     * Get Giftcard Template Id
     *
     * @return int|null
     */
    public function getGiftcardTemplateId();

    /**
     * Get Giftcard Template Image
     *
     * @return string|null
     */
    public function getGiftcardTemplateImage();

    /**
     * Set Template Id
     *
     * @param int $templateId
     * @return GiftCodePatternInterface
     */
    public function setTemplateId($templateId);

    /**
     * Set Type
     *
     * @param string $type
     * @return GiftCodePatternInterface
     */
    public function setType($type);

    /**
     * Set Template Name
     *
     * @param string $templateName
     * @return GiftCodePatternInterface
     */
    public function setTemplateName($templateName);

    /**
     * Set Pattern
     *
     * @param string $pattern
     * @return GiftCodePatternInterface
     */
    public function setPattern($pattern);

    /**
     * Set Balance
     *
     * @param number $balance
     * @return GiftCodePatternInterface
     */
    public function setBalance($balance);

    /**
     * Set Currency
     *
     * @param string $currency
     * @return GiftCodePatternInterface
     */
    public function setCurrency($currency);

    /**
     * Set Expired At
     *
     * @param date $expiredAt
     * @return GiftCodePatternInterface
     */
    public function setExpiredAt($expiredAt);

    /**
     * Set Amount
     *
     * @param number $amount
     * @return GiftCodePatternInterface
     */
    public function setAmount($amount);

    /**
     * Set Day To Send
     *
     * @param number $dayToSend
     * @return GiftCodePatternInterface
     */
    public function setDayToSend($dayToSend);

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return GiftCodePatternInterface
     */
    public function setStoreId($storeId);

    /**
     * Set Conditions Serialized
     *
     * @param string $conditionsSerialized
     * @return GiftCodePatternInterface
     */
    public function setConditionsSerialized($conditionsSerialized);

    /**
     * Set Is Generated
     *
     * @param int $isGenerated
     * @return GiftCodePatternInterface
     */
    public function setIsGenerated($isGenerated);

    /**
     * Set Giftcard Template Id
     *
     * @param int $giftcardTemplateId
     * @return GiftCodePatternInterface
     */
    public function setGiftcardTemplateId($giftcardTemplateId);

    /**
     * Set Giftcard Template Image
     *
     * @param string $giftcardTemplateImage
     * @return GiftCodePatternInterface
     */
    public function setGiftcardTemplateImage($giftcardTemplateImage);
}
