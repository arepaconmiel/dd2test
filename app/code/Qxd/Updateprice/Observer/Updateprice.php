<?php

namespace Qxd\Updateprice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTime;


class Updateprice implements ObserverInterface
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
            return $this;
        }catch (Exception $e){ 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_Updateprice_Error.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}

