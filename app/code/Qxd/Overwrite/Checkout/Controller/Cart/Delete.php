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

                $quoteItem = null;
                if($this->_checkoutSession->getConfigurableDiscountCurrentSku()){
                    $quoteItem = $this->cart->getQuote()->getItemById($id);
                }

                $this->cart->removeItem($id)->save();

                $cartCouponCode = $this->cart->getQuote()->getCouponCode();

                $couponRequired=$this->_scopeConfig->getValue(
	                'buyxgetyfree_section4/general/coupon_required',
	                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
	            );

                if(strtolower($cartCouponCode) === strtolower($couponRequired)){  
                	$this->_checkoutSession->setCartGiftRemoved(true); 
                }

                // check if a product with a discoutn is eliminated from the cart (salesrule module)
                if($this->_checkoutSession->getConfigurableDiscountCurrentSku() && $quoteItem){

                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_discount.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info(print_r('pase', true));
                    $logger->info(print_r($quoteItem->getSku(), true));

                    if($this->_checkoutSession->getConfigurableDiscountCurrentSku() == $quoteItem->getSku()){
                        $logger->info(print_r('refresh', true));
                        $this->_checkoutSession->unsConfigurableDiscountAlreadyApplied();
                        $this->_checkoutSession->unsConfigurableDiscountCurrentSku();
                    }
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