<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern;

/**
 * Class Index
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern
 */
class Index extends \Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Giftvoucher::generategiftcard');
        $resultPage->addBreadcrumb(__('Gift Code Pattern'), __('Gift Code Pattern'));
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Code Pattern'));
        return $resultPage;
    }
}
