<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer\Frontend;

use Magento\Framework\DataObject;

/**
 * Class GiftcardPaymentMethodObserver
 * @package Magestore\Giftvoucher\Observer\Frontend
 */
class GiftcardPaymentMethodObserver extends \Magestore\Giftvoucher\Observer\GiftcardObserver
{
    /**
     * Render Gift Card form
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $this->_storeManager->getStore()->getStoreId();
        if ($this->_helperData->getGeneralConfig('active', $storeId)) {
            if ($observer['element_name']=='checkout.cart.coupon') {
                $data = $observer['transport']->getData('output');
                $htmlAddgiftcardform = $observer['layout']->createBlock('Magestore\Giftvoucher\Block\Cart\Giftcard')
                                            ->toHtml();
                $observer['transport']->setData('output', $data.$htmlAddgiftcardform);
            };
        }
    }
}
