<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel;

/**
 * Class GiftTemplate
 * @package Magestore\Giftvoucher\Model\ResourceModel
 */
class GiftTemplate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('giftcard_template', 'giftcard_template_id');
    }
}
