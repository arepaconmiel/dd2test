<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model;

/**
 * Class GiftTemplate
 * @package Magestore\Giftvoucher\Model
 */
class GiftTemplate extends \Magento\Framework\Model\AbstractModel implements \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate');
    }
    
    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->_getData(self::GIFTCARD_TEMPLATE_ID);
    }

    /**
     * Get giftcard_template_id
     *
     * @return string
     */
    public function getGiftcardTemplateId()
    {
        return $this->_getData(self::GIFTCARD_TEMPLATE_ID);
    }

    /**
     * Get template_name
     *
     * @return string|null
     */
    public function getTemplateName()
    {
        return $this->_getData(self::TEMPLATE_NAME);
    }

    /**
     * Get notes
     *
     * @return string|null
     */
    public function getNotes()
    {
        return $this->_getData(self::NOTES);
    }

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getStatus()
    {
        return $this->_getData(self::STATUS);
    }
    
    /**
     *
     * @return string|null
     */
    public function getTextColor()
    {
        return $this->_getData(self::TEXT_COLOR);
    }
    
    /**
     *
     * @return string|null
     */
    public function getStyleColor()
    {
        return $this->_getData(self::STYLE_COLOR);
    }
    
    /**
     *
     * @return string|null
     */
    public function getDesignPattern()
    {
        return $this->_getData(self::DESIGN_PATTERN);
    }

    /**
     *
     * @return string|null
     */
    public function getImages()
    {
        return $this->_getData(self::IMAGES);
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return GiftTemplateInterface
     */
    public function setId($id)
    {
        return $this->setData(self::GIFTCARD_TEMPLATE_ID, $id);
    }

    /**
     * Set Id
     *
     * @param string $id
     * @return GiftTemplateInterface
     */
    public function setGiftcardTemplateId($id)
    {
        return $this->setData(self::GIFTCARD_TEMPLATE_ID, $id);
    }

    /**
     * Set Template Name
     *
     * @param string $templateName
     * @return GiftTemplateInterface
     */
    public function setTemplateName($templateName)
    {
        return $this->setData(self::TEMPLATE_NAME, $templateName);
    }
    
    /**
     *
     * @param string $notes
     * @return GiftTemplateInterface
     */
    public function setNotes($notes)
    {
        return $this->setData(self::NOTES, $notes);
    }

    /**
     *
     * @param string $styleColor
     * @return GiftTemplateInterface
     */
    public function setStyleColor($styleColor)
    {
        return $this->setData(self::STYLE_COLOR, $styleColor);
    }

    /**
     *
     * @param string $textColor
     * @return GiftTemplateInterface
     */
    public function setTextColor($textColor)
    {
        return $this->setData(self::TEXT_COLOR, $textColor);
    }

    /**
     *
     * @param string $designPattern
     * @return GiftTemplateInterface
     */
    public function setDesignPattern($designPattern)
    {
        return $this->setData(self::DESIGN_PATTERN, $designPattern);
    }

    /**
     *
     * @param string $images
     * @return GiftTemplateInterface
     */
    public function setImages($images)
    {
        return $this->setData(self::IMAGES, $images);
    }

    /**
     *
     * @param string $createdAt
     * @return GiftTemplateInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     *
     * @param string $updatedAt
     * @return GiftTemplateInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
    
    /**
     *
     * @param string $status
     * @return GiftTemplateInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
