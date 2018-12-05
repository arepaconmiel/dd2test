<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer\Frontend;

/**
 * Class PredispatchCheckoutObserver
 * @package Magestore\Giftvoucher\Observer\Frontend
 */
class PredispatchCheckoutObserver extends \Magestore\Giftvoucher\Observer\GiftcardObserver
{
    /**
     * Disable Gift Card multishipping
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magestore\Giftvoucher\Observer\Frontend\PredispatchCheckoutObserver
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $cart = $this->_objectManager->get('Magento\Checkout\Model\Session');

        $result = $this->_objectManager->create('Magento\Framework\DataObject');
        
        $items = $cart->getQuote()->getAllItems();
        foreach ($items as $item) {
            $code = 'recipient_ship';
            $codeSendFriend = 'send_friend';
            $option = $item->getOptionByCode($code);
            $option2 = $item->getOptionByCode($codeSendFriend);
            if ($option && $option2) {
                $data = $option->getData();
            }

            if (isset($data['value']) && $data['value'] != null) {
                $result->setData(
                    'error_messages',
                    __('You need to add your friend\'s address as the shipping address. We will send this gift card to that address.')
                );
                return $this->resultJsonFactory->create()->setData($result->getData());
            }
        }

        if ($cart->getQuote()->getCouponCode() && !$this->_helperData->getGeneralConfig('use_with_coupon')
            && ($cart->getQuote()->getGiftVoucherDiscount() > 0)) {
            $this->_sessionManager->setMessageApplyGiftcardWithCouponCode(false);
            $this->messageManager->addNotice(__('A coupon code has been used. You cannot apply gift codes with the coupon to get discount.'));
        }
        return;
    }
}
