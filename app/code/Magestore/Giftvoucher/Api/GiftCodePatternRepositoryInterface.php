<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Gift Card Pattern CRUD interface.
 * @api
 */
interface GiftCodePatternRepositoryInterface
{
    /**
     * Save item.
     *
     * @param Data\GiftCodePatternInterface $giftCodePattern
     * @return Data\GiftCodePatternInterface
     * @internal param Data\GiftCodePatternInterface $block
     */
    public function save(Data\GiftCodePatternInterface $giftCodePattern);

    /**
     * Retrieve item.
     *
     * @param int $id
     * @return \Magestore\Giftvoucher\Api\Data\GiftCodePatternInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve items matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Giftvoucher\Api\Data\GiftCodePatternSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete item.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftCodePatternInterface
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\GiftCodePatternInterface $giftCodePattern);

    /**
     * Delete item by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
