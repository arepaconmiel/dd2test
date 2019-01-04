<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qxd\Overwrite\Checkout\Controller\Cart;

use Magento\Checkout\Model\Cart\RequestQuantityProcessor;

class UpdatePost extends \Magento\Checkout\Controller\Cart\UpdatePost
{   

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        RequestQuantityProcessor $quantityProcessor = null
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quantityProcessor
        );

        $this->customQuantityProcessor = $quantityProcessor ?: $this->_objectManager->get(RequestQuantityProcessor::class);
    }


	/**
     * Update customer's shopping cart
     *
     * @return void
     */
    public function _updateCustomShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                if (!$this->cart->getCustomerSession()->getCustomerId() && $this->cart->getQuote()->getCustomerId()) {
                    $this->cart->getQuote()->setCustomerId(null);
                }
                $cartData = $this->customQuantityProcessor->process($cartData);
                $cartData = $this->cart->suggestItemsQty($cartData);
                $this->cart->updateItems($cartData)->save();

                $cartCouponCode = $this->cart->getQuote()->getCouponCode();
                $couponRequired = $this->_scopeConfig->getValue(
	                'buyxgetyfree_section4/general/coupon_required',
	                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
	            );

                if(strtolower($cartCouponCode) === strtolower($couponRequired)) { 
                	$this->_checkoutSession->setCartGiftRemoved(true); 
                }
            }

            $this->_checkoutSession->setCartActionUpdate(true);

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(
                $this->_objectManager->get(\Magento\Framework\Escaper::class)->escapeHtml($e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t update the shopping cart.'));
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
    }


	/**
     * Update shopping cart data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateCustomShoppingCart();
                break;
            default:
                $this->_updateCustomShoppingCart();
        }

        return $this->_goBack();
    }

}