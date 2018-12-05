<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\RequestInterface;

/**
 * Gift Code Pattern Grid Collection
 * @package Magestore\Giftvoucher
 */
class GiftCode extends \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\Grid\Collection
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RequestInterface $request
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        $mainTable = 'giftvoucher',
        $resourceModel = 'Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher'
    ) {
        $this->request = $request;
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
            ->where('history.action = ?', \Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE)
            ->where('main_table.template_id = ?', $this->request->getParam('current_template_id'));
        return $this;
    }
}
