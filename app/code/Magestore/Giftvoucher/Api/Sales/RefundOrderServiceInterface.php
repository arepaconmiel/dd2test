<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Sales;

/**
 * Interface RefundOrderServiceInterface
 * @package Magestore\Giftvoucher\Api\Sales
 */
interface RefundOrderServiceInterface
{
    /**
     * Process refund giftcard discount
     *
     * @param \Magento\Sales\Model\Order $order
     * @param float $baseGiftvoucherDiscountTotal
     * @param string $action
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function refundOffline($order, $baseGiftvoucherDiscountTotal, $action = null);
    
    /**
     * Check can refund order
     * 
     * @param \Magento\Sales\Model\Order $order
     * @return boolean
     */
    public function canRefund($order);    
}
