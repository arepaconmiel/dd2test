<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Sales;

/**
 * Interface RefundOrderItemServiceInterface
 * @package Magestore\Giftvoucher\Api\Sales
 */
interface RefundOrderItemServiceInterface
{
    /**
     * Process refund gift card item
     *
     * @param \Magento\Sales\Api\Data\CreditmemoItemInterface $item
     * @return boolean
     */
    public function execute($item);
    
    /**
     * Get not refundable statuses of gift card items
     *
     * @return array
     */
    public function getNotRefundableStatuses();
    
    /**
     * Refund the gift card item
     *
     * @param \Magento\Sales\Api\Data\CreditmemoItemInterface $item
     */
    public function refundGiftCardItem($item);
    
    
    /**
     * get qty-to-refund of gift card item
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return int
     */
    public function getGiftCardQtyToRefund($orderItem);
}
