<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qxd\Overwrite\Checkout\Controller\Cart;

class Delete extends \Magento\Checkout\Controller\Cart\Delete
{
	/**
     * Delete shopping cart item action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->cart->removeItem($id)->save();

                $cartCouponCode = $this->cart->getQuote()->getCouponCode();

                $couponRequired=$this->_scopeConfig->getValue(
	                'buyxgetyfree_section4/general/coupon_required',
	                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
	            );

                if(strtolower($cartCouponCode) === strtolower($couponRequired)){  
                	$this->_checkoutSession->setCartGiftRemoved(true); 
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t remove the item.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        }
        $defaultUrl = $this->_objectManager->create(\Magento\Framework\UrlInterface::class)->getUrl('*/*');
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($defaultUrl));
    }
}