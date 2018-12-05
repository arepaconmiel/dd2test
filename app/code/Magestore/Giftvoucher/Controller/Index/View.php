<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Index;

/**
 * View Action
 */
class View extends \Magestore\Giftvoucher\Controller\Action
{
    /**
     * Giftvoucher View action
     */
    public function execute()
    {
        if (!$this->customerLoggedIn()) {
            $resultRedirectFactory = $this->getRedirectFactory()
                ->setPath('customer/account/login', array('_secure' => true));
            return $resultRedirectFactory;
        }
        $resultPageFactory = $this->getPageFactory();
        $resultPageFactory->getConfig()->getTitle()->set('Gift Card Detail');
        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
        $navigationBlock = $resultPageFactory->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('giftvoucher');
        }
        return $resultPageFactory;
    }
}
