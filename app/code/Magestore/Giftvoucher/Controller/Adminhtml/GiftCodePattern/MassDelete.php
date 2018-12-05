<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern
 */
class MassDelete extends GiftCodePattern
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * MassDelete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Api\GiftCodePatternRepositoryInterface $repository
     * @param \Magestore\Giftvoucher\Model\GiftCodePatternFactory $modelFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\CollectionFactory $collectionFactory
     * @param Filter $filter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Api\GiftCodePatternRepositoryInterface $repository,
        \Magestore\Giftvoucher\Model\GiftCodePatternFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\CollectionFactory $collectionFactory,
        Filter $filter
    ) {
        $this->filter = $filter;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry, $repository, $modelFactory, $collectionFactory);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        
        foreach ($collection as $item) {
            $item->delete();
        }
        
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $collectionSize)
        );
        
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
