<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Repository;

use Magestore\Giftvoucher\Api\Data\GiftCodePatternInterface;
use Magestore\Giftvoucher\Api\Data\GiftCodePatternInterfaceFactory;
use Magestore\Giftvoucher\Api\Data\GiftCodePatternSearchResultsInterfaceFactory;
use Magestore\Giftvoucher\Api\GiftCodePatternRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magestore\Giftvoucher\Model\GiftCodePatternFactory as GiftCodePatternFactory;
use Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern as ResourceModel;
use Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\CollectionFactory as CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class GiftCodePatternRepository
 * @package Magestore\Giftvoucher\Model\Repository
 */
class GiftCodePatternRepository implements GiftCodePatternRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $resource;

    /**
     * @var GiftCodePatternFactory
     */
    protected $modelFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var GiftCodePatternSearchResultsInterfaceFactory
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
     * @var GiftCodePatternInterfaceFactory
     */
    protected $dataModelFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;


    /**
     * GiftCodePatternRepository constructor.
     * @param ResourceModel $resource
     * @param GiftCodePatternFactory $modelFactory
     * @param GiftCodePatternSearchResultsInterfaceFactory $dataModelFactory
     * @param CollectionFactory $collectionFactory
     * @param GiftCodePatternSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceModel $resource,
        GiftCodePatternFactory $modelFactory,
        GiftCodePatternSearchResultsInterfaceFactory $dataModelFactory,
        CollectionFactory $collectionFactory,
        GiftCodePatternSearchResultsInterfaceFactory $searchResultsFactory,
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
     * Save Block data
     *
     * @param GiftCodePatternInterface $giftCodePattern
     * @return Block
     * @throws CouldNotSaveException
     */
    public function save(GiftCodePatternInterface $giftCodePattern)
    {
        try {
            $this->resource->save($giftCodePattern);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $giftCodePattern;
    }

    /**
     * Load data by given Identity
     *
     * @param string $id
     * @return Block
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $item = $this->modelFactory->create();
        $this->resource->load($item, $id);
        if (!$item->getId()) {
            throw new NoSuchEntityException(__('Gift Card Template with id "%1" does not exist.', $id));
        }
        return $item;
    }

    /**
     * Load data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\Collection
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
        /** @var Magestore\Giftvoucher\Model\GiftCodePattern $item */
        foreach ($collection as $item) {
            $modelData = $this->dataModelFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $modelData,
                $item->getData(),
                'Magestore\Giftvoucher\Api\Data\GiftCodePatternInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $modelData,
                'Magestore\Giftvoucher\Api\Data\GiftCodePatternInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * Delete Gift Template
     *
     * @param GiftCodePatternInterface $giftCodePattern
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(GiftCodePatternInterface $giftCodePattern)
    {
        try {
            $this->resource->delete($giftCodePattern);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Item by given Identity
     *
     * @param string $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
