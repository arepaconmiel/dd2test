<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

/**
 * Class Edit
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate
 */
class Edit extends GiftTemplate
{
    /**
     * Edit CMS block
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->giftTemplateFactory->create();

        if ($id) {
            $model = $this->giftTemplateRepository->getById($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This template no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('gift_template', $model);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Template') : __('New Template'),
            $id ? __('Edit Template') : __('New Template')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Templates'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTemplateName() : __('New Template'));
        return $resultPage;
    }
}
