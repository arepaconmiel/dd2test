<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for gift code history search results.
 * @api
 */
interface HistorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get blocks list.
     *
     * @return \Magestore\Giftvoucher\Api\Data\HistoryInterface[]
     */
    public function getItems();

    /**
     * Set blocks list.
     *
     * @param \Magestore\Giftvoucher\Api\Data\HistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
