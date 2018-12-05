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
class OrderItemCancel implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\CancelOrderItemServiceInterface
     */
    protected $cancelOrderItemService;
    
    /**
     * @var \Magestore\Giftvoucher\Service\Logger\LoggerInterface
     */
    protected $logger;

    /**
     * OrderItemCancel constructor.
     * @param \Magestore\Giftvoucher\Api\Sales\CancelOrderItemServiceInterface $cancelOrderItemService
     * @param \Magestore\Giftvoucher\Service\Logger\LoggerInterface $logger
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\Sales\CancelOrderItemServiceInterface $cancelOrderItemService,
        \Magestore\Giftvoucher\Service\Logger\LoggerInterface $logger
    ) {
    
        $this->cancelOrderItemService = $cancelOrderItemService;
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
        $item = $observer->getEvent()->getItem();
        try {
            $this->cancelOrderItemService->execute($item);
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage(), 'OrderItemCancel');
        }
        return $this;
    }
}
