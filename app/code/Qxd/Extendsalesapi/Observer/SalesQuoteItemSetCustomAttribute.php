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
        $quoteItem = $observer->getQuoteItem();
        $product = $observer->getProduct();

        $aux_product = $this->_productloader->create()->load($product->getId());

        $allow_zero_price=0;
        if($aux_product->getAllowZeroPrice()){ 
            $allow_zero_price=$aux_product->getAllowZeroPrice();
        }

        $quoteItem->setAllowZeroPrice($allow_zero_price);
    }
}

