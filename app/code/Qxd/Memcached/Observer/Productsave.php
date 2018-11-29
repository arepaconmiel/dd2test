<?php
namespace Qxd\Memcached\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Productsave implements ObserverInterface
{
 	public function __construct(
        \Qxd\Memcached\Helper\Data $helper

    ) {
        $this->_helper = $helper;
    }


    /*
    *   Clear memcached key related with the category
    */

    public function execute(Observer $observer)
    {
        
        $product = $observer->getProduct();
        $productId = $product->getId();

        $_memcached = $this->_helper->initMemcached();
  
        if($_memcached){
            $_memcachedKeys=$_memcached->getMulti(array($productId.'_QA','cart_item_'.$productId,$productId.'_media',$productId.'_reviewsContent',$productId.'_reviewSummary',$productId.'_description',$productId.'_attributes',$productId.'_scrollOptions','cart_item_'.$productId));

            foreach($_memcachedKeys as $k=>$data) { if($data){ $_memcached->delete($k); } }
        }
    }
}