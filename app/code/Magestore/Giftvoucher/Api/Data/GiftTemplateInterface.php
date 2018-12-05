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
interface GiftTemplateInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const GIFTCARD_TEMPLATE_ID = 'giftcard_template_id';
    const TEMPLATE_NAME = 'template_name';
    const NOTES = 'notes';
    const STYLE_COLOR = 'style_color';
    const TEXT_COLOR = 'text_color';
    const DESIGN_PATTERN = 'design_pattern';
    const IMAGES = 'images';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STATUS = 'status';
    /**#@-*/
    
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;
    
    const DEFAULT_TEMPLATE_ID = 'amazon-giftcard-01';
    
    /**
     * Constants for keys of print data array
     */
    const LOGO_URL_PRINT = 'logo_url';
    const IMAGE_URL_PRINT = 'giftImageUrl';
    const MESSAGE_PRINT = 'giftMessage';
    const VALUE_PRINT = 'giftValue';
    const GIFTCODE_PRINT = 'giftCode';
    const BARCODE_URL_PRINT = 'barcodeUrl';
    const EXPIRED_DATE_PRINT = 'expiredDate';
    const NOTES_PRINT = 'notes';
    const TEXT_COLOR_PRINT = 'textColor';
    const STYLE_COLOR_PRINT = 'styleColor';
    const SENDER_NAME_PRINT = 'senderName';
    const RECIPIENT_NAME_PRINT = 'recipientName';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get giftcard_template_id
     *
     * @return string
     */
    public function getGiftcardTemplateId();

    /**
     * Get template_name
     *
     * @return string|null
     */
    public function getTemplateName();

    /**
     * Get notes
     *
     * @return string|null
     */
    public function getNotes();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function getStatus();
    
    /**
     *
     * @return string|null
     */
    public function getTextColor();
    
    /**
     *
     * @return string|null
     */
    public function getStyleColor();
    
    /**
     *
     * @return string|null
     */
    public function getDesignPattern();

    /**
     *
     * @return string|null
     */
    public function getImages();

    /**
     * Set ID
     *
     * @param int $id
     * @return GiftTemplateInterface
     */
    public function setId($id);

    /**
     * Set Id
     *
     * @param string $id
     * @return GiftTemplateInterface
     */
    public function setGiftcardTemplateId($id);

    /**
     * Set Template Name
     *
     * @param string $templateName
     * @return GiftTemplateInterface
     */
    public function setTemplateName($templateName);
    
    /**
     *
     * @param string $notes
     * @return GiftTemplateInterface
     */
    public function setNotes($notes);

    /**
     *
     * @param string $styleColor
     * @return GiftTemplateInterface
     */
    public function setStyleColor($styleColor);

    /**
     *
     * @param string $textColor
     * @return GiftTemplateInterface
     */
    public function setTextColor($textColor);

    /**
     *
     * @param string $designPattern
     * @return GiftTemplateInterface
     */
    public function setDesignPattern($designPattern);

    /**
     *
     * @param string $images
     * @return GiftTemplateInterface
     */
    public function setImages($images);

    /**
     *
     * @param string $createdAt
     * @return GiftTemplateInterface
     */
    public function setCreatedAt($createdAt);

    /**
     *
     * @param string $updatedAt
     * @return GiftTemplateInterface
     */
    public function setUpdatedAt($updatedAt);
    
    /**
     *
     * @param string $status
     * @return GiftTemplateInterface
     */
    public function setStatus($status);
}
