<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Qxd\Overwrite\Checkout\Controller\Cart;

class Index extends \Magento\Checkout\Controller\Cart\Index
{

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Qxd\Memcached\Helper\Data $memcached,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $resultPageFactory
        );
        $this->customerSession = $customerSession;
        $this->memcached = $memcached;
        $this->_productRepository = $productRepository;
        $this->_stockRegistryInterface = $stockRegistryInterface;
    }

    /**
     * Shopping cart display action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {   

        /*$_excludedCustomerGroupConfig= $this->_scopeConfig->getValue(
            'buyxgetyfree_section1/general/excluded_customer_groups',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $_groupId = (string) $this->customerSession->getCustomerGroupId(); //Get Customers Group ID
        $_excludedCustomerGroups=explode (",",$_excludedCustomerGroupConfig); // get list of excluded groups*/

        $_excludeFromOffer=false;
        /*foreach ($_excludedCustomerGroups as $_excludedCustomerGroup)
        {
            if ($_groupId===$_excludedCustomerGroup) {$_excludeFromOffer=true;} // group is excluded from all offers
        }*/

        if (!$_excludeFromOffer) {
            // Buy X get Y Free
            //$this->buyXgetYfree();
            // Spend X get Y Free
            //$this->spendXgetYfree();
            // Coupon X get Y Free
            $this->couponXgetYfree();
            // Category X get Y Free
            //$this->categoryXgetYfree();
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Shopping Cart'));
        return $resultPage;
    }

    public function couponXgetYfree()
    {
        // Use Coupon X Get Y product/s free/discounted

        $cart = $this->cart;

        if (!$this->cart->getQuote()->getItemsCount()) {
            // cart is empty
            return;
        }

        $couponXGetYFree=array();
        $_memcached = $this->memcached->initMemcached();
        if(!$_memcached || !$_memcached->get('couponXGetYFree'))
        {   
            // Get admin variables for COUPON x get y free
            $couponXGetYFree['couponProductYID']=explode (",",$this->_scopeConfig->getValue(
                'buyxgetyfree_section4/general/coupon_producty_product_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ));
            $couponXGetYFree['couponRequired']=explode (",",$this->_scopeConfig->getValue(
                'buyxgetyfree_section4/general/coupon_required',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ));
            $couponXGetYFree['couponProductYDescription']=explode (",",$this->_scopeConfig->getValue(
                'buyxgetyfree_section4/general/coupon_producty_description',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ));
            $couponXGetYFree['couponCartTotalRequired']=explode (",",$this->_scopeConfig->getValue(
                'buyxgetyfree_section4/general/coupon_cart_total_required',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ));

            if($_memcached){ $_memcached->set('couponXGetYFree',$couponXGetYFree); }
        }
        else{ $couponXGetYFree=$_memcached->get('couponXGetYFree'); }

        // Coupon X get Y Free
        $error="A CouponXGetYFree Extension cart error was detected!";

        try
        {
            $couponProductYID=$couponXGetYFree['couponProductYID'];
            $couponRequired=$couponXGetYFree['couponRequired'];
            $couponProductYDescription=$couponXGetYFree['couponProductYDescription'];
            $couponCartTotalRequired=$couponXGetYFree['couponCartTotalRequired'];

            for($i = 0; $i < count($couponProductYID); $i++){
                if (empty($couponProductYDescription[$i])) {
                    $couponProductYDescription[$i]="free gift";
                }
                if (empty($couponProductYID[$i])) {
                    $couponProductYID[$i]="0";
                }
                if (empty($couponCartTotalRequired[$i])) {
                    $couponCartTotalRequired[$i]="0";
                }
                if (empty($couponRequired[$i])) {
                    // no coupon specified
                    break;
                } else {
                }
                if ($couponProductYID[$i] !="0") {

                    if ($this->isProductYUnique() )
                    {
                        // update the cart for this offer
                        $this->couponXgetYfreeCartUpdate((int)$couponProductYID[$i],$couponRequired[$i],$couponProductYDescription[$i]);
                    } else {
                        $error = "Error in Coupon X configuration - Product Y is not unique across all extension settings.";
                        throw new Exception($error);
                    }
                }
            }

        } catch (Exception $ex) {
            // Catch errors
            $this->addNotificationMessage($cart,'error',$this->__($error));
            $this->sendErrorEmail($error);
        }
    }

    public function isProductYUnique()
    {

        /*if (Mage::getStoreConfig('buyxgetyfree_section3/general/allow_duplicate_product_y'))
        {
            // do nothing, returning true here to allow duplicates will create a nasty cart loop.
        }*/

        // check product Y is unique across all arrays
        /*$buyProductYID = explode (",",Mage::getStoreConfig('buyxgetyfree_section1/general/producty_product_id'));
        $spendProductYID = explode (",",Mage::getStoreConfig('buyxgetyfree_section2/general/spend_producty_product_id'));*/

        $couponProductYID = explode (",",$this->_scopeConfig->getValue(
                'buyxgetyfree_section4/general/coupon_producty_product_id',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ));

        /*$categoryProductYID = explode (",",Mage::getStoreConfig('buyxgetyfree_section5/general/category_producty_product_id'));*/

        //$result = array_merge((array)$buyProductYID, (array)$spendProductYID, (array)$couponProductYID, (array)$categoryProductYID);

        $result = $couponProductYID;

        foreach ($result as $key=>$val )
        {
            if (empty($val)) unset($result[$key] );
        }
        if ($this->isUnique($result) == true )
        {   // product Y must be unique across all offers
            return false;
        } else {
            return true;
        }
    }

    public function isUnique($array)
    {
        return (array_unique($array) != $array);
    }

    public function couponXgetYfreeCartUpdate($productYID,$couponRequired,$productYDesc)
    {
        try{
            $cart = $this->cart;

            $cartCouponCode = $cart->getQuote()->getCouponCode();

            $productYCartItemId = null;

            foreach ($cart->getQuote()->getAllItems() as $item) {
                if ($item->getProduct()->getId() == $productYID) {
                    if ($item->getQty() > 1)
                    {
                        $item->setQty(1);
                        $cart->save();
                    }
                    $productYCartItemId = $item->getItemId();
                }
            }

            if (strtolower($cartCouponCode) === strtolower($couponRequired)) {

                $flagGiftRemoved=$this->_checkoutSession->getCartGiftRemoved();

                if ($productYCartItemId == null && !isset($flagGiftRemoved)) {

                    $product = $this->_productRepository->getById($productYID,false, $this->_storeManager->getStore()->getId());
                   
                    $stockItem = $this->_stockRegistryInterface->getStockItem($productYID);
                    $qty = $stockItem->getQty();

                    if($product->isSaleable()) {
                        $cart->addProduct($product,1);
                        $cart->save();

                        foreach ($cart->getQuote()->getAllItems() as $item)
                        {
                            if ($item->getProduct()->getId() == $productYID)
                            {       
                                $item->setOriginalCustomPrice(0);
                                $item->save();
                                $cart->save();
                            }
                        }

                        session_write_close();

                        $this->_redirect('checkout/cart');
                    } else {
                        if ($qty == 0) {
                            $this->sendErrorEmail($product->getName(). ' '. $this->__('stock quantity is 0 and could not be added to the cart!'));
                            $this->addNotificationMessage($cart,'notice',$productYDesc. ' '. $this->__('is out of stock and cannot be added to the cart!'));
                            session_write_close();
                        } else {
                            $this->sendErrorEmail($product->getName(). ' ' . $this->__('was not saleable and could not be added to the cart!'));
                            $this->addNotificationMessage($cart,'notice',$productYDesc. ' '. $this->__('is out of stock and cannot be added to the cart!'));
                            session_write_close();
                        }
                    }
                }

            } else {
                if ($productYCartItemId != null) {
                    $cart->removeItem($productYCartItemId);
                    $cart->save();

                    session_write_close();
                    $this->_redirect('checkout/cart');
                }
            }
        }catch (Exception $e)
        {   
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_COUPON_ERROR.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r($e->getMessage(), true));
            $this->_checkoutSession->unsCartGiftRemoved();
        }
    }

    public function sendErrorEmail($message)
    {
        if ($this->_scopeConfig->getValue(
            'buyxgetyfree_section3/general/send_alert_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )){
            $message = wordwrap($message, 70);
            $from = "buyxgetyfree@gaiterjones.com";
            $headers = "From: $from";

            mail($this->_scopeConfig->getValue(
                'trans_email/ident_general/email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ), 'Alert from BuyXGetYFree Extension', $message, $headers);
        }
    }

    protected function addNotificationMessage($_cart,$_type='error',$_message)
    {
        $_messages = array_values((array)$this->messageManager->getMessages());
        foreach ($_messages[0] as $_existingMessages) {
            foreach($_existingMessages as $_existingMessage) {
                $_existingMessage = array_values((array)$_existingMessage);

                if ($_existingMessage[1] == $_message) {
                    // If the message is already set, stop here
                    return;
                }
            }
        }

        // clear messages
        $this->messageManager->getMessages(true);

        if ($_type==="success") {
            $this->messageManager->addSuccess($_message);
            return;
        }

        if ($_type==="notice") {
            $this->messageManager->addNotice($_message);
            return;
        }

        $this->messageManager->addError($_message);
    }



    /* The next functions are not used, but I move them here */ 

    /*FUNCTIONALITY TO ADJUST COUPONS*/
    public function buyXgetYfree()
    {
        // BUY X quantity Get Y product/s free/discounted

        $cart = $this->cart;

        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            // cart is empty
            return;
        }

        // Get admin variables for BUY x get y free
        $buyProductXID = explode (",",Mage::getStoreConfig('buyxgetyfree_section1/general/productx_product_id'));
        $buyProductYID = explode (",",Mage::getStoreConfig('buyxgetyfree_section1/general/producty_product_id'));
        $buyProductXminQty = explode (",",Mage::getStoreConfig('buyxgetyfree_section1/general/productx_required_qty'));
        $buyProductXmaxQty = explode (",",Mage::getStoreConfig('buyxgetyfree_section1/general/productx_limit_qty'));
        $buyProductYDescription = explode (",",Mage::getStoreConfig('buyxgetyfree_section1/general/producty_description'));

        $error="A BuyXGetYFree Extension cart error was detected!";

        try
        {
            for($i = 0; $i < count($buyProductXID); $i++){
                if (empty($buyProductYDescription[$i])) {
                    $buyProductYDescription[$i]="free gift";
                }
                if (empty($buyProductXID[$i])) {
                    $buyProductXID[$i]="0";
                }
                if (empty($buyProductYID[$i])) {
                    $buyProductYID[$i]="0";
                }
                if (empty($buyProductXminQty[$i])) {
                    $buyProductXminQty[$i]="999";
                }
                if (empty($buyProductXmaxQty[$i])) { // if no max quantity configured set to 0
                    $buyProductXmaxQty[$i]="0";
                }
                if ($buyProductXID[$i] !="0" && $buyProductYID[$i] !="0") {
                    if ($this->isProductYUnique()) // product Y must be unique
                    {
                        // update the cart for this offer
                        $this->buyXgetYfreeCartUpdate((int)$buyProductXID[$i],(int)$buyProductXminQty[$i],(int)$buyProductYID[$i],$buyProductYDescription[$i],(int)$buyProductXmaxQty[$i]);
                    } else {
                        $error = "Error in Buy X configuration - Product Y is not unique across all extension settings.";
                        throw new Exception($error);
                    }
                }

            }

        } catch (Exception $ex) {
            // Catch errors
            $this->addNotificationMessage($cart,'error',$this->__($error));
            $this->sendErrorEmail($error);
        }
    }

    public function spendXgetYfree()
    {
        // SPEND X quantity Get Y product/s free/discounted

        $cart = $this->_getCart();
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            // cart is empty
            return;
        }


        // Get admin variables for SPEND x get y free
        $spendProductYID = explode (",",Mage::getStoreConfig('buyxgetyfree_section2/general/spend_producty_product_id'));
        $spendCartYLimit = explode (",",Mage::getStoreConfig('buyxgetyfree_section2/general/spend_cart_y_limit'));
        $spendCartTotalRequired = explode (",",Mage::getStoreConfig('buyxgetyfree_section2/general/spend_cart_total_required'));
        $spendProductYDescription = explode (",",Mage::getStoreConfig('buyxgetyfree_section2/general/spend_producty_description'));
        $spendCustomerGroupID = explode (",",Mage::getStoreConfig('buyxgetyfree_section2/general/spend_customer_group_id'));
        $spendExcludedProductsID = Mage::getStoreConfig('buyxgetyfree_section2/general/spend_excluded_products_id');

        if (empty($spendExcludedProductsID)) {
            $spendExcludedProductsID=false;
        } else {
            $spendExcludedProductsID = explode (",",Mage::getStoreConfig('buyxgetyfree_section2/general/spend_excluded_products_id'));
        }

        $error="A SpendXGetYFree Extension cart error was detected!";

        // Spend X amount get Y Product/s free/discounted
        try
        {

            for($i = 0; $i < count($spendProductYID); $i++){
                if (empty($spendProductYDescription[$i])) {
                    $spendProductYDescription[$i]="free gift";
                }
                if (empty($spendProductYID[$i])) {
                    $spendProductYID[$i]="0";
                }
                if (empty($spendCartTotalRequired[$i])) {
                    $spendCartTotalRequired[$i]="50";
                }
                if (empty($spendCartYLimit[$i])) {
                    $spendCartYLimit[$i]="0";
                }
                if ($spendProductYID[$i] !="0") {
                    if ($this->isProductYUnique())
                    {
                        // update the cart for this offer
                        $this->spendXgetYfreeCartUpdate((int)$spendProductYID[$i],(float)$spendCartTotalRequired[$i],(float)$spendCartYLimit[$i],$spendProductYDescription[$i],$spendCustomerGroupID[$i],$spendExcludedProductsID);
                    } else {
                        $error = "Error in Spend X configuration - Product Y is not unique across all extension settings.";
                        throw new Exception($error);
                    }
                }
            }

        } catch (Exception $ex) {
            // Catch errors
            $this->addNotificationMessage($cart,'error',$this->__($error));
            $this->sendErrorEmail($error);
        }
    }

    public function categoryXgetYfree()
    {
        // Use a category as qualifier for bonus product Y

        $cart = $this->_getCart();

        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            // cart is empty
            return;
        }

        // Get admin variables for CATEGORY x get y free
        $categoryProductYID = explode (",",Mage::getStoreConfig('buyxgetyfree_section5/general/category_producty_product_id'));
        $productXcategoryID = explode (",",Mage::getStoreConfig('buyxgetyfree_section5/general/category_id'));
        $categoryProductYDescription = explode (",",Mage::getStoreConfig('buyxgetyfree_section5/general/category_producty_description'));
        $maxQtyProductY = explode (",",Mage::getStoreConfig('buyxgetyfree_section5/general/category_producty_max_qty'));
        $categoryProductXminQty = explode (",",Mage::getStoreConfig('buyxgetyfree_section5/general/category_productx_min_qty'));
        $categoryProductYstep = explode (",",Mage::getStoreConfig('buyxgetyfree_section5/general/category_producty_step'));

        // Category X get Y Free
        $error="A CategoryXGetYFree Extension cart error was detected!";

        try
        {

            for($i = 0; $i < count($categoryProductYID); $i++){
                if (empty($categoryProductYDescription[$i])) {
                    $categoryProductYDescription[$i]="free gift";
                }
                if (empty($categoryProductYID[$i])) {
                    $categoryProductYID[$i]="0";
                }
                // define default value for mimimum X products required in cart to qualify for product Y
                if (empty($categoryProductXminQty[$i])) {
                    $categoryProductXminQty[$i]="1";
                }
                if (empty($categoryProductYstep[$i])) {
                    $categoryProductYstep[$i]=$categoryProductXminQty[$i];
                }
                if (empty($productXcategoryID[$i])) {
                    // no category specified
                    break;
                } else {
                }
                if (empty($maxQtyProductY[$i])) {
                    $maxQtyProductY[$i]="1";
                }
                if ($categoryProductYID[$i] !="0") {
                    if ($this->isProductYUnique() )
                    {
                        // update the cart for this offer
                        $this->categoryXgetYfreeCartUpdate((int)$categoryProductYID[$i],$productXcategoryID[$i],$categoryProductYDescription[$i],$maxQtyProductY[$i],(int)$categoryProductXminQty[$i],$categoryProductYstep[$i]);
                    } else {
                        $error = "Error in Category X configuration - Product Y is not unique across all extension settings.";
                        throw new Exception($error);
                    }
                }
            }

        } catch (Exception $ex) {
            // Catch errors
            $this->addNotificationMessage($cart,'error',$this->__($error));
            $this->sendErrorEmail($error);
        }
    }
}