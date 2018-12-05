<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

/**
 * Class Delete
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate
 */
class Delete extends GiftTemplate
{
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->giftTemplateRepository->deleteById($id);
                $this->messageManager->addSuccess(__('You deleted the gift card template.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a gift card template to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
