<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Plugin\Sales\Model;

/**
 * Class Order
 * @package Magestore\Giftvoucher\Plugin\Sales\Model
 */
class Order
{
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface
     */
    protected $refundOrderService;
    
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\CancelOrderServiceInterface
     */
    protected $cancelOrderService;

    /**
     * Order constructor.
     * @param \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface $refundOrderService
     * @param \Magestore\Giftvoucher\Api\Sales\CancelOrderServiceInterface $cancelOrderService
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface $refundOrderService,
        \Magestore\Giftvoucher\Api\Sales\CancelOrderServiceInterface $cancelOrderService
    ) {
    
        $this->refundOrderService = $refundOrderService;
        $this->cancelOrderService = $cancelOrderService;
    }
    
    /**
     *
     * @param \Magento\Sales\Model\Order $order
     * @param boolean $result
     * @return boolean
     */
    public function afterCanCreditmemo(\Magento\Sales\Model\Order $order, $result)
    {
        if($result === true) {
            return true;
        }
        return $this->refundOrderService->canRefund($order);
    }
    
    /**
     *
     * @param \Magento\Sales\Model\Order $order
     * @param boolean $result
     * @return boolean
     */
    public function afterCanCancel(\Magento\Sales\Model\Order $order, $result)
    {
        if($result === true) {
            return true;
        }
        return $result;
        //return $this->cancelOrderService->canCancel($order);
    }    
}
