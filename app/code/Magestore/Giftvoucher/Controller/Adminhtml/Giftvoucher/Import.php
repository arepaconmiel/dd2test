<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

use Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

/**
 * Import Gift Code Form
 */
class Import extends Giftvoucher
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        
        $resultPage->addBreadcrumb(__('Gift Code Manager'), __('Gift Code Manager'))
            ->addBreadcrumb(__('Import Gift Code'), __('Import Gift Code'))
            ->setActiveMenu('Magestore_Giftvoucher::giftvoucher');
        
        $resultPage->getConfig()->getTitle()->prepend(__('Import Gift Code'));

        return $resultPage;
    }
}
