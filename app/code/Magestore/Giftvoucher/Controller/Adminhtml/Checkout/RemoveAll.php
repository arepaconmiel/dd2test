<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Checkout;

/**
 * Class RemoveAll
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Checkout
 */
class RemoveAll extends \Magestore\Giftvoucher\Controller\Adminhtml\Checkout\AbstractAction
{
    /**
     * JSON
     */
    public function execute()
    {
        $quoteId = $this->getBodyParams('quote_id');
        $result = $this->checkoutService->removeCodes($quoteId);
        $this->_processResponseMessages($result);
        $response = [];
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}
