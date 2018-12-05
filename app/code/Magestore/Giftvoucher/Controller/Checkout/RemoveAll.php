<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Checkout;

/**
 * Class RemoveAll
 * @package Magestore\Giftvoucher\Controller\Checkout
 */
class RemoveAll extends \Magestore\Giftvoucher\Controller\Checkout\AbstractAction
{
    /**
     * JSON
     */
    public function execute()
    {
        $quoteId = $this->getBodyParams('quote_id');
        $result = $this->checkoutService->removeCodes($quoteId);
        $this->_processResponseMessages($result);
        $response = $this->_getRedeemFormData();
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}
