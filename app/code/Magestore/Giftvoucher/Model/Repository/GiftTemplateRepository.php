<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Repository;

use Magestore\Giftvoucher\Api\Data\GiftTemplateInterface;
use Magestore\Giftvoucher\Api\Data\GiftTemplateInterfaceFactory;
use Magestore\Giftvoucher\Api\Data\GiftTemplateSearchResultsInterfaceFactory;
use Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magestore\Giftvoucher\Model\GiftTemplateFactory as GiftTemplateFactory;
use Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate as ResourceModel;
use Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory as CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class GiftTemplateRepository
 * @package Magestore\Giftvoucher\Model\Repository
 */
class GiftTemplateRepository implements GiftTemplateRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $resource;

    /**
     * @var GiftTemplateFactory
     */
    protected $modelFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var GiftTemplateSearchResultsInterfaceFactory
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
     * @var GiftTemplateInterfaceFactory
     */
    protected $dataModelFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;


    /**
     * GiftTemplateRepository constructor.
     * @param ResourceModel $resource
     * @param GiftTemplateFactory $modelFactory
     * @param GiftTemplateSearchResultsInterfaceFactory $dataModelFactory
     * @param CollectionFactory $collectionFactory
     * @param GiftTemplateSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceModel $resource,
        GiftTemplateFactory $modelFactory,
        GiftTemplateSearchResultsInterfaceFactory $dataModelFactory,
        CollectionFactory $collectionFactory,
        GiftTemplateSearchResultsInterfaceFactory $searchResultsFactory,
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
     * @param GiftTemplateInterface $giftTemplate
     * @return Block
     * @throws CouldNotSaveException
     * @internal param GiftTemplateInterface $block
     */
    public function save(GiftTemplateInterface $giftTemplate)
    {
        try {
            $this->resource->save($giftTemplate);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $giftTemplate;
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
     * @return \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\Collection
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
        /** @var Magestore\Giftvoucher\Model\GiftTemplate $item */
        foreach ($collection as $item) {
            $modelData = $this->dataModelFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $modelData,
                $item->getData(),
                'Magestore\Giftvoucher\Api\Data\GiftTemplateInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $modelData,
                'Magestore\Giftvoucher\Api\Data\GiftTemplateInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * Delete Gift Template
     *
     * @param GiftTemplateInterface $giftTemplate
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(GiftTemplateInterface $giftTemplate)
    {
        try {
            $this->resource->delete($giftTemplate);
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
