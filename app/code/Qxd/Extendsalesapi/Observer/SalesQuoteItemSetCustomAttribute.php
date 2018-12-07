<?php

namespace Qxd\Extendsalesapi\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class SalesQuoteItemSetCustomAttribute implements ObserverInterface
{   

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $_productloader
    )
    {
        $this->_productloader = $_productloader;
    }

    public function execute(Observer $observer)
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/api_sales.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();

        $logger->info('entre');
        $logger->info(print_r($product->getName(),true));
        $logger->info(print_r($product->getId(),true));

        $aux_product = $this->_productloader->create()->load($product->getId());

        $logger->info(print_r($aux_product->getAllowZeroPrice(),true));

        $allow_zero_price=0;
        if($aux_product->getAllowZeroPrice()){ 
            $allow_zero_price=$aux_product->getAllowZeroPrice();
        }

        $logger->info(print_r($allow_zero_price,true));

        $quoteItem->setAllowZeroPrice($allow_zero_price);
    }
}

