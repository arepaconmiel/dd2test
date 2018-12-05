<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer\Frontend;

use Magento\Framework\DataObject;

/**
 * Class CollectTotalsAfterObserver
 * @package Magestore\Giftvoucher\Observer\Frontend
 */
class CollectTotalsAfterObserver extends \Magestore\Giftvoucher\Observer\GiftcardObserver
{
    /**
     * Set Quote information about gift codes
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($code = trim($this->_request->getParam('coupon_code'))) {
            $quote = $observer->getEvent()->getQuote();
            if ($code != $quote->getCouponCode()) {
                $codes = $this->_objectManager->get('Magento\Checkout\Model\Session')->getCodes();
                $codes[] = $code;
                $codes = array_unique($codes);
                $this->_objectManager->get('Magento\Checkout\Model\Session')->setCodes($codes);
            }
        }
    }
}
