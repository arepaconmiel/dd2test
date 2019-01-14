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
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurable

    ) {
        $this->_category = $categoryFactory;
        $this->_helper = $helper;
        $this->_productFactory = $productFactory;
        $this->_layoutFactory = $layoutFactory;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_configurable = $configurable;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_Updateprice_Error.log');
        $this->_logger = new \Zend\Log\Logger();
        $this->_logger->addWriter($writer);
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

            $layout = $this->_layoutFactory->create();

            $productObserver = $observer->getProduct();
            $productId = $productObserver->getId();

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
            return $this;
        }catch (Exception $e){ 
            
            $this->_logger->info($e->getMessage());
        }
    }
}

