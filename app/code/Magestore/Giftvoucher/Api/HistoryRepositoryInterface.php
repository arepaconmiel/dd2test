<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Gift Code History CRUD interface.
 * @api
 */
interface HistoryRepositoryInterface
{
    /**
     * Save item.
     *
     * @param \Magestore\Giftvoucher\Api\Data\HistoryInterface $history
     * @return \Magestore\Giftvoucher\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Giftvoucher\Api\Data\HistoryInterface $history);

    /**
     * Retrieve item.
     *
     * @param int $id
     * @return \Magestore\Giftvoucher\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve items matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Giftvoucher\Api\Data\HistorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
