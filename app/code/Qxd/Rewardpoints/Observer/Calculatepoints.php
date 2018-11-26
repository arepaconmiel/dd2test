<?php

namespace Qxd\Rewardpoints\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTime;


class Calculatepoints implements ObserverInterface
{

    public function __construct(
        \Magento\Sales\Model\Order $order, 
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->_product = $productFactory;
        $this->_date = $date;
        $this->_storeManager=$storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_registry = $registry;
        $this->_messageManager = $messageManager;
        $this->_order = $order;
    }

    /*
    * This observer is used to save the cateogry images in s3 when a category is saved 
    * or updated in the admin panel.
    */

    public function execute(Observer $observer)
    {   

        $order_id = $observer->getEvent()->getOrderIds()[0];
        $order = $this->_order->load($order_id);

        if($order->getCustomerIsGuest() == '0'){
            $totalPoints=0;
            $orderedProductIds = array();

            foreach($order->getAllVisibleItems() as $item)
            {   
                $product = $this->_product->create();
                $product->load($product->getIdBySku($item->getSku()));
                if($product->getData()) { $totalPoints=$totalPoints + ( intval($product->getRewardPoints()) * intval($item->getQtyOrdered()) ); }
            }
            $order->setRewardPoints($totalPoints);
            $order->save();
        }
    }
}

