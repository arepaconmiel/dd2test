<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;
use Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory;

/**
 * Class MassDelete
 */
class MassDelete extends GiftTemplate
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;


    /**
     *
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory
     * @param GiftTemplateRepositoryInterface $giftTemplateRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory,
        \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry, $giftTemplateFactory, $giftTemplateRepository);
    }


    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $totalItem = 0;
        foreach ($collection->getAllIds() as $id) {
            $this->giftTemplateRepository->deleteById($id);
            $totalItem++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $totalItem));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
