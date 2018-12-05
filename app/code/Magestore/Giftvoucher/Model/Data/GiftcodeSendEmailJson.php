<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Data;

/**
 * Giftvoucher Actions Model
 */
class GiftcodeSendEmailJson extends \Magento\Framework\DataObject implements \Magestore\Giftvoucher\Api\Data\GiftcodeSendEmailJsonInterface
{
    /**
     * Get Gift Code
     * @param string $giftCode
     * @return $this
     */
    public function setGiftCode($giftCode)
    {
        $this->setData(self::GIFT_CODE, $giftCode);
        return $this;
    }

    /**
     * Get gift code
     *
     * @return string
     */
    public function getGiftCode()
    {
        return $this->getData(self::GIFT_CODE);
    }

    /**
     * Get type
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);
        return $this;
    }

    /**
     * Get gift type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }
}
