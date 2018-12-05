<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Order\Invoice;

/**
 * Adminhtml Giftvoucher Invoice Totals Block
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
        $totalsBlock = $this->getParentBlock();
        $invoice = $totalsBlock->getInvoice();
        $giftVoucherDiscount = $invoice->getGiftVoucherDiscount();
        if ($giftVoucherDiscount && $giftVoucherDiscount > 0) {
            $dataObject = new \Magento\Framework\DataObject(
                [
                    'code' => 'giftvoucher',
                    'label' => __('Gift Card (%1)', $invoice->getOrder()->getGiftVoucherGiftCodes()),
                    'value' => -$giftVoucherDiscount,
                    'base_value' => -$invoice->getBaseGiftVoucherDiscount(),
                ]
            );
            $totalsBlock->addTotal($dataObject, 'subtotal');

            /**
             * Get total discount and re-calculate discount value to showing
             */
            $discountTotal = $totalsBlock->getTotal('discount');
            if (!empty($discountTotal) && $discountTotal->getValue() != 0) {
                $discountTotal->setValue($discountTotal->getValue() + $giftVoucherDiscount);
                if ($discountTotal->getValue() != 0) {
                    $totalsBlock->addTotal($discountTotal);
                } else {
                    $totalsBlock->removeTotal($discountTotal->getCode());
                }
            }
        }
    }
}
