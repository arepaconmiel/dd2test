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
class CancelOrderItemService extends AbstractItemService implements \Magestore\Giftvoucher\Api\Sales\CancelOrderItemServiceInterface
{
    
    /**
     * @var string
     */
    protected $process = 'cancel_order_item';
    
    /**
     * Process cancel gift card item
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return boolean
     */
    public function execute($item)
    {
        if (!$this->canProcessItem($item)) {
            return;
        }
        $this->cancelGiftCardItem($item);
        $this->markItemProcessed($item);
        return true;
    }

    /**
     * Cancel the gift code which generated from order item
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return $this
     */
    public function cancelGiftCardItem($orderItem)
    {
        $giftCodes = $this->getCancelableGiftcodes($orderItem);
        $cancelQty = $orderItem->getQtyToCancel();
        if (count($giftCodes)) {
            foreach ($giftCodes as $giftCode) {
                if ($cancelQty-- <= 0) {
                    break;
                }
                $this->giftCodeManagementService->cancelGiftCode($giftCode, $orderItem->getOrder()->getIncrementId());
            }
        }
        return $this;
    }
    
    /**
     * Get not cancelable statuses of gift card items
     *
     * @return array
     */
    public function getNotCancelableStatuses()
    {
        return [
            \Magestore\Giftvoucher\Model\Source\Status::STATUS_USED,
            \Magestore\Giftvoucher\Model\Source\Status::STATUS_DISABLED
        ];
    }
    
    /**
     * Get list of cancelable giftcodes
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return \Magestore\Giftvoucher\Api\Data\GiftVoucherInterface[]
     */
    public function getCancelableGiftcodes($orderItem)
    {
        $giftCodes = $this->giftCodeManagementService->getGiftCodesFromOrderItem($orderItem);
        $notCancelableStatuses = $this->getNotCancelableStatuses();
        if (count($giftCodes)) {
            foreach ($giftCodes as $key => $giftCode) {
                if (in_array($giftCode->getStatus(), $notCancelableStatuses)) {
                    unset($giftCodes[$key]);
                }
            }
        }
        return $giftCodes;
    }
    
    /**
     * 
     * @param \Magento\Sales\Model\Order\Item $item
     * @return boolean
     */
    public function getQtyToCancel($item)
    {
        if($item->getBaseRowTotal() + 0.0001 >= $item->getBaseGiftvoucherDiscount()) {
            $qtyToCancel = $item->getQtyToShip();
            return max($qtyToCancel, 0);
        }
        $qtyToCancel = min($item->getQtyToInvoice(), $item->getQtyToShip());
        return max($qtyToCancel, 0);
    }
}
