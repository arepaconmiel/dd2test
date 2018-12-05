<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Sales;

/**
 * Interface CancelOrderServiceInterface
 * @package Magestore\Giftvoucher\Api\Sales
 */
interface CancelOrderServiceInterface
{

    /**
     * Process cancel order applied gift card discount
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return boolean
     */
    public function execute($order);
}
