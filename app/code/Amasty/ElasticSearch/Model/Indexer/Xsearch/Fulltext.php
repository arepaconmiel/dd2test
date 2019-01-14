<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Indexer\Xsearch;

use Magento\Framework\Indexer\ActionInterface;
use Amasty\ElasticSearch\Model\Client\Elasticsearch as Client;

class Fulltext  implements ActionInterface
{
    /**
     * @var \Magento\Framework\Indexer\CacheContext
     */
    private $cacheContext;

    /**
     * @var Client
     */
    private $elasticClient;

    public function __construct(
        \Magento\Framework\Indexer\CacheContext $cacheContext,
        Client $elasticClient
    ) {
        $this->cacheContext = $cacheContext;
        $this->elasticClient = $elasticClient;
    }

    /**
     * @inheritdoc
     */
    public function executeFull()
    {
        $this->elasticClient->saveExternal();
    }

    /**
     * @inheritdoc
     */
    public function executeRow($id)
    {
        $this->executeFull();
    }

    /**
     * @inheritdoc
     */
    public function executeList(array $ids)
    {
        $this->executeFull();
    }
}
