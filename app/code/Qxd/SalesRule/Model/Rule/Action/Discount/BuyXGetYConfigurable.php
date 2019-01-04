<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qxd\SalesRule\Model\Rule\Action\Discount;

class BuyXGetYConfigurable extends \Magento\SalesRule\Model\Rule\Action\Discount\AbstractDiscount
{   

    /**
     * @param \Magento\SalesRule\Model\Validator $validator
     * @param DataFactory $discountDataFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory $discountDataFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct(
            $validator,
            $discountDataFactory,
            $priceCurrency
        );
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_discount.log');
        $this->_logger = new \Zend\Log\Logger();
        $this->_logger->addWriter($writer);
    }
   

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return Data
     */
    public function calculate($rule, $item, $qty)
    {
        $rulePercent = min(100, $rule->getDiscountAmount());
        $discountData = $this->_calculate($rule, $item, $qty, $rulePercent);

        return $discountData;
    }

    /**
     * @param float $qty
     * @param \Magento\SalesRule\Model\Rule $rule
     * @return float
     */
    public function fixQuantity($qty, $rule)
    {
        return 1;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @param float $rulePercent
     * @return Data
     */
    protected function _calculate($rule, $item, $qty, $rulePercent)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        $configurableSkus=explode (",",$this->_scopeConfig->getValue(
            'buyxgetyfree_section4/general/coupon_configurable_skus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));

        /*$this->_logger->info(print_r($item->getSku(), true));
        $this->_logger->info(print_r($this->_checkoutSession->getConfigurableDiscountAlreadyApplied(), true));
        $this->_logger->info(print_r($this->_checkoutSession->getConfigurableDiscountCurrentSku(), true));*/

        $discountData->setAmount(0);
        $discountData->setBaseAmount(0);
        $discountData->setOriginalAmount(0);
        $discountData->setBaseOriginalAmount(0);
       
        if( array_search($item->getSku(),$configurableSkus) !== false)
        {   
            if($this->_checkoutSession->getConfigurableDiscountAlreadyApplied()){
                if($this->_checkoutSession->getConfigurableDiscountCurrentSku() != $item->getSku()){
                    return $discountData;
                }
            }
            
            $qty=1;
            $itemPrice = $this->validator->getItemPrice($item);
            $baseItemPrice = $this->validator->getItemBasePrice($item);
            $itemOriginalPrice = $this->validator->getItemOriginalPrice($item);
            $baseItemOriginalPrice = $this->validator->getItemBaseOriginalPrice($item);

            $_rulePct = 100;
            $discountData->setAmount(($qty * $itemPrice - $item->getDiscountAmount()) * $_rulePct);
            $discountData->setBaseAmount(($qty * $baseItemPrice - $item->getBaseDiscountAmount()) * $_rulePct);
            $discountData->setOriginalAmount(($qty * $itemOriginalPrice - $item->getDiscountAmount()) * $_rulePct);
            $discountData->setBaseOriginalAmount(
                ($qty * $baseItemOriginalPrice - $item->getBaseDiscountAmount()) * $_rulePct
            );

            if (!$rule->getDiscountQty() || $rule->getDiscountQty() > $qty) {
                $discountPercent = 100;
                $item->setDiscountPercent($discountPercent);
                $this->_checkoutSession->setConfigurableDiscountAlreadyApplied(true);
                $this->_checkoutSession->setConfigurableDiscountCurrentSku($item->getSku());
            }
        }

        return $discountData;
    }
}
