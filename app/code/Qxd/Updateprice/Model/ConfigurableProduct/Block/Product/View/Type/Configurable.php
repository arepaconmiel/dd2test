<?php
/**
 * Catalog super product configurable part block
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qxd\Updateprice\Model\ConfigurableProduct\Block\Product\View\Type;

use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Locale\Format;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @api
 * @since 100.0.2
 */
class Configurable extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{
	public function getProduct(){
        if($this->getData('qxd_custom_product')){
            return $this->getData('qxd_custom_product');
        }else{
            return parent::getProduct();
        }
    }
}