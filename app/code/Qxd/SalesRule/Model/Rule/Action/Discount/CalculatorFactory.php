<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qxd\SalesRule\Model\Rule\Action\Discount;

class CalculatorFactory extends \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory
{
    
    protected $classByTypeCustom = [
        \Magento\SalesRule\Model\Rule::TO_PERCENT_ACTION =>
            \Magento\SalesRule\Model\Rule\Action\Discount\ToPercent::class,
        \Magento\SalesRule\Model\Rule::BY_PERCENT_ACTION =>
            \Magento\SalesRule\Model\Rule\Action\Discount\ByPercent::class,
        \Magento\SalesRule\Model\Rule::TO_FIXED_ACTION => \Magento\SalesRule\Model\Rule\Action\Discount\ToFixed::class,
        \Magento\SalesRule\Model\Rule::BY_FIXED_ACTION => \Magento\SalesRule\Model\Rule\Action\Discount\ByFixed::class,
        \Magento\SalesRule\Model\Rule::CART_FIXED_ACTION =>
            \Magento\SalesRule\Model\Rule\Action\Discount\CartFixed::class,
        \Magento\SalesRule\Model\Rule::BUY_X_GET_Y_ACTION =>
            \Magento\SalesRule\Model\Rule\Action\Discount\BuyXGetY::class,
        \Qxd\SalesRule\Model\Rule::BUY_X_AND_Y_GET_CONFIGURABLE_ACTION =>
            \Qxd\SalesRule\Model\Rule\Action\Discount\BuyXGetYConfigurable::class,
    ];

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $discountRules
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, array $discountRules = [])
    {   
        parent::__construct(
            $objectManager,
            $discountRules
        );
        $this->classByTypeCustom = array_merge($this->classByTypeCustom, $discountRules);
    }

    /**
     * @param string $type
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\DiscountInterface
     * @throws \InvalidArgumentException
     */
    public function create($type)
    {
        if (!isset($this->classByTypeCustom[$type])) {
            throw new \InvalidArgumentException($type . ' is unknown type');
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        return $objectManager->create($this->classByTypeCustom[$type]);
    }
}
