<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

/**
 * Class MassPrint
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
 */
class MassPrint extends \Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
{
    /**
     * Print action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Code'));
        $resultPage->getConfig()->getTitle()->prepend(__('Print Gift Codes'));
        return $resultPage;
    }
}
