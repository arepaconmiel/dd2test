<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel;

/**
 * Class GiftCodePattern
 * @package Magestore\Giftvoucher\Model\ResourceModel
 */
class GiftCodePattern extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritDoc}
     * @see \Magento\Framework\Model\ResourceModel\AbstractResource::_construct()
     */
    protected function _construct()
    {
        $this->_init('giftvoucher_template', 'template_id');
    }
}
