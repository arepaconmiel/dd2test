<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Order;

/**
 * Adminhtml Giftvoucher Order Totals Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Totals
{
    public function initTotals()
    {
        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getOrder();
        $giftVoucherDiscount = $order->getGiftVoucherDiscount();
        if ($giftVoucherDiscount && $giftVoucherDiscount > 0) {
            $orderTotalsBlock->addTotal(new \Magento\Framework\DataObject(
                [
                    'code' => 'giftvoucher',
                    'label' => __('Gift Card (%1)', $order->getGiftVoucherGiftCodes()),
                    'value' => -$giftVoucherDiscount,
                    'base_value' => -$order->getBaseGiftVoucherDiscount(),
                ]
            ), 'subtotal');

            /**
             * Get total discount and re-calculate discount value to showing
             */
            $discountTotal = $orderTotalsBlock->getTotal('discount');
            if (!empty($discountTotal) && $discountTotal->getValue() != 0) {
                $discountTotal->setValue($discountTotal->getValue() + $giftVoucherDiscount);
                if ($discountTotal->getValue() != 0) {
                    $orderTotalsBlock->addTotal($discountTotal);
                } else {
                    $orderTotalsBlock->removeTotal($discountTotal->getCode());
                }
            }
        }
        $refund = $this->getGiftCardRefund($order);
        if (($refund > 0 || $refund === 0.0) && ($order->getUseGiftCreditAmount() || $order->getGiftVoucherDiscount())) {
            $baseCurrency = $this->_storeManager->getStore($order->getStoreId())->getBaseCurrency();
            if ($rate = $baseCurrency->getRate($order->getOrderCurrencyCode())) {
                $refundAmount = $refund / $rate;
            }
            $label = __('Refund to customer gift card code used to check out');
            $dataObject = new \Magento\Framework\DataObject(
                [
                    'code' => 'giftcard_refund',
                    'label' => $label,
                    'value' => $refund,
                    'base_value' => $refundAmount,
                    'area' => 'footer',
                ]
            );
            $orderTotalsBlock->addTotal($dataObject, 'subtotal');
        }
    }

    /**
     * Get Gift Card refunded amount
     *
     * @param \Magento\Sales\Model\Order $order
     * @return float
     */
    public function getGiftCardRefund($order)
    {
        $refund = 0;
        foreach ($order->getCreditmemosCollection() as $creditmemo) {
            $refund += $creditmemo->getGiftcardRefundAmount();
        }
        return $refund;
    }
}
