<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

/**
 * Interface GiftcodeSendEmailJsonInterface
 * @package Magestore\Giftvoucher\Api\Data
 */
interface GiftcodeMassCreateJsonInterface
{
    const TEMPLATE_NAME = 'template_name';
    const CURRENCY = 'currency';
    const EXPIRED_AT = 'expired_at';
    const STATUS = 'status';
    const PATTERN = 'pattern';
    const TYPE = 'type';
    const BALANCE = 'balance';
    const AMOUNT = 'amount';
    const DAY_TO_SEND = 'day_to_send';
    const STORE_ID = 'store_id';
    const CONDITION_SERIALIZED = 'conditions_serialized';
    const IS_GENERATED = 'is_generated';
    /**
     * Set data
     * @param mixed $data
     * @return $this
     */
    public function setData($data);

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData();
    /**
     * Set is generated
     * @param string $isGenerated
     * @return $this
     */
    public function setIsGenerated($isGenerated);

    /**
     * Get is generated
     *
     * @return string
     */
    public function getIsGenerated();
    /**
     * Set condition
     * @param string $conditionSerialized
     * @return $this
     */
    public function setConditionSerialized($conditionSerialized);

    /**
     * Get condition
     *
     * @return string
     */
    public function getConditionSerialized();
    /**
     * Set store id
     * @param string $storeId
     * @return $this
     */
    public function setStoreId($storeId);

    /**
     * Get store id
     *
     * @return string
     */
    public function getStoreId();
    /**
     * Set day to send
     * @param string $dayToSend
     * @return $this
     */
    public function setDayToSend($dayToSend);

    /**
     * Get day to send
     *
     * @return string
     */
    public function getDayToSend();
    /**
     * Set Amount
     * @param string $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Get Amount
     *
     * @return string
     */
    public function getAmount();

    /**
     * Set Balance
     * @param $balance
     * @return $this
     * @internal param string $type
     */
    public function setBalance($balance);

    /**
     * Get Balance
     *
     * @return string
     */
    public function getBalance();
    /**
     * Set Type
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get Type
     *
     * @return string
     */
    public function getType();
    /**
     * Set Pattern
     * @param string $pattern
     * @return $this
     */
    public function setPattern($pattern);

    /**
     * Get Pattern
     *
     * @return string
     */
    public function getPattern();
    /**
     * Set Template name
     * @param string $templateName
     * @return $this
     */
    public function setTemplateName($templateName);

    /**
     * Get Template name
     *
     * @return string
     */
    public function getTemplateName();

    /**
     * Set currency
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set expired at
     * @param $expiredAt
     * @return $this
     * @internal param string $currency
     */
    public function setExpiredAt($expiredAt);

    /**
     * Get expired at
     *
     * @return string
     */
    public function getExpiredAt();

    /**
     * Set status
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();
}
