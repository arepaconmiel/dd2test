<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model\Source\Giftvoucher;

/**
 * Class CurrencyOptions
 * @package Magestore\Giftvoucher
 */
class CurrencyOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currencyModel;
    
    /**
     * @var \Magento\Framework\Locale\Bundle\CurrencyBundle
     */
    protected $_currencyBundle;
    
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_locale;

    /**
     * CurrencyOptions constructor.
     * @param \Magento\Framework\Locale\Bundle\CurrencyBundle $currencyBundle
     * @param \Magento\Directory\Model\Currency $currencyModel
     * @param \Magento\Framework\Locale\ResolverInterface $locale
     */
    public function __construct(
        \Magento\Framework\Locale\Bundle\CurrencyBundle $currencyBundle,
        \Magento\Directory\Model\Currency $currencyModel,
        \Magento\Framework\Locale\ResolverInterface $locale
    ) {
        $this->_currencyBundle = $currencyBundle;
        $this->_currencyModel = $currencyModel;
        $this->_locale = $locale;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\Data\OptionSourceInterface::toOptionArray()
     */
    public function toOptionArray()
    {
        $allowedCurrencies = $this->_currencyModel->getConfigAllowCurrencies();
        $currencies = $this->_currencyBundle->get($this->_locale->getLocale())['Currencies'];
        
        $options = [];
        foreach ($currencies as $code => $data) {
            if (!in_array($code, $allowedCurrencies)) {
                continue;
            }
            $options[] = ['label' => $data[1], 'value' => $code];
        }
        
        return $options;
    }
}
