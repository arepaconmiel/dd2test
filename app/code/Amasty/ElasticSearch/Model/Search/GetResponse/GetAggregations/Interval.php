<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ElasticSearch
 */


namespace Amasty\ElasticSearch\Model\Search\GetResponse\GetAggregations;

use Magento\Framework\Search\Dynamic\IntervalInterface;
use Amasty\ElasticSearch\Model\Config;
use Magento\CatalogSearch\Model\Indexer\Fulltext;
use Amasty\ElasticSearch\Model\Client\ClientRepositoryInterface;

class Interval implements IntervalInterface
{
    /**
     * Minimal possible value
     */
    const DELTA = 0.005;

    /**
     * @var Config
     */
    private $clientConfig;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $storeId;

    /**
     * @var array
     */
    private $entityIds;

    /**
     * @var ClientRepositoryInterface
     */
    private $clientRepository;

    public function __construct(
        Config $clientConfig,
        ClientRepositoryInterface $clientRepository,
        $fieldName,
        $storeId,
        $entityIds
    ) {
        $this->clientConfig = $clientConfig;
        $this->fieldName = $fieldName;
        $this->storeId = $storeId;
        $this->entityIds = $entityIds;
        $this->clientRepository = $clientRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function load($limit, $offset = null, $lower = null, $upper = null)
    {
        $fromValue = ($lower) ? ['gte' => $lower - self::DELTA] : [];
        $toValue = ($upper) ? ['lt' => $upper - self::DELTA] : [];

        $client = $this->clientRepository->get();

        $requestQuery = [
            'index' => $client->getIndexName(Fulltext::INDEXER_ID, $this->storeId),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'stored_fields' => [
                    '_id'
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            'match_all' => ['boost' => 1]
                        ],
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'terms' => [
                                            '_id' => $this->entityIds,
                                        ],
                                    ],
                                    [
                                        'range' => [
                                            $this->fieldName => array_merge($fromValue, $toValue),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'sort' => [
                    $this->fieldName,
                ],
                'size' => $limit,
            ],
        ];

        if ($offset) {
            $requestQuery['body']['from'] = $offset;
        }

        $queryResult =  $client->search($requestQuery);

        return $this->convertToFloat($queryResult['hits']['hits']);
    }

    /**
     * {@inheritdoc}
     */
    public function loadPrevious($data, $index, $lower = null)
    {
        $fromValue = ($lower) ? ['gte' => $lower - self::DELTA] : [];
        $toValue = ($data) ? ['lt' => $data - self::DELTA] : [];

        $client = $this->clientRepository->get();

        $requestQuery = [
            'index' => $client->getIndexName(Fulltext::INDEXER_ID, $this->storeId),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'stored_fields' => [
                    '_id'
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            'match_all' => ['boost' => 1]
                        ],
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'terms' => [
                                            '_id' => $this->entityIds,
                                        ],
                                    ],
                                    [
                                        'range' => [
                                            $this->fieldName => array_merge($fromValue, $toValue),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'sort' => [
                    $this->fieldName,
                ],
            ],
        ];
        $queryResult =  $client->search($requestQuery);

        $offset = $queryResult['hits']['total'];
        if (!$offset) {
            return false;
        }

        return $this->load($index - $offset + 1, $offset - 1, $lower);
    }

    /**
     * {@inheritdoc}
     */
    public function loadNext($data, $rightIndex, $upper = null)
    {
        $fromValue = ($data) ? ['gte' => $data - self::DELTA] : [];
        $toValue = ($data) ? ['lt' => $data - self::DELTA] : [];

        $client = $this->clientRepository->get();
        $requestCountQuery = [
            'index' => $client->getIndexName(Fulltext::INDEXER_ID, $this->storeId),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'stored_fields' => [
                    '_id'
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            'match_all' => ['boost' => 1]
                        ],
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'terms' => [
                                            '_id' => $this->entityIds,
                                        ],
                                    ],
                                    [
                                        'range' => [
                                            $this->fieldName => array_merge($fromValue, $toValue),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'sort' => [
                    $this->fieldName,
                ],
            ],
        ];
        $queryCountResult = $client->search($requestCountQuery);

        $offset = $queryCountResult['hits']['total'];
        if (!$offset) {
            return false;
        }

        $fromValue = ['gte' => $data - self::DELTA];
        if ($upper !== null) {
            $toValue = ['lt' => $data - self::DELTA];
        }

        $requestQuery = $requestCountQuery;
        $requestCountQuery['body']['query']['filtered']['filter']['bool']['must']['range'] =
            [$this->fieldName => array_merge($fromValue, $toValue)];

        $requestCountQuery['body']['from'] = $offset - 1;
        $requestCountQuery['body']['size'] = $rightIndex - $offset + 1;

        $queryResult = $this->clientRepository->get()->search($requestQuery);

        return array_reverse($this->convertToFloat($queryResult['hits']['hits']));
    }

    /**
     * @param array $hits
     * @param string $fieldName
     *
     * @return float[]
     */
    private function convertToFloat($hits)
    {
        $returnPrices = [];
        foreach ($hits as $hit) {
            $returnPrices[] = (float) $hit['sort'][0];
        }

        return $returnPrices;
    }
}
