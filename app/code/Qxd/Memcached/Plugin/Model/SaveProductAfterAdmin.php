<?php
namespace Qxd\Memcached\Plugin\Model;
 
class SaveProductAfterAdmin
{	

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Model\Product $product,
        \Qxd\Memcached\Helper\Data $memcached
    ) {
        $this->_request = $context->getRequest();
        $this->_response = $context->getResponse();
        $this->_product = $product;
        $this->_memcached = $memcached;
    }

    /*
    *   Clear memcached key related with the product
    */

    public function afterExecute(
        \Magento\Catalog\Controller\Adminhtml\Product\Save $subject, 
        $result
    )
    {

        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test_product_observer2.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('observer before save product');*/
        $productId = $this->_request->getParam('id');
        $product = $this->_product->load($productId);
        //$logger->info($productId);

        $_memcached = $this->_memcached->initMemcached();
        //$logger->info(print_r($_memcached,true));
        if($_memcached){
            $_memcachedKeys=$_memcached->getMulti(array($productId.'_QA','cart_item_'.$productId,$productId.'_media',$productId.'_reviewsContent',$productId.'_reviewSummary',$productId.'_description',$productId.'_attributes',$productId.'_scrollOptions','cart_item_'.$productId));

            foreach($_memcachedKeys as $k=>$data) { if($data){ $_memcached->delete($k); } }
        }

        return $result;
    }
}