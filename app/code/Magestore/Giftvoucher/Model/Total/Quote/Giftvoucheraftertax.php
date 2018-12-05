<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Total\Quote;

/**
 * Giftvoucher Total Quote Giftvoucheraftertax Model
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class Giftvoucheraftertax extends \Magestore\Giftvoucher\Model\Total\Quote\GiftvoucherAbstract
{
    /**
     * @var string
     */
    protected $_code = 'giftvoucheraftertax';

    /**
     * collect reward points total
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);

        if (!$isApplyGiftAfterTax = !$this->taxConfig->applyTaxAfterDiscount($quote->getStoreId())) {
            return $this;
        }
        

        $this->calculateDiscount($quote, $shippingAssignment, $total, $isApplyGiftAfterTax);
    }

    /**
     * Fetch (Retrieve data as array)
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if ($this->taxConfig->applyTaxAfterDiscount($quote->getStoreId())) {
            return [];
        }
        return parent::fetch($quote, $total);
    }
}
