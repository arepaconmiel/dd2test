<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer;

/**
 * Class SalesOrderSaveAfterObserver
 * @package Magestore\Giftvoucher\Observer
 */
class SalesOrderSaveAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $giftvoucherFactory;

    protected $orderManagement;

    protected $messageManager;

    /**
     * SalesOrderSaveAfterObserver constructor.
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param \Magestore\Giftvoucher\Api\GiftvoucherProduct\OrderManagementInterface $orderManagement
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magestore\Giftvoucher\Api\GiftvoucherProduct\OrderManagementInterface $orderManagement,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->giftvoucherFactory = $giftvoucherFactory;
        $this->orderManagement = $orderManagement;
    }
    /**
     * Process Gift Card data after order is saved
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order->getStatus() == \Magento\Sales\Model\Order::STATE_COMPLETE) {
            $this->orderManagement->addGiftVoucherForOrder($order);
        }
    }
}
