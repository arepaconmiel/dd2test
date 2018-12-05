<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate;

/**
 * Class Collection
 * @package Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate
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
    protected $_idFieldName = 'giftcard_template_id';
    
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Magestore\Giftvoucher\Model\GiftTemplate', 'Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate');
    }
}
