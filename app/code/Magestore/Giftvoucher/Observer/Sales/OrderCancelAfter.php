<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer\Sales;

/**
 * Class SalesOrderPlaceAfterObserver
 * @package Magestore\Giftvoucher\Observer
 */
class OrderCancelAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\CancelOrderServiceInterface
     */
    protected $cancelOrderService;
    
    /**
     * @var \Magestore\Giftvoucher\Service\Logger\LoggerInterface
     */
    protected $logger;

    /**
     * OrderCancelAfter constructor.
     * @param \Magestore\Giftvoucher\Api\Sales\CancelOrderServiceInterface $cancelOrderService
     * @param \Magestore\Giftvoucher\Service\Logger\LoggerInterface $logger
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\Sales\CancelOrderServiceInterface $cancelOrderService,
        \Magestore\Giftvoucher\Service\Logger\LoggerInterface $logger
    ) {
    
        $this->cancelOrderService = $cancelOrderService;
        $this->logger = $logger;
    }
    
    /**
     * Process Gift Card data after placing order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        try {
            $this->cancelOrderService->execute($order);
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage(), 'OrderCancel');
        }
        return $this;
    }
}
