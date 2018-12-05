<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Index;

/**
 * Add Action
 */
class Add extends \Magestore\Giftvoucher\Controller\Action
{
    /**
     * @return mixed|void
     */
    public function execute()
    {
        if (!$this->customerLoggedIn()) {
            $this->_redirect("customer/account/login");
            return;
        }
        $resultPageFactory = $this->getPageFactory();
        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
        $navigationBlock = $resultPageFactory->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('giftvoucher');
        }
        return $resultPageFactory;
    }
}
