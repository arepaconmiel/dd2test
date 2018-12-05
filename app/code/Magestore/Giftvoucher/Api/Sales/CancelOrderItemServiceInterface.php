<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Sales;

/**
 * Interface CancelOrderItemServiceInterface
 * @package Magestore\Giftvoucher\Api\Sales
 */
interface CancelOrderItemServiceInterface
{
    /**
     * Cancel the gift code which generated from order item
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     */
    public function cancelGiftCardItem($orderItem);
    
    /**
     * Process cancel gift card item
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return boolean
     */
    public function execute($item);
}
