<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

use Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

/**
 * Class Edit
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
 */
class Edit extends Giftvoucher
{
    /**
     * Edit Giftcode block
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magestore\Giftvoucher\Model\Giftvoucher $model */
        $model = $this->modelFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This gift codes no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('giftvoucher_data', $model);
        
        $resultPage = $this->resultPageFactory->create();
        
        $resultPage->addBreadcrumb(
            $id ? __('Edit Gift Code') : __('New Gift Code'),
            $id ? __('Edit Gift Code') : __('New Gift Code')
        )->setActiveMenu('Magestore_Giftvoucher::giftvoucher');
        
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Code'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getGiftCode() : __('New Gift Code'));
        
        return $resultPage;
    }
}
