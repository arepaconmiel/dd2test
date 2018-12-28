<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qxd\Updateprice\Model\Catalog\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Category;

/**
 * Product View block
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
class View extends \Magento\Catalog\Block\Product\View
{

    
    /**
     * Get JSON encoded configuration array which can be used for JS dynamic
     * price calculation depending on product options
     *
     * @return string
     */
    public function getJsonConfig()
    {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();

        if (!$this->hasOptions()) {
            $config = [
                'productId' => $product->getId(),
                'priceFormat' => $this->_localeFormat->getPriceFormat()
            ];
            return $this->_jsonEncoder->encode($config);
        }

        $tierPrices = [];
        $tierPricesList = $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPrice) {
            $tierPrices[] = $tierPrice['price']->getValue();
        }

        //qxd testing lo de los productos */

        $qxd_config = null;

        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/configurable.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($product->getTypeId(),true));
        $logger->info(print_r('nuevo',true));*/

        switch ($product->getTypeId()) {
            case 'bundle':     
                //$bundle_block= $this->getLayout()->createBlock('Magento\Bundle\Block\Catalog\Product\View\Type\Bundle');

                //$qxd_config = $bundle_block->getJsonConfig();
                break;

            case 'configurable':
                $configurable_block= $this->getLayout()->createBlock('Magento\ConfigurableProduct\Block\Product\View\Type\Configurable');

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


                }
                break;
            
            default:
                break;
        }

        $qxd_price = [
            'amount'      => 0,
            'adjustments' => [],
            'data' => $qxd_config
        ];

        $config = [
            'productId'   => $product->getId(),
            'priceFormat' => $this->_localeFormat->getPriceFormat(),
            'prices'      => [
                'oldPrice'   => [
                    'amount'      => $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue(),
                    'adjustments' => []
                ],
                'basePrice'  => [
                    'amount'      => $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount(),
                    'adjustments' => []
                ],
                'finalPrice' => [
                    'amount'      => $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue(),
                    'adjustments' => []
                ]
            ],
            'idSuffix'    => '_clone',
            'tierPrices'  => $tierPrices,
            'qxd_price' => $qxd_price
        ];

        $responseObject = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('catalog_product_view_config', ['response_object' => $responseObject]);
        if (is_array($responseObject->getAdditionalOptions())) {
            foreach ($responseObject->getAdditionalOptions() as $option => $value) {
                $config[$option] = $value;
            }
        }

        return $this->_jsonEncoder->encode($config);
    }

}