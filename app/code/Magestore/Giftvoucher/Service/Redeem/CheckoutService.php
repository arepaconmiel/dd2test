<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Service\Redeem;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magestore\Giftvoucher\Api\Data\GiftcodeDiscountInterface;
use Magestore\Giftvoucher\Api\Data\GiftcodeInterface;
use Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface;

/**
 * Class CheckoutService
 * @package Magestore\Giftvoucher\Service\Redeem
 */
class CheckoutService implements \Magestore\Giftvoucher\Api\Redeem\CheckoutServiceInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory
     */
    protected $voucherCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherFactory
     */
    protected $giftvoucherFactory;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $directoryHelper;

    /**
     * @var \Magestore\Giftvoucher\Model\HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\CustomerVoucherFactory
     */
    protected $customerVoucherFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory
     */
    protected $giftVoucherCollectionFactory;

    /**
     * @var \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface
     */
    protected $giftVoucherRepository;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * CheckoutService constructor.
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $voucherCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory
     * @param \Magestore\Giftvoucher\Model\CustomerVoucherFactory $customerVoucherFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $giftVoucherCollectionFactory
     * @param \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $giftvoucherRepository
     * @param Magento\Framework\ObjectManagerInterface $objectmanager
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $voucherCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory,
        \Magestore\Giftvoucher\Model\CustomerVoucherFactory $customerVoucherFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $giftVoucherCollectionFactory,
        \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $giftvoucherRepository,
        \Magento\Framework\ObjectManagerInterface $objectmanager
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->currencyFactory = $currencyFactory;
        $this->voucherCollectionFactory = $voucherCollectionFactory;
        $this->storeManager = $storeManager;
        $this->giftvoucherFactory = $giftvoucherFactory;
        $this->priceCurrency = $priceCurrency;
        $this->helper = $helper;
        $this->directoryHelper = $directoryHelper;
        $this->historyFactory = $historyFactory;
        $this->customerVoucherFactory = $customerVoucherFactory;
        $this->giftVoucherCollectionFactory = $giftVoucherCollectionFactory;
        $this->giftvoucherRepository = $giftvoucherRepository;
        $this->_objectManager = $objectmanager;
    }

    /**
     * Get Existed Gift Card
     *
     * @param int $cartId
     * @return \Magestore\Giftvoucher\Api\Data\GiftcodeDiscountInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUsingGiftCodes($cartId)
    {
        $quote = $this->getQuote($cartId);
        $giftCards = array();
        if ($codes = $quote->getGiftVoucherGiftCodes()) {
            $codesArray = explode(',', $codes);
            $codesDiscountArray = explode(',', $quote->getGiftVoucherGiftCodesDiscount());
            foreach ($codesArray as $key => $code) {
                $giftCards[] = [
                    GiftcodeDiscountInterface::CODE => $code,
                    GiftcodeDiscountInterface::DISCOUNT => (isset($codesDiscountArray[$key]))?$codesDiscountArray[$key]:0
                ];
            }
        }
        return $giftCards;
    }

    /**
     * Get Existed Gift Card
     *
     * @param int $cartId
     * @return \Magestore\Giftvoucher\Api\Data\GiftcodeInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getExistedGiftCodes($cartId)
    {
        $quote = $this->getQuote($cartId);
        $store = $this->storeManager->getStore($this->helper->getStoreId($quote));
        $customerId = $quote->getCustomerId();
        $customerEmail = $quote->getCustomerEmail();

        $collection = $this->voucherCollectionFactory->create()
            ->addFieldToFilter('main_table.customer_id', $customerId);
        $collection->getExistedGiftcodes($customerId, $customerEmail);

        $giftCards = array();
        $addedCodes = array();
        if ($codes = $quote->getGiftVoucherGiftCodes()) {
            $addedCodes = explode(',', $codes);
        }
        $ruleModel = $this->giftvoucherFactory->create();

        foreach ($collection as $item) {
            if (in_array($item->getGiftCode(), $addedCodes)) {
                continue;
            }
            if ($item->getConditionsSerialized()) {
                if (class_exists('\Magento\Framework\Serialize\Serializer\Json')) {
                    $conditionsArr = $this->_objectManager->create('\Magento\Framework\Serialize\Serializer\Json')->unserialize($item->getConditionsSerialized());
                } else {
                    $conditionsArr = unserialize($item->getConditionsSerialized());
                }

                if (!empty($conditionsArr) && is_array($conditionsArr)) {
                    $ruleModel->getConditions()->setConditions(array())->loadArray($conditionsArr);
                    if ($quote->isVirtual()) {
                        $address = $quote->getBillingAddress();
                    } else {
                        $address = $quote->getShippingAddress();
                    }
                    if (!$ruleModel->validate($address)) {
                        continue;
                    }
                }
            }
            $giftCards[] = array(
                GiftcodeInterface::GIFT_CODE => $item->getGiftCode(),
                GiftcodeInterface::BALANCE => $this->_getGiftCardBalance($item, $store)
            );
        }
        return $giftCards;
    }

    /**
     * @param $item
     * @param $store
     * @return mixed
     */
    protected function _getGiftCardBalance($item, $store)
    {
        $cardCurrency = $this->currencyFactory->create()->load($item->getCurrency());
        $baseCurrency = $store->getBaseCurrency();
        $currentCurrency = $store->getCurrentCurrency();
        if ($cardCurrency->getCode() == $currentCurrency->getCode()) {
            return $this->formatPrice($store, $item->getBalance());
        }
        if ($cardCurrency->getCode() == $baseCurrency->getCode()) {
            return $this->convertPrice($store, $item->getBalance(), true);
        }
        if ($baseCurrency->convert(100, $cardCurrency)) {
            $amount = $item->getBalance() * $baseCurrency->convert(100, $currentCurrency)
                / $baseCurrency->convert(100, $cardCurrency);
            return $this->formatPrice($store, $amount);
        }
        return $cardCurrency->format($store, $item->getBalance(), array(), true);
    }

    /**
     * Retrieve formated price
     *
     * @param $store
     * @param float $value
     * @return string
     */
    public function formatPrice($store, $value)
    {
        return $this->priceCurrency->format(
            $value,
            false,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $store
        );
    }

    /**
     * Convert price
     *
     * @param $store
     * @param float $value
     * @param bool $format
     * @return float
     */
    public function convertPrice($store, $value, $format = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat(
                $value,
                true,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $store
            )
            : $this->priceCurrency->convert($value, $store);
    }

    /**
     * @param int $cartId
     * @return \Magento\Quote\Api\Data\CartInterface
     */
    public function getQuote($cartId)
    {
        $quote = $this->quoteRepository->get($cartId);
        return $quote;
    }

    /**
     * @param $cartId
     * @return $this
     */
    public function clearData($cartId)
    {
        $quote = $this->getQuote($cartId);
        $quote->setGiftVoucherGiftCodes('');
        $quote->setGiftVoucherGiftCodesDiscount('');
        $quote->setGiftVoucherGiftCodesMaxDiscount('');
        $quote->setGiftvoucherBaseHiddenTaxAmount(0);
        $quote->setGiftvoucherHiddenTaxAmount(0);
        $quote->setBaseGiftVoucherDiscount(0);
        $quote->setGiftVoucherDiscount(0);
        $quote->setCodesBaseDiscount('');
        $quote->setCodesDiscount('');
        $this->quoteRepository->save($quote);
        return $this;
    }

    /**
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher
     * @param int $customerId
     * @return bool
     */
    public function validateCustomer(\Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher, $customerId)
    {
        if (!($giftvoucher instanceof \Magestore\Giftvoucher\Model\Giftvoucher)) {
            return false;
        }
        if (!$giftvoucher->getId()) {
            return false;
        }
        $shareCard = intval($this->helper->getGeneralConfig('share_card'));
        if ($shareCard < 1) {
            return true;
        }
        $customersUsed = $giftvoucher->getCustomerIdsUsed();
        if ($shareCard > count($customersUsed) || in_array($customerId, $customersUsed)
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param int $cartId
     * @param array $addedCodes
     * @param string $existedCode
     * @param string $newCode
     * @return \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface
     */
    public function applyCodes($cartId, $addedCodes = [], $existedCode = '', $newCode = '')
    {
        $result = [
            ResponseInterface::ERRORS => [],
            ResponseInterface::SUCCESS => [],
            ResponseInterface::NOTICES => []
        ];
        $quote = $this->getQuote($cartId);
        if ($quote->getCouponCode() && !$this->helper->getGeneralConfig('use_with_coupon')) {
            $this->clearData($cartId);
            $result[ResponseInterface::NOTICES][] = __('A coupon code has been used. You cannot apply gift codes with the coupon to get discount.');
        } else {
            if (count($addedCodes)) {
                $giftMaxUseAmount = unserialize($quote->getGiftVoucherGiftCodesMaxDiscount());
                if (!is_array($giftMaxUseAmount)) {
                    $giftMaxUseAmount = array();
                }
                foreach ($addedCodes as $addedCode) {
                    $giftMaxUseAmount[$addedCode[GiftcodeDiscountInterface::CODE]] = $addedCode[GiftcodeDiscountInterface::DISCOUNT];
                }
                $quote->setGiftVoucherGiftCodesMaxDiscount(serialize($giftMaxUseAmount));
                $quote->collectTotals();
                $this->quoteRepository->save($quote);
            }
            $giftCodes = [];
            if ($existedCode) {
                $giftCodes[] = $existedCode;
            }
            if ($newCode) {
                $giftCodes[] = $newCode;
            }
            if (count($giftCodes)) {
                /** @var \Magento\Framework\App\State $state */
                $state = $this->_objectManager->get('Magento\Framework\App\State');
                if ('frontend' == $state->getAreaCode()) {
                    $max = $this->helper->getGeneralConfig('maximum');
                    if (!$this->helper->isAvailableToAddCode()) {
                        $result[ResponseInterface::ERRORS][] = __('The maximum number of times to enter gift codes is %1!', $max);
                        return $result;
                    }
                }
                foreach ($giftCodes as $code) {
                    $giftVoucher = $this->giftvoucherFactory->create()->loadByCode($code);
                    if (!$giftVoucher->getGiftCode()) {
                        // Max times to enter gift code incorrectly
                        if ('frontend' == $state->getAreaCode()) {
                            $session = $this->_objectManager->get('Magestore\Giftvoucher\Model\Session');
                            $codes = $session->getCodes();
                            $codes[] = $code;
                            $codes = array_unique($codes);
                            $session->setCodes($codes);
                        }
                        if ('frontend' == $state->getAreaCode() && $max) {
                            $result[ResponseInterface::ERRORS][] = __('Gift Card "%1" does not exist.', $code) . ' '
                                . __('You have %1 time(s) remaining to re-enter Gift Card code.', $max - count($codes));
                        } else {
                            $result[ResponseInterface::ERRORS][] = __('Gift Card "%1" does not exist.', $code);
                        }
                        continue;
                    }
                    if (!$this->validateCustomer($giftVoucher, $quote->getCustomerId())) {
                        $result[ResponseInterface::ERRORS][] = __('This gift code limits the number of users');
                        continue;
                    }
                    if ($giftVoucher->getBaseBalance() > 0
                        && $giftVoucher->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE
                        && $giftVoucher->validate($quote->setQuote($quote))
                        && $giftVoucher->isValidWebsite($this->helper->getStoreId($quote))
                    ) {
                        $this->addVoucherToQuote($cartId, $giftVoucher);
                        if ($giftVoucher->getCustomerId() == $quote->getCustomerId()
                            && $giftVoucher->getRecipientName() && $giftVoucher->getRecipientEmail()
                            && $giftVoucher->getCustomerId()
                        ) {
                            $result[ResponseInterface::NOTICES][] = __('Gift Card "%1" has been sent to the customer\'s friend.', $code);
                        }
                        $result[ResponseInterface::SUCCESS][] = __('Gift Card "%1" has been applied successfully.', $code);
                    } elseif ($giftVoucher->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE
                        && $giftVoucher->isValidWebsite($this->helper->getStoreId($quote))
                    ) {
                        $this->addVoucherToQuote($cartId, $giftVoucher);
                        $result[ResponseInterface::NOTICES][] = __('You can’t use this gift code since its conditions haven’t been met.');
                    } else {
                        $result[ResponseInterface::ERRORS][] = __('Gift Card "%1" is no longer available to use.', $code);
                    }
                }
            } else {
                $result[ResponseInterface::SUCCESS][] = __('Gift Card has been updated successfully.');
            }
        }
        return $result;
    }

    /**
     * @param int $cartId
     * @param string $giftCode
     * @return \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface
     */
    public function removeCode($cartId, $giftCode = '')
    {
        $result = [
            ResponseInterface::ERRORS => [],
            ResponseInterface::SUCCESS => [],
            ResponseInterface::NOTICES => []
        ];
        $quote = $this->getQuote($cartId);
        $codes = $quote->getGiftVoucherGiftCodes();

        $success = false;
        if ($giftCode && $codes) {
            $codesArray = explode(',', $codes);
            foreach ($codesArray as $key => $value) {
                if ($value == $giftCode) {
                    unset($codesArray[$key]);
                    $success = true;
                    $giftMaxUseAmount = unserialize($quote->getGiftVoucherGiftCodesMaxDiscount());
                    if (is_array($giftMaxUseAmount) && array_key_exists($giftCode, $giftMaxUseAmount)) {
                        unset($giftMaxUseAmount[$giftCode]);
                        $quote->setGiftVoucherGiftCodesMaxDiscount(serialize($giftMaxUseAmount));
                        $this->quoteRepository->save($quote);
                    }
                    break;
                }
            }
        }
        if ($success) {
            $codes = implode(',', $codesArray);
            $quote->setGiftVoucherGiftCodes($codes);
            if (empty($codesArray)) {
                $this->clearData($cartId);
            }
            $result[ResponseInterface::SUCCESS][] = __('Gift card "%1" has been removed successfully.', $giftCode);
        } else {
            $result[ResponseInterface::ERRORS][] = __('Gift card "%1" not found!', $giftCode);
        }
        return $result;
    }

    /**
     * @param int $cartId
     * @return \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface
     */
    public function removeCodes($cartId)
    {
        $result = [
            ResponseInterface::ERRORS => [],
            ResponseInterface::SUCCESS => [],
            ResponseInterface::NOTICES => []
        ];
        $this->clearData($cartId);
        $result[ResponseInterface::SUCCESS][] = __('Your Gift Card has been removed successfully.');
        return $result;
    }

    /**
     * @param int $cartId
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftVoucher
     * @return $this
     */
    public function addVoucherToQuote($cartId, \Magestore\Giftvoucher\Model\Giftvoucher $giftVoucher)
    {
        $quote = $this->getQuote($cartId);
        if ($codes = $quote->getGiftVoucherGiftCodes()) {
            $codesArray = explode(',', $codes);
            $codesArray[] = $giftVoucher->getGiftCode();
            $codes = implode(',', array_unique($codesArray));
        } else {
            $codes = $giftVoucher->getGiftCode();
        }
        $quote->setGiftVoucherGiftCodes($codes);
        $this->quoteRepository->save($quote);
        return $this;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    public function processOrderPlaceAfter(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        if (!($order->getGiftVoucherDiscount())) {
            return $this;
        }
        $codes = $order->getGiftVoucherGiftCodes();
        if ($codes) {
            $store = $order->getStore();
            $order->setGiftvoucherForOrderCodes($codes)
                ->setGiftvoucherForOrderAmount($order->getGiftVoucherDiscount());
            $codesArray        = explode(',', $codes);
            $codesBaseDiscount = explode(',', $order->getCodesBaseDiscount());
            $codesDiscount     = explode(',', $order->getCodesDiscount());
            $baseDiscount      = array_combine($codesArray, $codesBaseDiscount);
            $discount          = array_combine($codesArray, $codesDiscount);
            foreach ($codesArray as $code) {
                if (!$baseDiscount[$code] || $this->priceCurrency->round($baseDiscount[$code]) == 0) {
                    continue;
                }
                $giftVoucher = $this->giftvoucherFactory->create()->loadByCode($code);

                $baseCurrencyCode = $order->getBaseCurrencyCode();
                $baseCurrency     = $this->currencyFactory->create()->load($baseCurrencyCode);
                $currentCurrency  = $this->currencyFactory->create()->load($giftVoucher->getData('currency'));

                $codeDiscount        = $this->directoryHelper
                    ->currencyConvert($baseDiscount[$code], $baseCurrencyCode, $giftVoucher->getData('currency'));
                $codeCurrentDiscount = $this->directoryHelper
                    ->currencyConvert($baseDiscount[$code], $baseCurrencyCode, $store->getCurrentCurrencyCode());
                $balance             = $giftVoucher->getBalance() - $codeDiscount;
                if ($balance > 0) {
                    $baseBalance = $balance * $balance / $baseCurrency->convert($balance, $currentCurrency);
                } else {
                    $baseBalance = 0;
                }
                $currentBalance = $this->directoryHelper
                    ->currencyConvert($baseBalance, $baseCurrencyCode, $store->getCurrentCurrencyCode());
                $giftVoucher->setData('balance', $balance);
                $this->giftvoucherRepository->save($giftVoucher);
                if ($order->getData('customer_id') == null) {
                    $customerName = __('Used by Guest');
                } else {
                    $customerName = __('Used by %1 %1', $order->getData('customer_firstname'),
                        $order->getData('customer_lastname'));
                }
                $this->historyFactory->create()->setData(array(
                    'order_increment_id' => $order->getIncrementId(),
                    'giftvoucher_id' => $giftVoucher->getId(),
                    'created_at' => date("Y-m-d H:i:s"),
                    'action' => \Magestore\Giftvoucher\Model\Actions::ACTIONS_SPEND_ORDER,
                    'amount' => $codeCurrentDiscount,
                    'balance' => $currentBalance,
                    'currency' => $store->getCurrentCurrencyCode(),
                    'status' => $giftVoucher->getStatus(),
                    'order_amount' => $discount[$code],
                    'comments' => __('Spent on order %1', $order->getIncrementId()),
                    'extra_content' => $customerName,
                    'customer_id' => $order->getData('customer_id'),
                    'customer_email' => $order->getData('customer_email')
                ))->save();

                // add gift code to customer list
                if ($order->getCustomerId() && ($balance > 0)) {
                    $collection = $this->giftVoucherCollectionFactory->create()
                        ->addFieldToFilter('customer_id', $order->getCustomerId())
                        ->addFieldToFilter('voucher_id', $giftVoucher->getId());
                    if (!$collection->getSize()) {
                        try {
                            $timeSite = date(
                                "Y-m-d",
                                $this->helperData->getObjectManager()
                                    ->get('Magento\Framework\Stdlib\DateTime\DateTime')->timestamp(time())
                            );
                            $this->customerVoucherFactory->create()
                                ->setCustomerId($order->getCustomerId())
                                ->setVoucherId($giftVoucher->getId())
                                ->setAddedDate($timeSite)
                                ->save();
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }
        }

        // Create invoice for Order payed by Giftvoucher
        if ($this->priceCurrency->round($order->getGrandTotal()) == 0
            && $order->getPayment()->getMethod() == 'free'
            && $order->canInvoice()
        ) {
            try {
                $invoice = $order->prepareInvoice()->register();
                $order->addRelatedObject($invoice);
                if ($order->getState() == 'new') {
                    $order->setState('processing');
                }

                if ($order->getStatus() == 'pending') {
                    $order->setStatus('processing');
                }
            } catch (\Exception $e) {
                return $this;
            }
        }
    }
}
