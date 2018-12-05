<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer;

/**
 * Class OrderLoadAfterObserver
 * @package Magestore\Giftvoucher\Observer
 */
class OrderLoadAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $orderManagement;

    /**
     * OrderLoadAfterObserver constructor.
     * @param \Magestore\Giftvoucher\Api\GiftvoucherProduct\OrderManagementInterface $orderManagement
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\GiftvoucherProduct\OrderManagementInterface $orderManagement
    ) {
        $this->orderManagement = $orderManagement;
    }

    /**
     * Loading Gift Card information after order loaded
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magestore\Giftvoucher\Observer\OrderLoadAfterObserver
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $this->orderManagement->loadOrderData($order);
        return $this;
    }
}
