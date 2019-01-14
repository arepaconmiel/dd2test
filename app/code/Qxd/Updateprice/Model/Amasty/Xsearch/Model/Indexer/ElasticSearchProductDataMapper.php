<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Qxd\Updateprice\Model\Amasty\Xsearch\Model\Indexer;

use Amasty\Xsearch\Controller\RegistryConstants;

class ElasticSearchProductDataMapper extends \Amasty\Xsearch\Model\Indexer\ElasticSearchProductDataMapper
{
	public function __construct(
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Framework\App\State $appState,
        \Amasty\Xsearch\Block\Search\ProductFactory $productBlockFactory,
        \Qxd\Updateprice\Model\Amasty\Xsearch\Block\Search\ProductFactory $qxdProductBlockFactory
    ) {
        $this->qxdProductBlockFactory = $qxdProductBlockFactory;

        parent::__construct(
            $appEmulation,
            $appState,
            $productBlockFactory
        );
    }


    /**
     * @param array $productIds
     * @return array
     */
    public function getProductSearchData(array $productIds)
    {	
        return $this->qxdProductBlockFactory->create()
            ->setLimit(0)
            ->setIndexedIds($productIds)
            ->getResults();
    }
}