<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\Component\Listing\Column;

/**
 * Class Store
 */
class Store extends \Magento\Store\Ui\Component\Listing\Column\Store
{
    /**
     * (non-PHPdoc)
     * @see \Magento\Store\Ui\Component\Listing\Column\Store::prepareItem()
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        if (empty($item[$this->storeKey])) {
            $item[$this->storeKey] = [0];
        }
        return parent::prepareItem($item);
    }
}
