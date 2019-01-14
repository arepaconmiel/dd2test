<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Model\Indexer;

use Amasty\Xsearch\Block\Search\AbstractSearch;

class ElasticExternalProvider
{
    const FULLTEXT_INDEX_FIELD = 'fulltext_index';
    const BLOCK_TYPE_FIELD = 'block_type';

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $appEmulation;

    /**
     * @var Amasty\Xsearch\Block\Search\AbstractSearchFactory[]
     */
    private $sources;

    public function __construct(
        \Magento\Store\Model\App\Emulation $appEmulation,
        array $sources = []
    ) {
        $this->appEmulation = $appEmulation;
        $this->sources = $sources;
    }

    /**
     * @param $storeId
     * @return array
     */
    public function get($storeId)
    {
        $this->appEmulation->startEnvironmentEmulation($storeId);
        $result = [];
        foreach ($this->sources as $source) {
            /** @var AbstractSearch $block */
            $block = $source->create();
            $block->setLimit(0);
            $result = $this->setDocument($block, $result);
        }

        $this->appEmulation->stopEnvironmentEmulation();

        return $result;
    }

    /**
     * @param $block
     * @param $result
     * @return array
     */
    private function setDocument($block, $result)
    {
        $documents = $block->getResults();
        if ($documents) {
            $fulltextValues = $block->getIndexFulltextValues();
            foreach ($documents as $id => &$document) {
                $document[self::BLOCK_TYPE_FIELD] = $block->getBlockType();
                $document[self::FULLTEXT_INDEX_FIELD] = $fulltextValues[$id];
                $result[] = $document;
            }
        }

        return $result;
    }
}
