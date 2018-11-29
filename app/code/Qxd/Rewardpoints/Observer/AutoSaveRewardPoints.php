<?php

namespace Qxd\Rewardpoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class AutoSaveRewardPoints implements ObserverInterface
{

    public function __construct(
        \Qxd\Rewardpoints\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }

    /*
    * This observer is used to save the cateogry images in s3 when a category is saved 
    * or updated in the admin panel.
    */

    public function execute(Observer $observer)
    {   

        $product = $observer->getProduct();
        $productId = $product->getId();

        $type = $product->getTypeId();
        $price = $this->_helper->returnRewardPointsForProducts($product);

        if($type == "simple" || $type == "configurable" || $type == "giftvoucher") { 
            $product->setRewardPoints($price);
        }
        if($type == "bundle") {
            if($product->getPriceType() == 1){ 
                $product->setRewardPoints($price);
            }
            else{
                $bundle_price = $this->_helper->getPriceBundle($product);
                $product->setRewardPoints($bundle_price);
            }
        }

        $product->save();
    }
}

