<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

use Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassEmail
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
 */
class MassEmail extends Giftvoucher
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * MassEmail constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory
     * @param Filter $filter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory,
        Filter $filter
    ) {
        $this->filter = $filter;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry, $modelFactory, $collectionFactory);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        
        $totalEmailSent = 0;
        foreach ($collection as $item) {
            $item->setMassEmail(true);
            $totalEmailSent += (int)$item->sendEmail()->getEmailSent();
        }
        
        $this->messageManager->addSuccess(
            __('Total of %1 Gift Code with %2 email(s) were successfully sent.', $collectionSize, $totalEmailSent)
        );
        
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
