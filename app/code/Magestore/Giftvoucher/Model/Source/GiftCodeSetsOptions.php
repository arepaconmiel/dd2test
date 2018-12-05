<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Source;

/**
 * Class GiftCodeSetsOptions
 * @package Magestore\Giftvoucher\Model\Source
 */
class GiftCodeSetsOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var GiftCodeSets
     */
    protected $_giftcodeSets;

    /**
     * GiftCodeSetsOptions constructor.
     * @param \Magestore\Giftvoucher\Model\GiftCodeSets|GiftCodeSets $giftcodeSets
     */
    public function __construct(
        \Magestore\Giftvoucher\Model\GiftCodeSets $giftcodeSets
    ) {
        $this->_giftcodeSets = $giftcodeSets;
    }

    /**
     * @return array
     */
    public function getAvailableGiftcodeSets()
    {
        $giftcodeSets = $this->_giftcodeSets->getCollection();
        $listGiftcodeSets = array();
        foreach ($giftcodeSets as $giftcodeSet) {
            $listGiftcodeSets[] = array('label' => $giftcodeSet->getSetName(),
                'value' => $giftcodeSet->getSetId());
        }
        return  $listGiftcodeSets;
    }

    /**
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (is_null($this->_options)) {
            $this->_options = $this->getAvailableGiftcodeSets();
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => __('-- Please Select --'),
            ));
        }
        return $options;
    }
}
