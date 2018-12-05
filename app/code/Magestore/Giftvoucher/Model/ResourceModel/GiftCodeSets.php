<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel;

/**
 * Class GiftCodeSets
 * @package Magestore\Giftvoucher\Model\ResourceModel
 */
class GiftCodeSets extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('giftvoucher_sets', 'set_id');
    }
}
