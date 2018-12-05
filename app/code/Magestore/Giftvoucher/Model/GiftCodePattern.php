<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model;

use Magestore\Giftvoucher\Api\Data\GiftCodePatternInterface;

/**
 * Class GiftCodePattern
 * @package Magestore\Giftvoucher\Model
 */
class GiftCodePattern extends \Magento\Rule\Model\AbstractModel implements GiftCodePatternInterface
{
    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    protected $_conditionsInstance;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    protected $_actionsInstance;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $conditionsInstance
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $actionsInstance
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $conditionsInstance,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $actionsInstance,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
            $this->_conditionsInstance = $conditionsInstance;
            $this->_actionsInstance = $actionsInstance;
            parent::__construct(
                $context,
                $registry,
                $formFactory,
                $localeDate,
                $resource,
                $resourceCollection,
                $data
            );
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern');
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    public function getConditionsInstance()
    {
        return $this->_conditionsInstance->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    public function getActionsInstance()
    {
        return $this->_actionsInstance->create();
    }

    /**
     * Initialize rule model data from array
     *
     * @param array $rule
     * @return $this
     * @internal param array $data
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions([])->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions([])->loadArray($arr['actions'][1], 'actions');
        }

        return $this;
    }

    /**
     * Duplicate Gift Code Pattern
     *
     * @return NULL|\Magestore\Giftvoucher\Model\GiftCodePattern
     */
    public function duplicate()
    {
        if (!$this->getId()) {
            return null;
        }
        $newModel = clone $this;
        return $newModel->setData('template_id', null)
            ->setData('is_generated', 0)
            ->save();
    }

    /**
     * Get Template Id
     *
     * @return int|null
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Get Template Name
     *
     * @return string|null
     */
    public function getTemplateName()
    {
        return $this->getData(self::TEMPLATE_NAME);
    }

    /**
     * Get Pattern
     *
     * @return string|null
     */
    public function getPattern()
    {
        return $this->getData(self::PATTERN);
    }

    /**
     * Get Balance
     *
     * @return number|null
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * Get Currency
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * Get Expired At
     *
     * @return date|null
     */
    public function getExpiredAt()
    {
        return $this->getData(self::EXPIRED_AT);
    }

    /**
     * Get Amount
     *
     * @return number|null
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * Get Day To Send
     *
     * @return number|null
     */
    public function getDayToSend()
    {
        return $this->getData(self::DAY_TO_SEND);
    }

    /**
     * Get Store Id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get Conditions Serialized
     *
     * @return string|null
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * Get Is Generated
     *
     * @return int|null
     */
    public function getIsGenerated()
    {
        return $this->getData(self::IS_GENERATED);
    }

    /**
     * Get Giftcard Template Id
     *
     * @return int|null
     */
    public function getGiftcardTemplateId()
    {
        return $this->getData(self::GIFTCARD_TEMPLATE_ID);
    }

    /**
     * Get Giftcard Template Image
     *
     * @return string|null
     */
    public function getGiftcardTemplateImage()
    {
        return $this->getData(self::GIFTCARD_TEMPLATE_IMAGE);
    }

    /**
     * Set Template Id
     *
     * @param int $templateId
     * @return GiftCodePatternInterface
     */
    public function setTemplateId($templateId)
    {
        return $this->setData(self::TEMPLATE_ID, $templateId);
    }

    /**
     * Set Type
     *
     * @param string $type
     * @return GiftCodePatternInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Set Template Name
     *
     * @param string $templateName
     * @return GiftCodePatternInterface
     */
    public function setTemplateName($templateName)
    {
        return $this->setData(self::TEMPLATE_NAME, $templateName);
    }

    /**
     * Set Pattern
     *
     * @param string $pattern
     * @return GiftCodePatternInterface
     */
    public function setPattern($pattern)
    {
        return $this->setData(self::PATTERN, $pattern);
    }

    /**
     * Set Balance
     *
     * @param number $balance
     * @return GiftCodePatternInterface
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * Set Currency
     *
     * @param string $currency
     * @return GiftCodePatternInterface
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * Set Expired At
     *
     * @param date $expiredAt
     * @return GiftCodePatternInterface
     */
    public function setExpiredAt($expiredAt)
    {
        return $this->setData(self::EXPIRED_AT, $expiredAt);
    }

    /**
     * Set Amount
     *
     * @param number $amount
     * @return GiftCodePatternInterface
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Set Day To Send
     *
     * @param number $dayToSend
     * @return GiftCodePatternInterface
     */
    public function setDayToSend($dayToSend)
    {
        return $this->setData(self::DAY_TO_SEND, $dayToSend);
    }

    /**
     * Set Store Id
     *
     * @param int $storeId
     * @return GiftCodePatternInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set Conditions Serialized
     *
     * @param string $conditionsSerialized
     * @return GiftCodePatternInterface
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * Set Is Generated
     *
     * @param int $isGenerated
     * @return GiftCodePatternInterface
     */
    public function setIsGenerated($isGenerated)
    {
        return $this->setData(self::IS_GENERATED, $isGenerated);
    }

    /**
     * Set Giftcard Template Id
     *
     * @param int $giftcardTemplateId
     * @return GiftCodePatternInterface
     */
    public function setGiftcardTemplateId($giftcardTemplateId)
    {
        return $this->setData(self::GIFTCARD_TEMPLATE_ID, $giftcardTemplateId);
    }

    /**
     * Set Giftcard Template Image
     *
     * @param string $giftcardTemplateImage
     * @return GiftCodePatternInterface
     */
    public function setGiftcardTemplateImage($giftcardTemplateImage)
    {
        return $this->setData(self::GIFTCARD_TEMPLATE_IMAGE, $giftcardTemplateImage);
    }
}
