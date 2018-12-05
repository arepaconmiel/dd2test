<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer;

/**
 * Class ProductAddAfterObserver
 * @package Magestore\Giftvoucher\Observer
 */
class ProductAddAfterObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $customerSession;

    /**
     * ProductAddAfterObserver constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * Set the Gift Card custom images to the customer session after Gift Card products is added to cart
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $product = $event->getProduct();
        if ($product->getTypeId() == 'giftvoucher') {
            $this->customerSession->setGiftcardCustomUploadImage('');
        }
    }
}
