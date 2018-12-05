<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\History;

/**
 * Giftvoucher history resource collection
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Magestore\Giftvoucher\Model\History', 'Magestore\Giftvoucher\Model\ResourceModel\History');
    }
    
    /**
     * Join Giftcode for Grid of Customer
     *
     * @return \Magestore\Giftvoucher\Model\ResourceModel\History\Collection
     */
    public function joinGiftcodeForGrid()
    {
        if ($this->hasFlag('join_giftcode') && $this->getFlag('join_giftcode')) {
            return $this;
        }
        $this->setFlag('join_giftcode', true);
        $this->getSelect()->joinLeft(
            array('giftvoucher' => $this->getTable('giftvoucher')),
            'main_table.giftvoucher_id = giftvoucher.giftvoucher_id',
            array(
                'gift_code'
            )
        );
        return $this;
    }

    /**
     * @return $this
     */
    public function joinGiftVoucher()
    {
        if ($this->hasFlag('join_giftvoucher') && $this->getFlag('join_giftvoucher')) {
            return $this;
        }
        $this->setFlag('join_giftvoucher', true);
        $this->getSelect()->joinLeft(
            array('giftvoucher' => $this->getTable('giftvoucher')),
            'main_table.giftvoucher_id = giftvoucher.giftvoucher_id',
            array(
                'gift_code'
            )
        )->where('main_table.action = ?', \Magestore\Giftvoucher\Model\Actions::ACTIONS_SPEND_ORDER);
        return $this;
    }

    /**
     * @return $this
     */
    public function joinSalesOrder()
    {
        $this->getSelect()->joinLeft(
            array('o' => $this->getTable('sales_order')),
            'main_table.order_increment_id = o.increment_id',
            array('order_customer_id' => 'customer_id')
        )->group('o.customer_id');

        return $this;
    }

    /**
     * @return $this
     */
    public function getHistory()
    {
        $this->getSelect()->order('main_table.created_at DESC');
        $this->getSelect()
            ->joinLeft(
                array('o' => $this->getTable('sales_order')),
                'main_table.order_increment_id = o.increment_id',
                array('order_id' => 'entity_id')
            );

        return $this;
    }
}
