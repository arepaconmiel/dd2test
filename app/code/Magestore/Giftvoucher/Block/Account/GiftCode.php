<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Account;

use Magestore\Giftvoucher\Block\Account;
use Magestore\Giftvoucher\Model\Actions;

/**
 * Gift Code block
 */
class GiftCode extends \Magestore\Giftvoucher\Block\Account
{
    /**
     * @var \Magestore\Giftvoucher\Model\CustomerVoucher
     */
    protected $model;

    /**
     * GiftCode constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $accountManagement
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Magento\Framework\Url\DecoderInterface $decode
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $collectionFactory
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param \Magestore\Giftvoucher\Model\CustomerVoucher $model
     * @param \Magestore\Giftvoucher\Service\GiftTemplate\ProcessorService $processor
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $accountManagement,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Framework\Url\DecoderInterface $decode,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $collectionFactory,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magestore\Giftvoucher\Model\CustomerVoucher $model,
        \Magestore\Giftvoucher\Service\GiftTemplate\ProcessorService $processor,
        array $data = []
    ) {
        parent::__construct($context, $accountManagement, $viewHelper, $httpContext, $currentCustomer, $objectManager, $datetime, $decode, $imageFactory, $priceCurrency, $helper, $collectionFactory, $giftvoucherFactory, $data);
        $this->model = $model;
        if (!$model->getId()) {
            $this->model->load($this->getRequest()->getParam('id'));
        }
    }

    /**
     * @return \Magestore\Giftvoucher\Model\CustomerVoucher
     */
    public function getCustomerGift()
    {
        return $this->model;
    }
    
    /**
     * Check a gift code is sent to the recipient or not
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftCard
     * @return boolean
     */
    public function checkSendFriendGiftCard($giftCard)
    {
        return ($giftCard->getRecipientName() && $giftCard->getRecipientEmail()
            && $giftCard->getCustomerId() == $this->currentCustomer->getCustomerId()
            );
    }
    
    /**
     * Get current Gift Code Model
     *
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     */
    public function getGiftVoucher()
    {
        if (!$this->hasData('gift_voucher')) {
            $this->setData(
                'gift_voucher',
                $this->_giftvoucherFactory->create()->load($this->model->getData('voucher_id'))
            );
        }
        return $this->getData('gift_voucher');
    }
    
    /**
     * get History for Gift Card
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftCard
     * @return \Magestore\Giftvoucher\Model\ResourceModel\History\Collection
     */
    public function getGiftCardHistory($giftCard)
    {
        $collection = $this->getModel('Magestore\Giftvoucher\Model\History')->getCollection()
            ->addFieldToFilter('main_table.giftvoucher_id', $giftCard->getId());
        if ($giftCard->getCustomerId() != $this->getCustomer()->getId()) {
            $collection->addFieldToFilter('main_table.customer_id', $this->getCustomer()->getId());
        }
        $collection->getHistory();
        return $collection;
    }
    
    /**
     * Get action name of Gift card history
     *
     * @param \Magestore\Giftvoucher\Model\History $history
     * @return string
     */
    public function getActionName($history)
    {
        $actions = Actions::getOptionArray();
        if (isset($actions[$history->getAction()])) {
            return $actions[$history->getAction()];
        }
        reset($actions);
        return current($actions);
    }
    
    /**
     * get shipment for gift card
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftCard
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipmentForGiftCard($giftCard)
    {
        $history = $this->getModel('Magestore\Giftvoucher\Model\History')->getCollection()
            ->addFieldToFilter('giftvoucher_id', $giftCard->getId())
            ->addFieldToFilter('action', \Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE)
            ->getFirstItem();
        if (!$history->getOrderIncrementId() || !$history->getOrderItemId()) {
            return false;
        }
        $shipmentItem = $this->getModel('Magento\Sales\Model\Order\Shipment\Item')->getCollection()
            ->addFieldToFilter('order_item_id', $history->getOrderItemId())
            ->getFirstItem();
        if (!$shipmentItem || !$shipmentItem->getId()) {
            return false;
        }
        $shipment = $this->getModel('Magento\Sales\Model\Order\Shipment')->load($shipmentItem->getParentId());
        if (!$shipment->getId()) {
            return false;
        }
        return $shipment;
    }

    /**
     * @return bool
     */
    public function messageMaxLen()
    {
        return $this->_helper->getInterfaceConfig('max');
    }
}
