<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer\Frontend;

/**
 * Class CartIndexActionObserver
 * @package Magestore\Giftvoucher\Observer\Frontend
 */
class CartIndexActionObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $checkoutSession;

    protected $helperData;

    protected $messageManager;

    /**
     * CartIndexActionObserver constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Giftvoucher\Helper\Data $helperData,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helperData = $helperData;
        $this->messageManager = $messageManager;
    }
    /**
     * Show Gift Card notification in Cart page
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $session = $this->checkoutSession;
        if ($session->getQuote()->getCouponCode() && !$this->helperData->getGeneralConfig('use_with_coupon')
            && ($session->getGiftVoucherDiscount() > 0)) {
            $this->messageManager->addNoticeMessage(__('A coupon code has been used. You cannot apply gift codes with the coupon to get discount.'));
            $session->setMessageApplyGiftcardWithCouponCode(false);
        }
        $session->getQuote()->setTotalsCollectedFlag(false)->collectTotals()->save();
    }
}
