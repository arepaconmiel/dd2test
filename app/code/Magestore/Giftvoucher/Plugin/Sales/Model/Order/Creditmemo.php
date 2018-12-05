<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Plugin\Sales\Model\Order;

/**
 * Class Creditmemo
 * @package Magestore\Giftvoucher\Plugin\Sales\Model\Order
 */
class Creditmemo
{
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface
     */
    protected $refundOrderService;

    /**
     * Creditmemo constructor.
     * @param \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface $refundOrderService
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface $refundOrderService
    ) {
    
        $this->refundOrderService = $refundOrderService;
    }

    /**
     *
     * @param \Magento\Sales\Model\Creditmemo|\Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @param boolean $result
     * @return bool
     */
    public function afterIsValidGrandTotal(\Magento\Sales\Model\Order\Creditmemo $creditmemo, $result)
    {
        if($result === true) {
            return true;
        }
        return $this->refundOrderService->canRefund($creditmemo->getOrder());
    }
}
