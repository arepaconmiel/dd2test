<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for gift code pattern search results.
 * @api
 */
interface GiftCodePatternSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get patterns list.
     *
     * @return \Magestore\Giftvoucher\Api\Data\GiftCodePatternInterface[]
     */
    public function getItems();

    /**
     * Set patterns list.
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftCodePatternInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
