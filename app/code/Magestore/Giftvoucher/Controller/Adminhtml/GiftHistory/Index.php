<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftHistory;

/**
 * Class Index
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftHistory
 */
class Index extends \Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magestore_Giftvoucher::gifthistory';

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Giftvoucher::gifthistory');
        $resultPage->addBreadcrumb(__('Gift Code History'), __('Gift Code History'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage History'));
        return $resultPage;
    }
}
