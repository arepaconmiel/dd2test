<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher;

/**
 * Giftvoucher resource collection
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'giftvoucher_id';
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher $resource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Zend_Db_Adapter_Abstract $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher $resource,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\DB\Adapter\AdapterInterface  $connection = null
    ) {
        $this->_date = $date;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }
    
    protected function _construct()
    {
        $this->_init('Magestore\Giftvoucher\Model\Giftvoucher', 'Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher');
    }

    /**
     * @param $quoteId
     * @return $this
     */
    public function addItemFilter($quoteId)
    {
        if ($this->hasFlag('add_item_filer') && $this->getFlag('add_item_filer')) {
            return $this;
        }
        $this->setFlag('add_item_filer', true);

        $this->getSelect()->joinLeft(
           array('history' => $this->getTable('giftvoucher_history')),
           'main_table.giftvoucher_id = history.giftvoucher_id',
           array('quote_item_id')
        )->where('history.quote_item_id = ?', $quoteId)
        ->where('history.action = ?', \Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE);

        return $this;
    }

    /**
     * @param $dayBefore
     * @return $this
     */
    public function addExpireAfterDaysFilter($dayBefore)
    {
        $date = $this->_date->gmtDate();
        $zendDate = new \Zend_Date($date);
        $dayAfter = $zendDate->addDay($dayBefore)->toString('YYYY-MM-dd');
        $this->getSelect()->where('date(expired_at) = ?', $dayAfter);
        return $this;
    }
}
