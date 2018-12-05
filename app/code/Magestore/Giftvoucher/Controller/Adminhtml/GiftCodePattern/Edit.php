<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern;

/**
 * Class Edit
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern
 */
class Edit extends GiftCodePattern
{
    /**
     * Edit Gift code pattern
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magestore\Giftvoucher\Model\GiftCodePattern $model */
        $model = $this->modelFactory->create();
        if ($id) {
            $model = $this->repository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This gift code pattern no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        
        $this->coreRegistry->register('giftcodepattern_data', $model);
        
        $resultPage = $this->resultPageFactory->create();
        
        $resultPage->addBreadcrumb(
            $id ? __('Edit Gift Code Pattern') : __('New Gift Code Pattern'),
            $id ? __('Edit Gift Code Pattern') : __('New Gift Code Pattern')
        )->setActiveMenu('Magestore_Giftvoucher::generategiftcard');
        
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Code Pattern'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ?
            __('Edit Gift Code Pattern "%1"', $model->getTemplateName()) :
            __('New Gift Code Pattern')
        );
        
        return $resultPage;
    }
}
