<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Service\Sales;

/**
 * process cancel gift card item
 *
 */
class CancelOrderService implements \Magestore\Giftvoucher\Api\Sales\CancelOrderServiceInterface
{
    
    /**
     * @var string
     */
    protected $process = 'cancel_order';
    
    /**
     * @var \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface
     */
    protected $refundOrderService;
    
    /**
     *
     * @param \Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface $refundOrderService
     */
    public function __construct(\Magestore\Giftvoucher\Api\Sales\RefundOrderServiceInterface $refundOrderService)
    {
        $this->refundOrderService = $refundOrderService;
    }

    /**
     * Process cancel order applied gift card discount
     *
     * @param \Magento\Sales\Model\Order $order
     * @return boolean
     */
    public function execute($order)
    {
        $remainingGiftvoucherDiscount = 0;
        foreach ($order->getAllItems() as $item) {
            if ((float)$item->getBaseGiftVoucherDiscount()) {
                $usedGiftvoucherDiscountItem = $item->getBaseGiftVoucherDiscount() *
                        (($item->getQtyOrdered() - $this->getQtyToCancelBefore($item)) / $item->getQtyOrdered());
                $remainingGiftvoucherDiscount += $item->getBaseGiftVoucherDiscount() - $usedGiftvoucherDiscountItem;
            }
        }
        /* make sure that the $remainingGiftvoucherDiscount is not greater than BaseGiftVoucherDiscount */
        $remainingGiftvoucherDiscount = min($remainingGiftvoucherDiscount, $order->getBaseGiftVoucherDiscount());
        
        /* calculate gift voucher discount on shipping fee */
        if ($order->getBaseShippingAmount() > 0) {
            $cancelGiftvoucherDiscountShipping = $order->getBaseShippingCanceled() / $order->getBaseShippingAmount() * $order->getBaseGiftvoucherDiscountForShipping();
            $remainingGiftvoucherDiscount += $cancelGiftvoucherDiscountShipping;
        }
        
        /* process to return gift card discount */
        if ($remainingGiftvoucherDiscount) {
            $this->refundOrderService->refundOffline($order, $remainingGiftvoucherDiscount, \Magestore\Giftvoucher\Model\Actions::ACTIONS_CANCEL);
        }
        return $this;
    }
    
    /**
     * Get qtyToCancel of item before canceled
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return float
     */
    public function getQtyToCancelBefore($item)
    {
        return max(0, $item->getQtyOrdered() - max($item->getQtyShipped(), $item->getQtyInvoiced()));
    }
    
    /**
     * Check can cancel order
     * 
     * @param \Magento\Sales\Model\Order $order
     * @return boolean
     */
    public function canCancel($order)
    {
        if ($order->isCanceled() 
                || $order->getState() === \Magento\Sales\Model\Order::STATE_COMPLETE 
                || $order->getState() === \Magento\Sales\Model\Order::STATE_CLOSED) {
            return false;
        }
        if($order->getBaseGrandTotal() == 0
                && $order->getBaseGiftVoucherDiscount() > 0) {
            return true;
        }
        return false;        
    }
}
