<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Plugin\Sales\Model\Order;

/**
 * Class Item
 * @package Magestore\Giftvoucher\Plugin\Sales\Model\Order
 */
class Item
{
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\RefundOrderItemServiceInterface
     */
    protected $refundOrderItemService;
    
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\CancelOrderItemServiceInterface
     */
    protected $cancelOrderItemService;


    /**
     * Item constructor.
     * @param \Magestore\Giftvoucher\Api\Sales\RefundOrderItemServiceInterface $refundOrderItemService
     * @param \Magestore\Giftvoucher\Api\Sales\CancelOrderItemServiceInterface $cancelOrderItemService
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\Sales\RefundOrderItemServiceInterface $refundOrderItemService,
        \Magestore\Giftvoucher\Api\Sales\CancelOrderItemServiceInterface $cancelOrderItemService
    ) {
    
        $this->refundOrderItemService = $refundOrderItemService;
        $this->cancelOrderItemService = $cancelOrderItemService;
    }

    /**
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @param $result
     * @return float|int
     * @oaram float|integer $result
     */
    public function afterGetQtyToRefund(\Magento\Sales\Model\Order\Item $item, $result)
    {
        if ($item->getProductType() != \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE) {
            return $result;
        }
        return min($result, $this->refundOrderItemService->getGiftCardQtyToRefund($item));
    }

    /**
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @param $result
     * @return float|int
     * @oaram float|integer $result
     */
    public function afterGetQtyToCancel(\Magento\Sales\Model\Order\Item $item, $result)
    {
        return $result;
    }
}
