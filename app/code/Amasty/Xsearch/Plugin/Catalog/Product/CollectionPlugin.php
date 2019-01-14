<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Plugin\Catalog\Product;

use Amasty\Shopby\Model\ResourceModel\Fulltext\Collection as ShopbyCollection;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as CatalogSearchCollection;
use Magento\Store\Model\ScopeInterface;

class CollectionPlugin
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    private $searchModules = [
        'catalogsearch',
        'amasty_xsearch'
    ];

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param CatalogSearchCollection|ShopbyCollection $subject
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad($subject, $printQuery = false, $logQuery = false)
    {
        if (in_array($this->request->getModuleName(), $this->searchModules) && !$subject->isLoaded() && $this->isEnabled()) {
            $subject->getSelect()->order('stock_status_index.stock_status ' . CatalogSearchCollection::SORT_ORDER_DESC);
            $orders = $subject->getSelect()->getPart(\Zend_Db_Select::ORDER);
            // move from the last to the the first position
            array_unshift($orders, array_pop($orders));
            $subject->getSelect()->setPart(\Zend_Db_Select::ORDER, $orders);
        }

        return [$printQuery, $logQuery];
    }

    /**
     * @return bool
     */
    private function isEnabled()
    {
        return $this->scopeConfig->getValue(
            'amasty_xsearch/product/out_of_stock_last',
            ScopeInterface::SCOPE_STORE
        );
    }
}
