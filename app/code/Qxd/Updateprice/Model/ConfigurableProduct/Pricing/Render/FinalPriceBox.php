<?php


namespace Qxd\Updateprice\Model\ConfigurableProduct\Pricing\Render;

use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\ConfigurableProduct\Pricing\Price\ConfigurableOptionsProviderInterface;
use Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\View\Element\Template\Context;


class FinalPriceBox extends \Magento\ConfigurableProduct\Pricing\Render\FinalPriceBox
{

	public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        ConfigurableOptionsProviderInterface $configurableOptionsProvider,
        array $data = [],
        LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider = null,
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null
    ) {
        parent::__construct(
            $context,
            $saleableItem,
            $price,
            $rendererPool,
            $configurableOptionsProvider,
            $data,
            $lowestPriceOptionsProvider,
            $salableResolver,
            $minimalPriceCalculator
        );


        /*$product = $this->getSaleableItem();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/configurable.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r('entrando :D',true));
        $logger->info(print_r($product->getId(),true));*/
    }

    public function test(){
    	return 'jose pablo';
    }

}