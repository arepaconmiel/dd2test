<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\ResourceModel\GiftCodeSets;

/**
 * Class Collection
 * @package Magestore\Giftvoucher\Model\ResourceModel\GiftCodeSets
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Giftvoucher\Model\GiftCodeSets', 'Magestore\Giftvoucher\Model\ResourceModel\GiftCodeSets');
    }
    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }
}
