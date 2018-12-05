<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Repository;

use Magestore\Giftvoucher\Api\Data\HistoryInterface;
use Magestore\Giftvoucher\Api\Data\HistoryInterfaceFactory;
use Magestore\Giftvoucher\Api\Data\HistorySearchResultsInterfaceFactory;
use Magestore\Giftvoucher\Api\HistoryRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magestore\Giftvoucher\Model\HistoryFactory as HistoryFactory;
use Magestore\Giftvoucher\Model\ResourceModel\History as ResourceModel;
use Magestore\Giftvoucher\Model\ResourceModel\History\CollectionFactory as CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class HistoryRepository
 * @package Magestore\Giftvoucher\Model\Repository
 */
class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $resource;

    /**
     * @var HistoryFactory
     */
    protected $modelFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var HistorySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var HistoryInterfaceFactory
     */
    protected $dataModelFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;


    /**
     * HistoryRepository constructor.
     * @param ResourceModel $resource
     * @param HistoryFactory $modelFactory
     * @param HistorySearchResultsInterfaceFactory $dataModelFactory
     * @param CollectionFactory $collectionFactory
     * @param HistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceModel $resource,
        HistoryFactory $modelFactory,
        HistorySearchResultsInterfaceFactory $dataModelFactory,
        CollectionFactory $collectionFactory,
        HistorySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataModelFactory = $dataModelFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save history
     *
     * @param HistoryInterface|GiftvoucherInterface $history
     * @return GiftvoucherInterface
     * @throws CouldNotSaveException
     */
    public function save(HistoryInterface $history)
    {
        try {
            $this->resource->save($history);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $history;
    }

    /**
     * Load data by given Identity
     *
     * @param string $id
     * @return HistoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $item = $this->modelFactory->create();
        $this->resource->load($item, $id);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Gift Card with id "%1" does not exist.', $id));
        }
        return $item;
    }

    /**
     * Load data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magestore\Giftvoucher\Api\Data\HistorySearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $items = [];
        /** @var \Magestore\Giftvoucher\Model\Giftvoucher $item */
        foreach ($collection as $item) {
            $modelData = $this->dataModelFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $modelData,
                $item->getData(),
                'Magestore\Giftvoucher\Api\Data\HistoryInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $modelData,
                'Magestore\Giftvoucher\Api\Data\HistoryInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }
}
