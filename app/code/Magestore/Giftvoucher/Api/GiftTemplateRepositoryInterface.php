<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Gift Card Template CRUD interface.
 * @api
 */
interface GiftTemplateRepositoryInterface
{
    /**
     * Save item.
     *
     * @param Data\GiftTemplateInterface $giftTemplate
     * @return Data\GiftTemplateInterface
     * @internal param Data\GiftTemplateInterface $block
     */
    public function save(Data\GiftTemplateInterface $giftTemplate);

    /**
     * Retrieve item.
     *
     * @param int $id
     * @return \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve items matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Giftvoucher\Api\Data\GiftTemplateSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete item.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\GiftTemplateInterface $giftTemplate);

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
