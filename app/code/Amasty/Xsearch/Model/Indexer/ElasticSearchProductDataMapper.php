<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Model\Indexer;

use Amasty\Xsearch\Controller\RegistryConstants;

class ElasticSearchProductDataMapper
{
    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $appEmulation;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var Amasty\Xsearch\Block\Search\ProductFactory
     */
    private $productBlockFactory;

    public function __construct(
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Framework\App\State $appState,
        \Amasty\Xsearch\Block\Search\ProductFactory $productBlockFactory
    ) {
        $this->appEmulation = $appEmulation;
        $this->appState = $appState;
        $this->productBlockFactory = $productBlockFactory;
    }

    /**
     * @param array $documentData
     * @param $storeId
     * @param array $context
     * @return array
     */
    public function map(array $documentData, $storeId, array $context = [])
    {
        $this->appEmulation->startEnvironmentEmulation($storeId);
        $productsData = $this->appState->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_FRONTEND,
            [$this, 'getProductSearchData'],
            [array_keys($documentData)]
        );
        $this->appEmulation->stopEnvironmentEmulation();
        $productsData = array_map(function ($product) {
            return [RegistryConstants::INDEX_ENTITY_TYPE => $product];
        }, $productsData);

        return $productsData;
    }

    /**
     * @param array $productIds
     * @return array
     */
    public function getProductSearchData(array $productIds)
    {   
        return $this->productBlockFactory->create()
            ->setLimit(0)
            ->setIndexedIds($productIds)
            ->getResults();
    }
}
