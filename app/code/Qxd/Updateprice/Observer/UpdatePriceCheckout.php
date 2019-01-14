<?php

namespace Qxd\Updateprice\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\DateTime\DateTime;


class UpdatePriceCheckout implements ObserverInterface
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

        // falta probar aca y ademas ver aquella mierda del stock
        $totalPoints=0;
        $orderedProductIds = array();

        foreach($order->getAllVisibleItems() as $item)
        {   
            $product = $this->_product->create();
            $product->load($product->getIdBySku($item->getSku()));
            $parent_ids = $this->_configurable->getParentIdsByChild($productId);
            
            foreach ($parent_ids as $id) {

                $product = $this->_productFactory->create()->load($id);
                if($product->getTypeId() == 'configurable') {

                    $configurable_block= $layout->createBlock('Qxd\Updateprice\Model\ConfigurableProduct\Block\Product\View\Type\Configurable',
                    "",
                    [
                        'data' => [
                            'qxd_custom_product' => $product
                        ]
                    ]);

                    $configurable_config = $configurable_block->getJsonConfig();

                    if($configurable_config){
                        $configurable_config = json_decode($configurable_config, true);
                        $options = $configurable_config["optionPrices"];

                        $first_option = reset($options);
                        $higherRegular = $first_option["oldPrice"]['amount'];
                        $lowerRegular = $first_option["oldPrice"]['amount'];
                        $regularPrice = $first_option["oldPrice"]['amount'];
                        $specialPrices = [];

                        $hasSameRegularPrice = true;
                        $hasSameSpecialPrice = true;

                        $hasSpecialPrice = false;
                        foreach ($options as $price) {
                            //regular

                            //get higher
                            if($price["oldPrice"]['amount'] > $higherRegular)
                                $higherRegular = $price["oldPrice"]['amount'];

                            //get lower
                            if($price["oldPrice"]['amount'] < $lowerRegular)
                                $lowerRegular = $price["oldPrice"]['amount'];

                            if($price["oldPrice"]['amount'] != $regularPrice)
                                $hasSameRegularPrice = false;


                            //special

                            if($price["finalPrice"]['amount'] < $price["oldPrice"]['amount']){
                                $hasSpecialPrice = true;
                                $specialPrices[] = $price["finalPrice"]['amount'];
                            }
                        }



                        if(empty($specialPrices)){
                            $qxd_config['hasSpecialPrice'] = false;
                        }else{
                            $qxd_config['hasSpecialPrice'] = true;
                            sort($specialPrices);
                            $hasSameSpecialPrice = count(array_unique($specialPrices)) == 1;
                            $qxd_config['rangeSpecial'] = ["lower" => $specialPrices[0], "higher" => $specialPrices[count($specialPrices) - 1]];
                        }

                        $qxd_config['rangeRegular'] = ["lower" => $lowerRegular, "higher" => $higherRegular];


                        $qxd_config['hasSameSpecialPrice'] = $hasSameSpecialPrice;
                        $qxd_config['hasSameRegularPrice'] = $hasSameRegularPrice; 

                        $json_config = $this->_jsonEncoder->encode($qxd_config);
                        $product->setPriceRangeConfig($json_config);
                        $product->save();

                    }   
                }
                
            }
        }

        
    }
}

