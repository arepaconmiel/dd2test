<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer;

/**
 * Class UpdateShippedGiftCardObserver
 * @package Magestore\Giftvoucher\Observer
 */
class UpdateShippedGiftCardObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory
     */
    protected $giftVoucherCollectionFactory;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helperData;

    /**
     * UpdateShippedGiftCardObserver constructor.
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $giftVoucherCollectionFactory
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     */
    public function __construct(
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $giftVoucherCollectionFactory,
        \Magestore\Giftvoucher\Helper\Data $helperData
    ) {
        $this->giftVoucherCollectionFactory = $giftVoucherCollectionFactory;
        $this->helperData = $helperData;
    }

    /**
     * Update the shipping information of Gift Card
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipmentItem = $observer->getEvent()->getShipmentItem();
        $orderItemId = $shipmentItem->getOrderItemId();

        $giftVouchers = $this->giftVoucherCollectionFactory->create()->addItemFilter($orderItemId);
        foreach ($giftVouchers as $giftCard) {
            if ($giftCard->getShippedToCustomer()
                || !$this->helperData->getStoreConfig('giftvoucher/general/auto_shipping', $giftCard->getStoreId())
            ) {
                return $this;
            }
            try {
                $giftCard->setShippedToCustomer(1)
                        ->save();
            } catch (\Exception $e) {
                return $this;
            }
        }
        return $this;
    }
}
