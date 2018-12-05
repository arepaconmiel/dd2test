<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Checkout;

/**
 * Class Apply
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Checkout
 */
class Apply extends \Magestore\Giftvoucher\Controller\Adminhtml\Checkout\AbstractAction
{
    /**
     * JSON
     */
    public function execute()
    {
        $quoteId = $this->getBodyParams('quote_id');
        $addedCodes = $this->getBodyParams('added_codes');
        $existedCode = $this->getBodyParams('existed_code');
        $newCode = $this->getBodyParams('new_code');
        $result = $this->checkoutService->applyCodes($quoteId, $addedCodes, $existedCode, $newCode);
        $this->_processResponseMessages($result);
        $response = [];
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}
