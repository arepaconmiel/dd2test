<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Redeem;

/**
 * Class Form
 * @package Magestore\Giftvoucher\Block\Redeem
 */
class Form extends \Magento\Payment\Block\Form
{
    /**
     * @var \Magestore\Giftvoucher\Service\Redeem\CheckoutService
     */
    protected $checkoutService;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helper;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService,
        \Magestore\Giftvoucher\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->checkoutService = $checkoutService;
        $this->helper = $helper;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (
            $this->helper->getGeneralConfig('active') &&
            $this->helper->getStoreConfig('giftvoucher/interface_payment/show_gift_card')
        );
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
        $data['quote_id'] = $this->getQuote()->getId();
        $data['gift_voucher_discount'] = $this->checkoutService->getQuote($cartId)->getGiftVoucherDiscount();
        $data['is_buying_giftcard'] = $this->hasGiftcardOnly();
        $data['is_guest'] = ($this->helper->getCustomerSession()->isLoggedIn())?false:true;
        $data['using_codes'] = $this->checkoutService->getUsingGiftCodes($cartId);
        $data['existing_codes'] = $this->checkoutService->getExistedGiftCodes($cartId);
        $data['reload_form_url'] = $this->getUrl('giftvoucher/checkout/reloadForm');
        $data['apply_url'] = $this->getUrl('giftvoucher/checkout/apply');
        $data['remove_url'] = $this->getUrl('giftvoucher/checkout/remove');
        $data['remove_all_url'] = $this->getUrl('giftvoucher/checkout/removeAll');
        $data['manage_codes_url'] = $this->getUrl('giftvoucher/index/index');
        $data['check_codes_url'] = $this->getUrl('giftvoucher/index/check');
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

    /**
     * @return mixed
     */
    public function getQuote()
    {
        return $this->helper->getCheckoutSession()->getQuote();
    }
}
