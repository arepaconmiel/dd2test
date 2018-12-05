<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Controller\Index;

use Magento\Customer\Model\Session;

/**
 * Giftvoucher Index Check Action
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Check extends \Magestore\Giftvoucher\Controller\Action
{

    /**
     * @return mixed
     */
    public function execute()
    {
        $resultPage = $this->getPageFactory();
        $resultPage->getConfig()->getTitle()->set(__('Check Gift Card Balance'));
        $this->_view->getLayout()->initMessages();
        return $resultPage;
    }
}
