<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Total\Order\Creditmemo;

/**
 * Giftvoucher Total Order Creditmemo Giftvoucher Model
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Giftvoucher extends \Magento\Sales\Model\Order\Total\AbstractTotal
{

    /**
     * Collect creditmemo giftvoucher
     *
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if (!$order->getGiftVoucherDiscount()) {
            return $this;
        }

        $totalDiscountAmountGiftvoucher = 0;
        $baseTotalDiscountAmountGiftvoucher = 0;

        $totalGiftvoucherDiscountRefunded = 0;
        $baseGiftvoucherTotalDiscountRefunded = 0;

        foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
            if ($existedCreditmemo->getGiftVoucherDiscount()) {
                $totalGiftvoucherDiscountRefunded += $existedCreditmemo->getGiftVoucherDiscount();
                $baseGiftvoucherTotalDiscountRefunded += $existedCreditmemo->getBaseGiftVoucherDiscount();
            }
        }

        $baseShippingAmount = $creditmemo->getBaseShippingAmount();
        if ($baseShippingAmount) {
            $baseTotalDiscountAmountGiftvoucher = $baseShippingAmount * $order->getBaseGiftvoucherDiscountForShipping()
                / $order->getBaseShippingAmount();
            $totalDiscountAmountGiftvoucher = $creditmemo->getShippingAmount() * $order->getGiftvoucherDiscountForShipping() 
                / $order->getShippingAmount();
        }

        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
            if ($orderItem->isDummy()) {
                continue;
            }

            $orderItemDiscountGiftvoucher = (float) $orderItem->getGiftVoucherDiscount();
            $baseOrderItemDiscountGiftvoucher = (float) $orderItem->getBaseGiftVoucherDiscount();

            $orderItemQty = $orderItem->getQtyOrdered();
            $creditmemoItemQty = $item->getQty();

            if ($orderItemDiscountGiftvoucher && $orderItemQty) {
                $discount = $creditmemo->roundPrice(
                    $orderItemDiscountGiftvoucher / $orderItemQty * $creditmemoItemQty,
                    'regular',
                    false
                );
                $baseDiscount = $creditmemo->roundPrice(
                    $baseOrderItemDiscountGiftvoucher / $orderItemQty * $creditmemoItemQty,
                    'base',
                    false
                );

                $totalDiscountAmountGiftvoucher += $discount;
                $baseTotalDiscountAmountGiftvoucher += $baseDiscount;
            }
        }

        $creditmemo->setBaseGiftVoucherDiscount($baseTotalDiscountAmountGiftvoucher);
        $creditmemo->setGiftVoucherDiscount($totalDiscountAmountGiftvoucher);
    }

    /**
     * Check credit memo is last or not
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return boolean
     */
    public function isLast($creditmemo)
    {
        foreach ($creditmemo->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }
}
