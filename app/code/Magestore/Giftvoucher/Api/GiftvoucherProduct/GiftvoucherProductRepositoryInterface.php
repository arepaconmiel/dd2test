<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\GiftvoucherProduct;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Gift Voucher CRUD interface.
 * @api
 */
interface GiftvoucherProductRepositoryInterface
{
    /**
     * Save item.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftvoucherProductInterface $giftvoucherProduct
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Giftvoucher\Api\Data\GiftvoucherProductInterface $giftvoucherProduct);

    /**
     * Retrieve item.
     *
     * @param int $id
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve items matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherProductSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete item.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftvoucherProductInterface
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\Giftvoucher\Api\Data\GiftvoucherProductInterface $giftvoucherProduct);

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
