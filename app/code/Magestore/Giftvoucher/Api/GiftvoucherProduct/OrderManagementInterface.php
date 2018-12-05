<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\GiftvoucherProduct;

/**
 * Interface OrderManagementInterface
 * @package Magestore\Giftvoucher\Api\GiftvoucherProduct
 */
interface OrderManagementInterface
{
    /**
     * @param \Magento\Sales\Model\Order $order
     * @param float $baseGrandTotal
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function refundOffline($order, $baseGrandTotal);

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order
     */
    public function loadOrderData($order);

    /**
     * Add Gift Card data to order
     *
     * @param $order
     * @return \Magento\Sales\Model\Order
     */
    public function addGiftVoucherForOrder($order);
}
