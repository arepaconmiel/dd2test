<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer;

/**
 * Class SalesOrderPlaceAfterObserver
 * @package Magestore\Giftvoucher\Observer
 */
class SalesOrderPlaceAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magestore\Giftvoucher\Service\Redeem\CheckoutService
     */
    protected $checkoutService;

    /**
     * @var \Magestore\Giftvoucher\Api\GiftVoucherProduct\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * SalesOrderPlaceAfterObserver constructor.
     * @param \Magestore\Giftvoucher\Api\GiftvoucherProduct\OrderManagementInterface $orderManagement
     * @param \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\GiftvoucherProduct\OrderManagementInterface $orderManagement,
        \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService
    ) {
        $this->orderManagement = $orderManagement;
        $this->checkoutService = $checkoutService;
    }

    /**
     * Process Gift Card data after placing order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $this->orderManagement->addGiftVoucherForOrder($order);
        $this->checkoutService->processOrderPlaceAfter($order);
        return $this;
    }
}
