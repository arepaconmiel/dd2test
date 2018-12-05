<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Service\GiftVoucherProduct;

/**
 * Class OrderManagement
 * @package Magestore\Giftvoucher\Service\GiftvoucherProduct
 */

class OrderManagement implements \Magestore\Giftvoucher\Api\GiftvoucherProduct\OrderManagementInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $_helperData;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\CreditHistory\CollectionFactory
     */
    protected $creditHistoryCollectionFactory;
    /**
     * @var \Magestore\Giftvoucher\Model\HistoryFactory
     */
    protected $historyFactory;
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $quoteSession;
    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherFactory
     */
    protected $giftvoucherFactory;
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageFactory;
    /**
     * @var \Magento\Quote\Model\Quote\Item\OptionFactory
     */
    protected $optionFactory;
    /**
     * @var \Magestore\Giftvoucher\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magestore\Giftvoucher\Model\CustomerVoucherFactory
     */
    protected $customerVoucherFactory;
    /**
     * OrderManagement constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\State $state
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Giftvoucher\Model\ResourceModel\CreditHistory\CollectionFactory $collectionFactory
     * @param \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Quote\Model\Quote\Item\OptionFactory $optionFactory
     * @param \Magestore\Giftvoucher\Model\ProductFactory $productFactory
     * @param \Magestore\Giftvoucher\Model\CustomerVoucherFactory $customerVoucherFactory
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\State $state,
        \Magestore\Giftvoucher\Helper\Data $helperData,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Giftvoucher\Model\ResourceModel\CreditHistory\CollectionFactory $collectionFactory,
        \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Quote\Model\Quote\Item\OptionFactory $optionFactory,
        \Magestore\Giftvoucher\Model\ProductFactory $productFactory,
        \Magestore\Giftvoucher\Model\CustomerVoucherFactory $customerVoucherFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_appState = $state;
        $this->_helperData = $helperData;
        $this->_priceCurrency = $priceCurrency;
        $this->_storeManager = $storeManager;
        $this->creditHistoryCollectionFactory = $collectionFactory;
        $this->historyFactory = $historyFactory;
        $this->quoteSession = $quoteSession;
        $this->giftvoucherFactory = $giftvoucherFactory;
        $this->imageFactory = $imageFactory;
        $this->optionFactory = $optionFactory;
        $this->productFactory = $productFactory;
        $this->customerVoucherFactory = $customerVoucherFactory;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order
     */
    public function loadOrderData($order)
    {
        $giftVouchers = $this->historyFactory->create()
            ->getCollection()
            ->joinGiftVoucher()
            ->addFieldToFilter('main_table.order_increment_id', $order->getIncrementId());
        $codesArray = array();
        $baseDiscount = 0;
        $discount = 0;
        foreach ($giftVouchers as $giftVoucher) {
            $codesArray[] = $giftVoucher->getGiftCode();
            $baseDiscount += $giftVoucher->getAmount();
            $discount += $giftVoucher->getOrderAmount();
        }
        if ($baseDiscount) {
            $baseCurrency = $this->_priceCurrency->getCurrency(null, $order->getBaseCurrencyCode());
            $currentCurrency = $this->_priceCurrency->getCurrency(null, $order->getOrderCurrencyCode());
            $baseDiscount = $baseDiscount * $baseDiscount / $baseCurrency->convert($baseDiscount, $currentCurrency);

            $order->setGiftCodes(implode(',', $codesArray));
            $order->setBaseGiftVoucherDiscount($baseDiscount);
            $order->setGiftVoucherDiscount($discount);
        }
        $creditHistory = $this->creditHistoryCollectionFactory->create()
            ->addFieldToFilter('action', 'Spend')
            ->addFieldToFilter('order_id', $order->getId())
            ->getFirstItem();
        if ($creditHistory && $creditHistory->getId()) {
            $order->setGiftcardCreditAmount($creditHistory->getBalanceChange());
            $order->setBaseUseGiftCreditAmount($creditHistory->getBaseAmount());
            $order->setUseGiftCreditAmount($creditHistory->getAmount());
        }
        return $order;
    }


    /**
     * @param \Magento\Sales\Model\Order $order
     * @param float $baseGrandTotal
     * @return \Magento\Sales\Model\Order\Creditmemo
     */
    public function refundOffline($order, $baseGrandTotal)
    {
        $adminSession = $this->_objectManager->get('Magento\Backend\Model\Session\Quote');
        if ($this->_appState->getAreaCode() == 'admin') {
            $store = $adminSession->getStore();
        } else {
            $store = $this->_storeManager->getStore();
        }

        if ($codes = $order->getGiftVoucherGiftCodes()) {
            $codesArray = explode(',', $codes);
            foreach ($codesArray as $code) {
                if ($this->_priceCurrency->round($baseGrandTotal) == 0) {
                    return;
                }

                $giftVoucher = $this->_objectManager->create('Magestore\Giftvoucher\Model\Giftvoucher')
                    ->loadByCode($code);
                $history = $this->_objectManager->create('Magestore\Giftvoucher\Model\History');
                $baseCurrency = $this->_storeManager->getStore($order->getStoreId())->getBaseCurrency();
                $availableDiscount = 0;
                if ($rate = $baseCurrency->getRate($order->getOrderCurrencyCode())) {
                    $availableDiscount = ($history->getTotalSpent($giftVoucher, $order)
                            - $history->getTotalRefund($giftVoucher, $order)) / $rate;
                }
                if ($this->_priceCurrency->round($availableDiscount) == 0) {
                    continue;
                }

                if ($availableDiscount < $baseGrandTotal) {
                    $baseGrandTotal = $baseGrandTotal - $availableDiscount;
                } else {
                    $availableDiscount = $baseGrandTotal;
                    $baseGrandTotal = 0;
                }
                $baseCurrencyCode = $order->getBaseCurrencyCode();
                $baseCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                    ->load($baseCurrencyCode);
                $currentCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                    ->load($giftVoucher->getData('currency'));

                $discountRefund = $this->_objectManager->create('Magento\Directory\Helper\Data')
                    ->currencyConvert($availableDiscount, $baseCurrencyCode, $giftVoucher->getData('currency'));
                $discountCurrentRefund = $this->_objectManager->create('Magento\Directory\Helper\Data')
                    ->currencyConvert($availableDiscount, $baseCurrencyCode, $order->getOrderCurrencyCode());

                $balance = $giftVoucher->getBalance() + $discountRefund;
                $baseBalance = $balance * $balance / $baseCurrency->convert($balance, $currentCurrency);
                $currentBalance = $this->_objectManager->create('Magento\Directory\Helper\Data')
                    ->currencyConvert($baseBalance, $baseCurrencyCode, $order->getOrderCurrencyCode());

                if ($giftVoucher->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_USED) {
                    $giftVoucher->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE);
                }
                $giftVoucher->setData('balance', $balance)->save();

                $history->setData(array(
                    'order_increment_id' => $order->getIncrementId(),
                    'giftvoucher_id' => $giftVoucher->getId(),
                    'created_at' => date("Y-m-d H:i:s"),
                    'action' => \Magestore\Giftvoucher\Model\Actions::ACTIONS_REFUND,
                    'amount' => $discountCurrentRefund,
                    'balance' => $currentBalance,
                    'currency' => $order->getOrderCurrencyCode(),
                    'status' => $giftVoucher->getStatus(),
                    'comments' => __('Refund from order %1', $order->getIncrementId()),
                    'customer_id' => $order->getData('customer_id'),
                    'customer_email' => $order->getData('customer_email'),
                ))->save();
            }
        }
        if ($order->getBaseUseGiftCreditAmount() && $order->getCustomerId()
            && $this->_helperData->getGeneralConfig('enablecredit', $order->getStoreId())) {
            $credit = $this->_objectManager->create('Magestore\Giftvoucher\Model\Credit')
                ->load($order->getCustomerId(), 'customer_id');
            if ($credit->getId()) {
                // check order is refunded to credit balance
                $histories = $this->_objectManager
                    ->create('Magestore\Giftvoucher\Model\ResourceModel\Credithistory\Collection')
                    ->addFieldToFilter('customer_id', $order->getCustomerId())
                    ->addFieldToFilter('action', 'Refund')
                    ->addFieldToFilter('order_id', $order->getId())
                    ->getFirstItem();
                if ($histories && $histories->getId()) {
                    return;
                }
                try {
                    $credit->setBalance($credit->getBalance() + $order->getBaseUseGiftCreditAmount());
                    $credit->save();
                    if ($store->getCurrentCurrencyCode() != $order->getBaseCurrencyCode()) {
                        $baseCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                            ->load($order->getBaseCurrencyCode());
                        $currentCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                            ->load($order->getOrderCurrencyCode());
                        $currencyBalance = $baseCurrency->convert(round($credit->getBalance(), 4), $currentCurrency);
                    } else {
                        $currencyBalance = round($credit->getBalance(), 4);
                    }
                    $credithistory = $this->_objectManager->create('Magestore\Giftvoucher\Model\Credithistory')
                        ->setData($credit->getData());
                    $credithistory->addData(array(
                        'action' => 'Refund',
                        'currency_balance' => $currencyBalance,
                        'order_id' => $order->getId(),
                        'order_number' => $order->getIncrementId(),
                        'balance_change' => $order->getUseGiftCreditAmount(),
                        'created_date' => date("Y-m-d H:i:s"),
                        'currency' => $store->getCurrentCurrencyCode(),
                        'base_amount' => $order->getBaseUseGiftCreditAmount(),
                        'amount' => $order->getUseGiftCreditAmount()
                    ))->setId(null)->save();
                } catch (\Exception $e) {
                }
            }
        }
        return;
    }

    /**
     * Add Gift Card data to order
     *
     * @param $order
     * @return \Magento\Sales\Model\Order
     */
    public function addGiftVoucherForOrder($order)
    {
        $adminSession = $this->quoteSession;
        if ($this->_appState->getAreaCode() == 'admin') {
            $store = $adminSession->getStore();
        } else {
            $store = $this->_storeManager->getStore();
        }

        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() != 'giftvoucher') {
                continue;
            }

            $options = $item->getProductOptions();

            $buyRequest = $options['info_buyRequest'];

            $quoteItemOptions = $this->optionFactory->create()->getCollection()
                ->addFieldToFilter('item_id', array('eq' => $item->getQuoteItemId()));
            if (isset($buyRequest['amount']) && $quoteItemOptions) {
                foreach ($quoteItemOptions as $quoteItemOption) {
                    if ($quoteItemOption->getCode() == 'amount') {
                        $buyRequest['amount'] = $this->_priceCurrency->round($quoteItemOption->getValue());
                        $options['info_buyRequest'] = $buyRequest;
                        $item->setProductOptions($options);
                    }
                }
            }

            $giftVouchers = $this->giftvoucherFactory->create()->getCollection()
                ->addItemFilter($item->getQuoteItemId());

            $time = time();

            for ($i = 0; $i < $item->getQtyOrdered() - $giftVouchers->getSize(); $i++) {
                $giftVoucher = $this->giftvoucherFactory->create();
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
                if (isset($buyRequest['amount'])) {
                    $amount = $buyRequest['amount'];
                } else {
                    $amount = $item->getPrice();
                }
                $giftVoucher->setBalance($amount)->setAmount($amount);
                $giftVoucher->setOrderAmount($item->getBasePrice());

                $giftProduct = $this->productFactory->create()->loadByProduct($product);
                $giftVoucher->setDescription($giftProduct->getGiftcardDescription());
                if ($giftProduct->getId()) {
                    if (class_exists('\Magento\Framework\Serialize\Serializer\Json')) {
                        $unserialize = $this->_objectManager->create('\Magento\Framework\Serialize\Serializer\Json');
                        $conditionsArr =$unserialize->unserialize($giftProduct->getConditionsSerialized());
                        $actionsArr = $unserialize->unserialize($giftProduct->getActionsSerialized());
                    } else {
                        $conditionsArr = unserialize($giftProduct->getConditionsSerialized());
                        $actionsArr = unserialize($giftProduct->getActionsSerialized());
                    }
                    if (!empty($conditionsArr) && is_array($conditionsArr)) {
                        $giftVoucher->getConditions()->loadArray($conditionsArr);
                    }
                    if (!empty($actionsArr) && is_array($actionsArr)) {
                        $giftVoucher->getActions()->loadArray($actionsArr);
                    }
                }
                if (isset($buyRequest['customer_name'])) {
                    $giftVoucher->setCustomerName($buyRequest['customer_name']);
                }
                if (isset($buyRequest['giftcard_template_id']) && $buyRequest['giftcard_template_id']) {
                    $giftVoucher->setGiftcardTemplateId($buyRequest['giftcard_template_id']);
                }
                if (isset($buyRequest['recipient_name'])) {
                    $giftVoucher->setRecipientName($buyRequest['recipient_name']);
                }
                if (isset($buyRequest['recipient_email'])) {
                    $giftVoucher->setRecipientEmail($buyRequest['recipient_email']);
                }
                if (isset($buyRequest['message'])) {
                    $giftVoucher->setMessage($buyRequest['message']);
                }
                if (isset($buyRequest['notify_success'])) {
                    $giftVoucher->setNotifySuccess($buyRequest['notify_success']);
                }
                if (isset($buyRequest['day_to_send']) && $buyRequest['day_to_send']) {
                    $giftVoucher->setDayToSend(date('Y-m-d', strtotime($buyRequest['day_to_send'])));
                }

                if (isset($buyRequest['timezone_to_send']) && $buyRequest['timezone_to_send']) {
                    $giftVoucher->setTimezoneToSend($buyRequest['timezone_to_send']);
                    // $customerZone = new \DateTimeZone($giftVoucher->getTimezoneToSend());
                    $customerZone = $this->_objectManager->create('DateTimeZone', ['timezone' => $giftVoucher->getTimezoneToSend()]);
                    // $date = new \DateTime($giftVoucher->getDayToSend(), $customerZone);
                    $date = $this->_objectManager->create(
                        'DateTime',
                        [
                            'time' => $giftVoucher->getDayToSend(),
                            'DateTimeZone' => $customerZone
                        ]
                    );
                    $serverTimezone = $this->_storeManager->getStore()->getConfig('general/locale/timezone');
                    $date->setTimezone(
                        $this->_objectManager->create('DateTimeZone', ['timezone' => $serverTimezone])
                    );
                    $giftVoucher->setDayStore($date->format('Y-m-d'));
                }

                if (isset($buyRequest['giftcard_template_image']) && $buyRequest['giftcard_template_image']) {
                    if (isset($buyRequest['giftcard_use_custom_image']) && $buyRequest['giftcard_use_custom_image']) {
                        $dir = $this->_helperData->getBaseDirMedia()->getAbsolutePath('tmp/giftvoucher/images/'
                            . $buyRequest['giftcard_template_image']);
                        if (file_exists($dir)) {
                            $imageObj = $this->imageFactory->create();
                            $imageObj->open($dir);
                            $imagePath = $this->_helperData->getStoreManager()->getStore()
                                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                                . 'giftvoucher/template/images/';
                            $customerIploadImage = strval($time) . $buyRequest['giftcard_template_image'];
                            $dirCustomerUpload = $this->_helperData->getBaseDirMedia()
                                ->getAbsolutePath(strstr($imagePath, '/giftvoucher'). $customerIploadImage);

                            if (!file_exists($dirCustomerUpload)) {
                                $result = $imageObj->save($dirCustomerUpload);
                                $this->_helperData->customResizeImage($customerIploadImage, 'images');
                            }
                            $giftVoucher->setGiftcardCustomImage(true);
                            $giftVoucher->setGiftcardTemplateImage($customerIploadImage);
                        } else {
                            $giftVoucher->setGiftcardTemplateImage('default.png');
                        }
                    } else {
                        $giftVoucher->setGiftcardTemplateImage($buyRequest['giftcard_template_image']);
                    }
                }

                if (isset($buyRequest['recipient_ship'])
                    && $buyRequest['recipient_ship'] != null
                    && $address = $order->getShippingAddress()) {
                    $giftVoucher->setRecipientAddress($address->getFormated());
                }

                $giftVoucher->setCurrency($store->getCurrentCurrencyCode());

                if ($order->getStatus() == \Magento\Sales\Model\Order::STATE_COMPLETE) {
                    $giftVoucher->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE);
                } else {
                    $giftVoucher->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_PENDING);
                }

                if ($timeLife = $this->_helperData->getGeneralConfig('expire', $order->getStoreId())) {
                    $orderTime = date(
                        "Y-m-d",
                        $this->_helperData->getObjectManager()->get('Magento\Framework\Stdlib\DateTime\DateTime')
                            ->timestamp(time())
                    );
                    $expire = date('Y-m-d', strtotime($orderTime . '+' . $timeLife . ' days'));
                    $giftVoucher->setExpiredAt($expire);
                }

                $giftVoucher->setCustomerId($order->getCustomerId())
                    ->setCustomerEmail($order->getCustomerEmail())
                    ->setStoreId($order->getStoreId());

                if (!$giftVoucher->getCustomerName()) {
                    $giftVoucher->setCustomerName(
                        $order->getData('customer_firstname') . ' ' . $order->getData('customer_lastname')
                    );
                }

                $giftVoucher->setAction(\Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE)
                    ->setComments(__('Created for order %1', $order->getIncrementId()))
                    ->setOrderIncrementId($order->getIncrementId())
                    ->setQuoteItemId($item->getQuoteItemId())
                    ->setExtraContent(
                        __('Created by customer %1 %2', $order->getData('customer_firstname'), $order->getData('customer_lastname'))
                    )
                    ->setIncludeHistory(true);

                try {
                    if ($giftVoucher->getDayToSend() && strtotime($giftVoucher->getDayToSend()) > time()
                    ) {
                        $giftVoucher->setData('dont_send_email_to_recipient', 1);
                    }
                    if (!empty($buyRequest['recipient_ship'])) {
                        $giftVoucher->setData('is_sent', 2);
                        if (!$this->_helperData->getEmailConfig('send_with_ship', $order->getStoreId())) {
                            $giftVoucher->setData('dont_send_email_to_recipient', 1);
                        }
                    }
                    $giftVoucher->save();
                    if ($order->getCustomerId()) {
                        $timeSite = date(
                            "Y-m-d",
                            $this->_helperData->getObjectManager()->get('Magento\Framework\Stdlib\DateTime\DateTime')
                                ->timestamp(time())
                        );
                        $this->customerVoucherFactory->create()
                            ->setCustomerId($order->getCustomerId())
                            ->setVoucherId($giftVoucher->getId())
                            ->setAddedDate($timeSite)
                            ->save();
                    }
                } catch (\Exception $e) {
                }
            }
        }
        return;
    }
}
