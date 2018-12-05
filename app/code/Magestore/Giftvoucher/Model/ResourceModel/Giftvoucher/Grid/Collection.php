<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Giftcode Grid Collection
 * @package Magestore\Giftvoucher
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * Columns to join history table
     * @var array
     */
    protected $fields = [
        'history_amount'     => 'amount',
        'history_currency'   => 'currency',
        'created_at'         => 'created_at',
        'extra_content'      => 'extra_content',
        'order_increment_id' => 'order_increment_id',
    ];

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'giftvoucher',
        $resourceModel = 'Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher'
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                array('history' => $this->getTable('giftvoucher_history')),
                'main_table.giftvoucher_id = history.giftvoucher_id',
                $this->fields
            )->group('main_table.giftvoucher_id')
            ->where('history.action = ?', \Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $countSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $countSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        if (!count($this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP))) {
            $countSelect->columns(new \Zend_Db_Expr('COUNT(*)'));
            return $countSelect;
        }

        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        $countSelect->distinct(true); // Different from parent function
        $group = $this->getSelect()->getPart(\Magento\Framework\DB\Select::GROUP);
        $countSelect->columns(new \Zend_Db_Expr(("COUNT(DISTINCT ".implode(", ", $group).")")));
        return $countSelect;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, array_keys($this->fields))) {
            $field = new \Zend_Db_Expr('history.' . $this->fields[$field]);
        } else {
            $field = new \Zend_Db_Expr('main_table.' . $field);
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if (in_array($field, array_keys($this->fields))) {
            $field = new \Zend_Db_Expr('history.' . $this->fields[$field]);
        } else {
            $field = new \Zend_Db_Expr('main_table.' . $field);
        }
        return parent::setOrder($field, $direction);
    }
}
