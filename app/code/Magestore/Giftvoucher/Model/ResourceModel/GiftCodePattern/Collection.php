<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern;

/**
 * Class Collection
 * @package Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Identifier field name for collection items
     *
     * Can be used by collections with items without defined
     *
     * @var string
     */
    protected $_idFieldName = 'template_id';
    
    /**
     * {@inheritDoc}
     * @see \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection::_construct()
     */
    protected function _construct()
    {
        $this->_init(
            'Magestore\Giftvoucher\Model\GiftCodePattern',
            'Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern'
        );
    }
}
