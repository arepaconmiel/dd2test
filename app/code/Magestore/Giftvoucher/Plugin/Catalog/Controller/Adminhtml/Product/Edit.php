<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Plugin\Catalog\Controller\Adminhtml\Product;

/**
 * Class Edit
 * @package Magestore\Giftvoucher\Plugin\Catalog\Controller\Adminhtml\Product
 */
class Edit
{
    /**
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Edit $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(\Magento\Catalog\Controller\Adminhtml\Product\Edit $subject, $result)
    {
        $result->addHandle('giftvoucher_product_form');
        return $result;
    }
}
