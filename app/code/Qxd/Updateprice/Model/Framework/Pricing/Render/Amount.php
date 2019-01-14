<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qxd\Updateprice\Model\Framework\Pricing\Render;

use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Pricing\Price\ConfiguredPriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Catalog\Pricing\Price\FinalPrice;


class Amount extends \Magento\Framework\Pricing\Render\Amount
{	
	public function __construct(
        Template\Context $context,
        AmountInterface $amount,
        PriceCurrencyInterface $priceCurrency,
        RendererPool $rendererPool,
        SaleableInterface $saleableItem = null,
        PriceInterface $price = null,
        array $data = [],
        \Magento\Catalog\Pricing\Price\ConfiguredPriceSelection $configuredPriceSelection = null,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        parent::__construct($context, $amount, $priceCurrency, $rendererPool, $saleableItem, $price, $data);
        $this->amount = $amount;
        $this->configuredPriceSelection = $configuredPriceSelection;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_resource = $resource;
    }
	// fucntion to get deafult ranges
    public function getCustomPriceConfig () {
        //qxd testing lo de los productos */
    	$product = $this->getSaleableItem();
        $qxd_config = null;

        /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/configurable.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($product->getName(), true));
        $logger->info(print_r($product->getId(), true));*/
        switch ($product->getTypeId()) {
            case 'bundle':     
                //$bundle_block= $this->getLayout()->createBlock('Magento\Bundle\Block\Catalog\Product\View\Type\Bundle');

                //$qxd_config = $bundle_block->getJsonConfig();
                break;

            case 'configurable':

                $qxd_config = $price_config = json_decode($product->getPriceRangeConfig(), true);
                /*$configurable_block= $this->getLayout()->createBlock('Magento\ConfigurableProduct\Block\Product\View\Type\Configurable',
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

                    $qxd_config['testing'] = $this->hasSpecialPrice();


                }*/
                break;
            
            default:
                break;
        }

        return $qxd_config;
    }

    public function testop(){
        return $this->getRequest()->getFullActionName() ;
    }

    public function isProductPage(){
        return ($this->getRequest()->getFullActionName() == 'catalog_product_view' || $this->getRequest()->getFullActionName() == 'checkout_cart_configure') ? true : false;
    }

    public function wrapPrice($price){
        return '<span class="price">'.$this->formatPrice($price).'</span>';
    }

    public function formatPrice($price){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');

        return $priceHelper->currency($price , true, false);
    }


    public function getCustomPriceType($priceCode)
    {
        return $this->saleableItem->getPriceInfo()->getPrice($priceCode);
    }

    public function parentHasSpecialPrice()
    {
        $displayRegularPrice = $this->getCustomPriceType(RegularPrice::PRICE_CODE)->getAmount()->getValue();
        $displayFinalPrice = $this->getCustomPriceType(FinalPrice::PRICE_CODE)->getAmount()->getValue();
        return $displayFinalPrice < $displayRegularPrice;
    }

    public function hasSpecialPrice()
    {	
    	$product = $this->getSaleableItem();
        if ($this->price->getPriceCode() == ConfiguredPriceInterface::CONFIGURED_PRICE_CODE && $product->getTypeId() == 'configurable') {
            $displayRegularPrice = $this->getConfiguredRegularPrice()->getAmount()->getValue();
            $displayFinalPrice = $this->getConfiguredPrice()->getAmount()->getValue();
            return $displayFinalPrice < $displayRegularPrice;
        }
        return $this->parentHasSpecialPrice();
    }

    public function getConfiguredRegularPrice()
    {
        /** @var \Magento\Bundle\Pricing\Price\ConfiguredPrice $configuredPrice */
        $configuredPrice = $this->getCustomPriceType(ConfiguredPriceInterface::CONFIGURED_REGULAR_PRICE_CODE);
        if (empty($this->configuredPriceSelection->getSelectionPriceList($configuredPrice))) {
            // If there was no selection we must show minimal regular price
            return $this->getSaleableItem()->getPriceInfo()->getPrice(RegularPrice::PRICE_CODE);
        }

        return $configuredPrice;
    }


    public function getConfiguredPrice()
    {
        /** @var \Magento\Bundle\Pricing\Price\ConfiguredPrice $configuredPrice */
        $configuredPrice = $this->getPrice();
        if (empty($this->configuredPriceSelection->getSelectionPriceList($configuredPrice))) {
            // If there was no selection we must show minimal regular price
            return $this->getSaleableItem()->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE);
        }

        return $configuredPrice;
    }

}   
