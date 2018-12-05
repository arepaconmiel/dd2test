<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

use Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

/**
 * Class Delete
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
 */
class Delete extends Giftvoucher
{
    /**
     * @var \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface
     */
    protected $repository;

    /**
     * Delete constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory
     * @param \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $repository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory,
        \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $repository
    ) {
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry, $modelFactory, $collectionFactory);
        $this->repository = $repository;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        
        /** @var \Magestore\Giftvoucher\Model\Giftvoucher $model */
        $model = $this->modelFactory->create();
        
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $this->repository->deleteById($id);
                $this->messageManager->addSuccess(__('Gift Code was successfully deleted'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
