<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Pricing\ConfiguredPrice;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Catalog\Pricing\Price\ConfiguredPriceInterface;
use Magento\Catalog\Pricing\Price\FinalPrice;

/**
 * Class ConfigurableProduct
 * @package Magestore\Giftvoucher\Pricing\ConfiguredPrice
 */
class ConfigurableProduct extends FinalPrice implements ConfiguredPriceInterface
{
    /**
     * @var ItemInterface
     */
    private $item;

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        $result = 0.;
        /** @var \Magento\Wishlist\Model\Item\Option $customOption */
        $customOption = $this->getProduct()->getCustomOption('price_amount');
        if ($customOption) {
            $result = $this->priceCurrency->convert($customOption->getValue(), false, false);
        }

        if ($result == 0) {
            /** @var \Magento\Framework\Pricing\PriceInfoInterface $priceInfo */
            $priceInfo = $this->getProduct()->getPriceInfo();
            $result = $this->priceCurrency->convert($priceInfo->getPrice(self::PRICE_CODE)->getValue(), false, false);
        }

        return max(0, $result);
    }

    /**
     * @inheritdoc
     */
    public function setItem(ItemInterface $item)
    {
        $this->item = $item;
        return $this;
    }
}
