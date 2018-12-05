<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Order\Creditmemo;

/**
 * Class Totals
 * @package Magestore\Giftvoucher\Block\Adminhtml\Order\Creditmemo
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Totals
{
    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();
        $giftVoucherDiscount = $creditmemo->getGiftVoucherDiscount();
        if ($giftVoucherDiscount && $giftVoucherDiscount > 0) {
            $dataObject = new \Magento\Framework\DataObject(
                [
                    'code' => 'giftvoucher',
                    'label' => __('Gift Card (%1)', $creditmemo->getOrder()->getGiftVoucherGiftCodes()),
                    'value' => -$giftVoucherDiscount,
                    'base_value' => -$creditmemo->getBaseGiftVoucherDiscount(),
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
