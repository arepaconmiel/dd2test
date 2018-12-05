<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Checkout;

/**
 * Class ReloadForm
 * @package Magestore\Giftvoucher\Controller\Checkout
 */
class ReloadForm extends \Magestore\Giftvoucher\Controller\Checkout\AbstractAction
{
    /**
     * JSON
     */
    public function execute()
    {
        $response = $this->_getRedeemFormData();
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}
