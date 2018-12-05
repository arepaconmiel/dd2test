<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer\Frontend;

use Magento\Framework\DataObject;

/**
 * Class CouponPostActionObserver
 * @package Magestore\Giftvoucher\Observer\Frontend
 */
class CouponPostActionObserver extends \Magestore\Giftvoucher\Observer\GiftcardObserver
{
    /**
     * Apply gift codes to cart
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magestore\Giftvoucher\Observer\Frontend\CouponPostActionObserver
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        $session = $this->_objectManager->get('Magento\Checkout\Model\Session');
        $quote = $session->getQuote();
        $code = trim($action->getRequest()->getParam('coupon_code'));

        if (!$code) {
            return;
        }

        if (!$this->_helperData->isAvailableToAddCode()) {
            return;
        }
        if (!$this->_helperData->getGeneralConfig('use_with_coupon') && ($quote->getGiftVoucherDiscount() > 0)) {
            $this->messageManager->addNotice(__('The gift code(s) has been used. You cannot apply a coupon code with gift codes to get discount.'));
            $action->getActionFlag()->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $action->getResponse()->setRedirect($this->_urlBuilder->getUrl('checkout/cart'));
        }
        return;
    }
}
