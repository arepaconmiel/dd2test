<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Service\Sales;

/**
 * process refund gift card item
 *
 */
class RefundOrderItemService extends AbstractItemService implements \Magestore\Giftvoucher\Api\Sales\RefundOrderItemServiceInterface
{
    /**
     * @var string
     */
    protected $process = 'create_creditmemo_item';

    /**
     * Get not refundable statuses of gift card items
     *
     * @return array
     */
    public function getNotRefundableStatuses()
    {
        return [
            \Magestore\Giftvoucher\Model\Source\Status::STATUS_USED,
            \Magestore\Giftvoucher\Model\Source\Status::STATUS_REFUNDED
        ];
    }
    
    /**
     * Process refund gift card item
     *
     * @param \Magento\Sales\Api\Data\CreditmemoItemInterface $item
     * @return boolean
     */
    public function execute($item)
    {
        if (!$this->canProcessItem($item)) {
            return;
        }
        $this->refundGiftCardItem($item);
        $this->markItemProcessed($item);
        return true;
    }


    /**
     * Refund the gift card item
     *
     * @param \Magento\Sales\Model\Order\Creditmemo\Item $item
     * @return $this
     */
    public function refundGiftCardItem($item)
    {
        $giftCodes = $this->getRefundableGiftcodes($item->getOrderItem());
        $qtyRefund = $item->getQty();
        if (count($giftCodes)) {
            foreach ($giftCodes as $giftCode) {
                if ($qtyRefund-- <= 0) {
                    break;
                }
                $this->giftCodeManagementService->refundGiftCode($giftCode, $item->getOrderItem()->getOrder()->getIncrementId());
            }
        }
        return $this;
    }
    
    /**
     * get qty-to-refund of gift card item
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return int
     */
    public function getGiftCardQtyToRefund($orderItem)
    {
        return count($this->getRefundableGiftcodes($orderItem));
    }
    
    /**
     * Get list of refundable giftcodes
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return \Magestore\Giftvoucher\Api\Data\GiftVoucherInterface[]
     */
    public function getRefundableGiftcodes($orderItem)
    {
        $giftCodes = $this->giftCodeManagementService->getGiftCodesFromOrderItem($orderItem);
        $notRefundableStatuses = $this->getNotRefundableStatuses();
        if (count($giftCodes)) {
            foreach ($giftCodes as $key => $giftCode) {
                if (in_array($giftCode->getStatus(), $notRefundableStatuses)) {
                    unset($giftCodes[$key]);
                }
            }
        }
        return $giftCodes;
    }
}
