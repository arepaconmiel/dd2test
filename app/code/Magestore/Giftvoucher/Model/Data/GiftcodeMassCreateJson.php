<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Data;

/**
 * Giftvoucher Actions Model
 */
class GiftcodeMassCreateJson extends \Magento\Framework\DataObject implements \Magestore\Giftvoucher\Api\Data\GiftcodeMassCreateJsonInterface
{
    /**
     * Set is generated
     * @param string $isGenerated
     * @return $this
     */
    public function setIsGenerated($isGenerated)
    {
        $this->setData(self::IS_GENERATED, $isGenerated);
        return $this;
    }

    /**
     * Get is generated
     *
     * @return string
     */
    public function getIsGenerated()
    {
        return $this->getData(self::IS_GENERATED);
    }
    /**
     * Set condition
     * @param string $conditionSerialized
     * @return $this
     */
    public function setConditionSerialized($conditionSerialized)
    {
        $this->setData(self::CONDITION_SERIALIZED, $conditionSerialized);
        return $this;
    }

    /**
     * Get condition
     *
     * @return string
     */
    public function getConditionSerialized()
    {
        return $this->getData(self::CONDITION_SERIALIZED);
    }
    /**
     * Set store id
     * @param string $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, $storeId);
        return $this;
    }

    /**
     * Get store id
     *
     * @return string
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }
    /**
     * Set day to send
     * @param string $dayToSend
     * @return $this
     */
    public function setDayToSend($dayToSend)
    {
        $this->setData(self::DAY_TO_SEND, $dayToSend);
        return $this;
    }

    /**
     * Get day to send
     *
     * @return string
     */
    public function getDayToSend()
    {
        return $this->getData(self::DAY_TO_SEND);
    }
    /**
     * Set Amount
     * @param string $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->setData(self::AMOUNT, $amount);
        return $this;
    }

    /**
     * Get Amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * Set Balance
     * @param $balance
     * @return $this
     * @internal param string $type
     */
    public function setBalance($balance)
    {
        $this->setData(self::BALANCE, $balance);
        return $this;
    }

    /**
     * Get Balance
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }
    /**
     * Set Type
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);
        return $this;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }
    /**
     * Set Pattern
     * @param string $pattern
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->setData(self::PATTERN, $pattern);
        return $this;
    }
    /**
     * Get Pattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->getData(self::PATTERN);
    }
    /**
     * Set Template name
     * @param string $templateName
     * @return $this
     */
    public function setTemplateName($templateName)
    {
        $this->setData(self::TEMPLATE_NAME, $templateName);
        return $this;
    }

    /**
     * Get Template name
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->getData(self::TEMPLATE_NAME);
    }

    /**
     * Set currency
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->setData(self::CURRENCY, $currency);
        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * Set expired at
     * @param $expiredAt
     * @return $this
     * @internal param string $currency
     */
    public function setExpiredAt($expiredAt)
    {
        $this->setData(self::EXPIRED_AT, $expiredAt);
        return $this;
    }

    /**
     * Get expired at
     *
     * @return string
     */
    public function getExpiredAt()
    {
        return $this->getData(self::EXPIRED_AT);
    }

    /**
     * Set status
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }
}
