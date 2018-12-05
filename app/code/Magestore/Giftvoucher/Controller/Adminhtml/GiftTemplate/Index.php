<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

/**
 * Class Index
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate
 */
class Index extends GiftTemplate
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magestore_Giftvoucher::gifttemplate');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Gift Card Template'));
        return $resultPage;
    }
}
