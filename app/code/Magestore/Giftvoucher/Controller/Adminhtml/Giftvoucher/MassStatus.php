<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

use Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magestore\Giftvoucher\Model\Status;
use Magestore\Giftvoucher\Model\Actions;

/**
 * Class MassStatus
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
 */
class MassStatus extends Giftvoucher
{
    /**
     * @var Filter
     */
    protected $filter;
    
    /**
     * @var AuthSession
     */
    protected $authSession;

    /**
     * MassStatus constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param AuthSession $authSession
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory,
        Filter $filter,
        AuthSession $authSession
    ) {
        $this->filter = $filter;
        $this->authSession = $authSession;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry, $modelFactory, $collectionFactory);
    }
    
    /**
     * (non-PHPdoc)
     * @see \Magento\Framework\App\ActionInterface::execute()
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        
        $newStatus = $this->getRequest()->getParam('status');
        $extraContent = __('Mass status updated by %1', $this->authSession->getUser()->getUsername());
        try {
            $changes = 0;
            foreach ($collection as $item) {
                if ($item->getStatus() < Status::STATUS_EXPIRED) {
                    $item->setStatus($newStatus)
                        ->setIsMassupdate(true)
                        ->setAction(Actions::ACTIONS_MASS_UPDATE)
                        ->setExtraContent($extraContent)
                        ->setIncludeHistory(true)
                        ->save();
                    $changes++;
                }
            }
            
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been changed status.', $changes)
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
