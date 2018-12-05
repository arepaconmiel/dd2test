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
class CreditmemoSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface
     */
    protected $refundOrderService;
    
    /**
     * @var \Magestore\Giftvoucher\Service\Logger\LoggerInterface
     */
    protected $logger;

    /**
     * CreditmemoSaveAfter constructor.
     * @param \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface $refundOrderService
     * @param \Magestore\Giftvoucher\Service\Logger\LoggerInterface $logger
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface $refundOrderService,
        \Magestore\Giftvoucher\Service\Logger\LoggerInterface $logger
    ) {
    
        $this->refundOrderService = $refundOrderService;
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
        $creditmemo = $observer->getEvent()->getCreditmemo();
        try {
            $this->refundOrderService->execute($creditmemo);
        } catch (\Exception $e) {
            $this->logger->log($e->getMessage(), 'CreditmemoItemSaveAfter');
        }
        return $this;
    }
}
