<?php

namespace Qxd\Onsale\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTime;


class AddToSaleCategory implements ObserverInterface
{

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Qxd\Onsale\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->_category = $categoryFactory;
        $this->_helper = $helper;
        $this->_productFactory = $productFactory;
    }

    /*
    * This observer assign the product to the sale category if it has a special price lower than the 
    * regular price
    * 
    */

    public function execute(Observer $observer)
    {   
        try
        {
            $product = $observer->getProduct();
            $currentDateTime = date('Y-m-d H:i:s');
            $saleCategory = $this->_category->create()
            ->getCollection()->addAttributeToFilter('url_key','sale')->getFirstItem();

            $final_product = $this->_productFactory->create()->load($product->getId());
            $this->_helper->processProduct($final_product,$currentDateTime,$saleCategory);
        }catch (Exception $e){ 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_Onsale_Error.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}

