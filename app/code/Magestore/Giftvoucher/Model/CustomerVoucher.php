<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model;

/**
 * Giftvoucher Customervoucher Model
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class CustomerVoucher extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher');
    }
}
