<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

/**
 * Class Index
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
 */
class Index extends \Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Giftvoucher::giftvoucher');
        $resultPage->addBreadcrumb(__('Gift Code'), __('Gift Code'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Gift Codes'));
        return $resultPage;
    }
}
