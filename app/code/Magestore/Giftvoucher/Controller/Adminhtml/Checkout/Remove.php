<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Checkout;

/**
 * Class Remove
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Checkout
 */
class Remove extends \Magestore\Giftvoucher\Controller\Adminhtml\Checkout\AbstractAction
{
    /**
     * JSON
     */
    public function execute()
    {
        $quoteId = $this->getBodyParams('quote_id');
        $giftCode = $this->getBodyParams('gift_code');
        $result = $this->checkoutService->removeCode($quoteId, $giftCode);
        $this->_processResponseMessages($result);
        $response = [];
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}
