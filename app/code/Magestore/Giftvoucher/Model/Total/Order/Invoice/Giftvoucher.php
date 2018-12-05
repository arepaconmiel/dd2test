<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Total\Order\Invoice;

/**
 * Giftvoucher Total Order Invoice Giftvoucher Model
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Giftvoucher extends \Magento\Sales\Model\Order\Total\AbstractTotal
{

    /**
     * Collect invoice giftvoucher
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return this
     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if (!$order->getGiftVoucherDiscount()) {
            return $this;
        }

        $baseTotalDiscountAmountGiftvoucher = 0;
        $totalDiscountAmountGiftvoucher = 0;

        $totalGiftvoucherDiscountInvoiced = 0;
        $baseTotalGiftvoucherDiscountInvoiced = 0;

        $addShippingDiscount = true;
        foreach ($invoice->getOrder()->getInvoiceCollection() as $previusInvoice) {
            if ($previusInvoice->getGiftVoucherDiscount()) {
                $addShippingDiscount = false;
                $totalGiftvoucherDiscountInvoiced += $previusInvoice->getGiftVoucherDiscount();
                $baseTotalGiftvoucherDiscountInvoiced += $previusInvoice->getBaseGiftVoucherDiscount();
            }
        }

        if ($addShippingDiscount) {
            $totalDiscountAmountGiftvoucher = $order->getGiftvoucherDiscountForShipping();
            $baseTotalDiscountAmountGiftvoucher = $order->getBaseGiftvoucherDiscountForShipping();
        }

        if ($invoice->isLast()) {
            $totalDiscountAmountGiftvoucher = $order->getGiftVoucherDiscount() - $totalGiftvoucherDiscountInvoiced;
            $baseTotalDiscountAmountGiftvoucher = $order->getBaseGiftVoucherDiscount() - $baseTotalGiftvoucherDiscountInvoiced;
        } else {
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }
                $orderItemDiscountGiftvoucher = (float)$orderItem->getGiftVoucherDiscount();
                $baseOrderItemDiscountGiftvoucher = (float)$orderItem->getBaseGiftVoucherDiscount();

                $orderItemQty = $orderItem->getQtyOrdered();
                $invoiceItemQty = $item->getQty();

                if ($orderItemDiscountGiftvoucher && $orderItemQty) {
                    $discount = $invoice->roundPrice(
                        $orderItemDiscountGiftvoucher / $orderItemQty * $invoiceItemQty,
                        'regular',
                        false
                    );
                    $baseDiscount = $invoice->roundPrice(
                        $baseOrderItemDiscountGiftvoucher / $orderItemQty * $invoiceItemQty,
                        'base',
                        false
                    );
                    $totalDiscountAmountGiftvoucher += $discount;
                    $baseTotalDiscountAmountGiftvoucher += $baseDiscount;
                }
            }
        }

        $invoice->setBaseGiftVoucherDiscount($baseTotalDiscountAmountGiftvoucher);
        $invoice->setGiftVoucherDiscount($totalDiscountAmountGiftvoucher);
        return $this;
    }
}
