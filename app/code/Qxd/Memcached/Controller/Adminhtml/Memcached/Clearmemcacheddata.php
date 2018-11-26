<?php

namespace Qxd\Memcached\Controller\Adminhtml\Memcached;

class Clearmemcacheddata extends \Magento\Backend\App\Action
{   

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Qxd\Memcached\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_resultJsonFactory = $resultJsonFactory;
    }

    /*  
    * This fucntion will clear some memcached keys.
    */

    public function execute()
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_Memcached-Error.log');
        $logger = new \Zend\Log\Logger();
        
        try{
            $_memcached = $this->_helper->initMemcached();
            $result = $this->_resultJsonFactory->create();
            if($_memcached){

                $_memcachedKeys=$_memcached->getMulti(array('minimumAmountWarning_cart','_menuDefault','wishlist_cart','tax_cart','_stalker','_homeBrands','_quickMessage'));
                foreach($_memcachedKeys as $k=>$data) { if($data){ $_memcached->delete($k); } }
            }

            $result->setData(['status' => '200', 'data' => "Blocks cleared"]);
            return $result; 

        } catch (Exception $e)
        {
            $logger->info(print_r($e->getMessage(), true));
            $result->setData(['status' => '500', 'data' => "Error, Please try again"]);
            return $result; 
        }
    }
}
