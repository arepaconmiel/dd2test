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
class CreditmemoItemSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\RefundOrderItemServiceInterface
     */
    protected $refundOrderItemService;
    
    /**
     * @var \Magestore\Giftvoucher\Service\Logger\LoggerInterface
     */
    protected $logger;

    /**
     * CreditmemoItemSaveAfter constructor.
     * @param \Magestore\Giftvoucher\Api\Sales\RefundOrderItemServiceInterface $refundOrderItemService
     * @param \Magestore\Giftvoucher\Service\Logger\LoggerInterface $logger
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\Sales\RefundOrderItemServiceInterface $refundOrderItemService,
        \Magestore\Giftvoucher\Service\Logger\LoggerInterface $logger
    ) {
    
        $this->refundOrderItemService = $refundOrderItemService;
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
        $item = $observer->getEvent()->getCreditmemoItem();
        try {
            $this->refundOrderItemService->execute($item);
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage(), 'CreditmemoItemSaveAfter');
        }
        return $this;
    }
}
