<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Repository;

use Magestore\Giftvoucher\Api\Data\GiftvoucherInterface;
use Magestore\Giftvoucher\Api\Data\GiftvoucherInterfaceFactory;
use Magestore\Giftvoucher\Api\Data\GiftvoucherSearchResultsInterfaceFactory;
use Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magestore\Giftvoucher\Model\GiftvoucherFactory as GiftvoucherFactory;
use Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher as ResourceModel;
use Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory as CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\StoreFactory as StoreFactory;
use Magestore\Giftvoucher\Model\GiftTemplateFactory as GiftTemplateFactory;
use Magestore\Giftvoucher\Helper\Data as GiftvoucherHelper;

/**
 * Class GiftvoucherRepository
 * @package Magestore\Giftvoucher\Model\Repository
 */
class GiftvoucherRepository implements GiftvoucherRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $resource;

    /**
     * @var GiftvoucherFactory
     */
    protected $modelFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var GiftvoucherSearchResultsInterfaceFactory
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
     * @var GiftvoucherInterfaceFactory
     */
    protected $dataModelFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StoreFactory
     */
    protected $storeFactory;

    /**
     * @var GiftTemplateFactory
     */
    protected $templateFactory;

    /**
     * @var
     */
    protected $giftVoucherHelper;


    /**
     * GiftvoucherRepository constructor.
     * @param ResourceModel $resource
     * @param GiftvoucherFactory $modelFactory
     * @param GiftvoucherSearchResultsInterfaceFactory $dataModelFactory
     * @param CollectionFactory $collectionFactory
     * @param GiftvoucherSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param StoreFactory $storeFactory
     * @param GiftTemplateFactory $giftTemplateFactory
     * @param GiftvoucherHelper $giftvoucherHelper
     */
    public function __construct(
        ResourceModel $resource,
        GiftvoucherFactory $modelFactory,
        GiftvoucherSearchResultsInterfaceFactory $dataModelFactory,
        CollectionFactory $collectionFactory,
        GiftvoucherSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        StoreFactory $storeFactory,
        GiftTemplateFactory $giftTemplateFactory,
        GiftvoucherHelper $giftvoucherHelper
    ) {
        $this->resource = $resource;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataModelFactory = $dataModelFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->storeFactory = $storeFactory;
        $this->templateFactory = $giftTemplateFactory;
        $this->giftVoucherHelper = $giftvoucherHelper;
    }

    /**
     * Save $giftvoucher
     *
     * @param GiftvoucherInterface $giftvoucher
     * @return GiftvoucherInterface $giftvoucher
     * @throws CouldNotSaveException
     */
    public function save(GiftvoucherInterface $giftvoucher)
    {
        try {
            $this->resource->save($giftvoucher);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $giftvoucher;
    }

    /**
     * Load data by given Identity
     *
     * @param string $id
     * @return GiftvoucherInterface $giftvoucher
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
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherSearchResultsInterface
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
        $items =  $collection->getItems();
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * Delete Gift Template
     *
     * @param GiftvoucherInterface $giftTemplate
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(GiftvoucherInterface $giftTemplate)
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
    /**
     * mass create item.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftcodeMassCreateJsonInterface $data
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function massCreate(\Magestore\Giftvoucher\Api\Data\GiftcodeMassCreateJsonInterface $data)
    {

        $saveData = $data->getData();
        $saveData['extra_content'] = __('Created by Api user');
        try {
            if (!isset($saveData['balance']) || $saveData['balance'] <= 0) {
                throw new NoSuchEntityException(
                    __('Your Gift Card credit balance must be greater than 0.')
                );
            }
            if (!$this->giftVoucherHelper->isExpression($saveData['pattern'])) {
                throw new NoSuchEntityException(
                    __('Invalid pattern')
                );
            }
            if (!isset($saveData['template_name'])) {
                $saveData['template_name'] = __('Created by Api user');
            }
            if (!isset($saveData['currency'])) {
                $storeId = $this->storeManager->getStore()->getId();
                $storeModel = $this->storeFactory->create()->load($storeId);
                $saveData['currency'] = $storeModel->getDefaultCurrencyCode();
            }
            if (!isset($saveData['expired_at'])) {
                $saveData['expired_at'] = null;
            }
            if (!isset($saveData['status'])) {
                $saveData['status'] = \Magestore\Giftvoucher\Model\Source\Status::STATUS_ACTIVE;
            }
            $model = $this->templateFactory->create();
            $model->setData($saveData)
                ->save();
            $giftcard = $model->getData();
            $giftcard['gift_code'] = $giftcard['pattern'];
            $giftcard['template_id'] = $model->getId();
            $giftcard['amount'] = $giftcard['balance'];
            $giftcard['status'] = \Magestore\Giftvoucher\Model\Source\Status::STATUS_ACTIVE;
            $amount = $model->getAmount();
            $result = array();
            for ($i = 1; $i <= $amount; $i++) {
                $giftvoucher = $this->modelFactory->create()
                    ->setData($giftcard)
                    ->setIncludeHistory(true)
                    ->save();
                $result[] = $giftvoucher->toArray();
            }
            $model->setIsGenerated(1)->save();
        } catch (\Exception $e) {
            throw new NoSuchEntityException(
                __('Data_invalid')
            );
        }
        return $result;
    }
}
