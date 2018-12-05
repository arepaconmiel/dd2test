<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for gift card template search results.
 * @api
 */
interface GiftvoucherProductSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blocks list.
     *
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherProductInterface[]
     */
    public function getItems();

    /**
     * Set blocks list.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftvoucherProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
