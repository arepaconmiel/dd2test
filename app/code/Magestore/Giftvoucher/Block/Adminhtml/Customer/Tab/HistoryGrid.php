<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Customer\Tab;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Class HistoryGrid
 * @package Magestore\Giftvoucher\Block\Adminhtml\Customer\Tab
 */
class HistoryGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\History\Collection
     */
    protected $_historyCollection;
    
    /**
     * @var \Magestore\Giftvoucher\Model\Actions
     */
    protected $_actions;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\ResourceModel\History\Collection $historyCollection
     * @param \Magestore\Giftvoucher\Model\Actions $actions
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\ResourceModel\History\Collection $historyCollection,
        \Magestore\Giftvoucher\Model\Actions $actions,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_historyCollection = $historyCollection;
        $this->_actions = $actions;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('historyGrid');
        $this->setDefaultSort('history_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        if (!$customerId) {
            $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        }
        $collection = $this->_historyCollection
            ->joinGiftcodeForGrid()
            ->addFieldToFilter('main_table.customer_id', $customerId);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('history_id', array(
            'header' => __('ID'),
            'align' => 'left',
            'width' => '50px',
            'type' => 'number',
            'index' => 'history_id',
        ));
        
        $this->addColumn('action', array(
            'header' => __('Action'),
            'align' => 'left',
            'index' => 'action',
            'type' => 'options',
            'options' => $this->_actions->getOptionArray(),
        ));

        $this->addColumn('amount', array(
            'header' => __('Balance Change'),
            'align' => 'left',
            'index' => 'amount',
            'type' => 'currency',
            'currency' => 'currency',
            'rate' => 1
        ));
        
        $this->addColumn('gift_code', array(
            'header' => __('Gift Card Code'),
            'align' => 'left',
            'index' => 'gift_code',
        ));
        
        $this->addColumn('order_increment_id', array(
            'header' => __('Order'),
            'align' => 'left',
            'index' => 'order_increment_id',
        ));
        
        $this->addColumn('balance', array(
            'header' => __('Current Balance'),
            'align' => 'left',
            'index' => 'balance',
            'filter_index' => 'main_table.balance',
            'type' => 'currency',
            'currency' => 'currency',
            'rate' => 1
        ));
        
        $this->addColumn('created_at', array(
            'header' => __('Created Time'),
            'align' => 'left',
            'type' => 'datetime',
            'index' => 'created_at',
        ));
        
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('giftvoucheradmin/gifthistory/customer', array(
            '_current' => true,
            'customer_id' => $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID),
        ));
    }
}
