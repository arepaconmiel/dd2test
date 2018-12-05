<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Redeem;

/**
 * Class Form
 * @package Magestore\Giftvoucher\Block\Adminhtml\Redeem
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{

    /**
     * Session quote
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * Order create
     *
     * @var \Magento\Sales\Model\AdminOrder\Create
     */
    protected $_orderCreate;

    /**
     * @var \Magestore\Giftvoucher\Service\Redeem\CheckoutService
     */
    protected $checkoutService;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $_helper;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher
     * @param \Magestore\Giftvoucher\Model\Credit $credit
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService,
        \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher,
        \Magestore\Giftvoucher\Model\Credit $credit,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Giftvoucher\Helper\Data $helper,
        array $data = []
    ) {
        $this->_localeCurrency = $localeCurrency;
        $this->checkoutService = $checkoutService;
        $this->_giftvoucher = $giftvoucher;
        $this->_credit = $credit;
        $this->_checkoutSession = $checkoutSession;
        $this->_objectManager = $objectManager;
        $this->_helper = $helper;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_helper->getGeneralConfig('active');
    }

    /**
     * @param bool $isJson
     * @param string $key
     * @return array|mixed|string
     */
    public function getFormData($isJson = true, $key = '')
    {
        $cartId = $this->getQuote()->getId();
        $data = [];
        $data['quote_id'] = $cartId;
        $data['is_buying_giftcard'] = $this->hasGiftcardOnly();
        $data['using_codes'] = $this->checkoutService->getUsingGiftCodes($cartId);
        $data['existing_codes'] = $this->checkoutService->getExistedGiftCodes($cartId);
        $data['apply_url'] = $this->getUrl('giftvoucheradmin/checkout/apply', ['form_key' => $this->getFormKey()]);
        $data['remove_url'] = $this->getUrl('giftvoucheradmin/checkout/remove', ['form_key' => $this->getFormKey()]);
        $data['remove_all_url'] = $this->getUrl('giftvoucheradmin/checkout/removeAll', ['form_key' => $this->getFormKey()]);
        if ($key) {
            $data = (isset($data[$key]))?$data[$key]:'';
        }
        return ($isJson)?\Zend_Json::encode($data):$data;
    }

    /**
     * @return int
     */
    public function hasGiftcardOnly()
    {
        $items = $this->getQuote()->getAllItems();
        $hasGiftcardOnly = false;
        if ($items && count($items) > 0) {
            $hasGiftcardOnly = true;
            foreach ($items as $item) {
                $data = $item->getData();
                if ($data['product_type'] != 'giftvoucher') {
                    $hasGiftcardOnly = false;
                }
            }
        }
        return $hasGiftcardOnly;
    }
}
