<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\Product;

/**
 * Class Collection
 * @package Magestore\Giftvoucher\Model\ResourceModel\Product
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     *
     */
    protected function _initSelect()
    {
        $this->addFieldToSelect('gift_template_ids');
        $this->addFieldToFilter('type_id', \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE);
        parent::_initSelect();
    }
}
