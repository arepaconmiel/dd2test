<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel;

/**
 * Giftvoucher resource model class
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Giftvoucher extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('giftvoucher', 'giftvoucher_id');
    }
    
    /**
     * Check giftcode is existed in database
     *
     * @param string $code
     * @return boolean
     */
    public function giftcodeExist($code)
    {
        $select = $this->getConnection()->select()->from($this->getMainTable())
                    ->where('gift_code=?', trim($code));
        if ($this->getConnection()->fetchRow($select)) {
            return true;
        }
        return false;
    }
}
