<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Gift Voucher CRUD interface.
 * @api
 */
interface GiftvoucherRepositoryInterface
{
    /**
     * Save item.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface $giftvoucher
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Giftvoucher\Api\Data\GiftvoucherInterface $giftvoucher);

    /**
     * Retrieve item.
     *
     * @param int $id
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve items matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete item.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\Giftvoucher\Api\Data\GiftvoucherInterface $giftTemplate);

    /**
     * Delete item by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
    /**
     * mass create item.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftcodeMassCreateJsonInterface $data
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function massCreate(\Magestore\Giftvoucher\Api\Data\GiftcodeMassCreateJsonInterface $data);
}
