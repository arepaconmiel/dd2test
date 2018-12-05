<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\GiftvoucherConfigProvider;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class DefaultConfigProvider extends \Magento\Framework\DataObject implements ConfigProviderInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Timezone
     */
    protected $_timezone;
    /**
     * @var \Magestore\Giftvoucher\Model\GiftTemplateFactory
     */
    protected $_giftTemplateFactory;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magestore\Giftvoucher\Helper\Giftproduct
     */
    protected $_giftproductData;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;
    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData;
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $giftVoucherHelper;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var LocaleFormat
     */
    protected $localeFormat;
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;
    /**
     * @var \Magento\Framework\View\Element\Template
     */
    protected $_template;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $itemFactory;
    /**
     * @var \Magento\Quote\Model\Quote\Item\OptionFactory
     */
    protected $optionFactory;
    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;
    /**
     * @var \Magento\Framework\DataObject
     */
    protected $dataObject;
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface
     */
    protected $IOService;
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface
     */
    protected $sampleDataService;

    /**
     * DefaultConfigProvider constructor.
     * @param \Magento\Config\Model\Config\Source\Locale\Timezone $timezone
     * @param \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Giftvoucher\Helper\Giftproduct $helperData
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Helper\Data $catalogHelper
     * @param \Magento\Tax\Helper\Data $taxData
     * @param CheckoutSession $checkoutSession
     * @param LocaleFormat $localeFormat
     * @param \Magestore\Giftvoucher\Helper\Data $giftVoucherHelper
     * @param \Magento\Framework\View\Asset\Repository $asset
     * @param \Magento\Framework\View\Element\Template $template
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Quote\Model\Quote\ItemFactory $itemFactory
     * @param \Magento\Quote\Model\Quote\Item\OptionFactory $optionFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\DataObject $dataObject
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $IOService
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface $sampleDataService
     * @param array $data
     */
    public function __construct(
        \Magento\Config\Model\Config\Source\Locale\Timezone $timezone,
        \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Giftvoucher\Helper\Giftproduct $helperData,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Tax\Helper\Data $taxData,
        CheckoutSession $checkoutSession,
        LocaleFormat $localeFormat,
        \Magestore\Giftvoucher\Helper\Data $giftVoucherHelper,
        \Magento\Framework\View\Asset\Repository $asset,
        \Magento\Framework\View\Element\Template $template,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory,
        \Magento\Quote\Model\Quote\Item\OptionFactory $optionFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\DataObject $dataObject,
        \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $IOService,
        \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface $sampleDataService,
        array $data = []
    ) {
    
        $this->_timezone = $timezone;
        $this->_giftTemplateFactory = $giftTemplateFactory;
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_giftproductData = $helperData;
        $this->_priceCurrency = $priceCurrency;
        $this->_catalogHelper = $catalogHelper;
        $this->_taxData = $taxData;
        $this->giftVoucherHelper = $giftVoucherHelper;
        $this->checkoutSession = $checkoutSession;
        $this->localeFormat = $localeFormat;
        $this->_assetRepo = $asset;
        $this->_template = $template;
        $this->request = $request;
        $this->itemFactory = $itemFactory;
        $this->optionFactory = $optionFactory;
        $this->currencyFactory = $currencyFactory;
        $this->dataObject = $dataObject;
        $this->IOService = $IOService;
        $this->sampleDataService = $sampleDataService;
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $product = $this->getProduct();
        $timezones = $this->_timezone->toOptionArray();
        $timezoneArray = array();
        foreach ($timezones as $timezone) {
            $timezoneArray[] = $timezone;
        }
        $output['timezones'] = $timezoneArray;
        $output['templates'] = $this->getAvailableTemplate();
        $output['imageBaseUrl'] = $this->_storeManager
                ->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'giftvoucher/template/images';
        $output['customImageBaseUrl'] = $this->_storeManager
                ->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'tmp/giftvoucher/images';
        $output['giftAmount'] = $this->getGiftAmount($product);
        $output['messageMaxLength'] = $this->giftVoucherHelper->getInterfaceConfig('max');
        $output['priceFormat'] = $this->localeFormat->getPriceFormat(
            null,
            $this->checkoutSession->getQuote()->getQuoteCurrencyCode()
        );
        $output['settings'] = $this->giftVoucherHelper->getStoreConfig('giftvoucher');
        $output['uploadUrl'] = $this->_storeManager->getStore()->getUrl("giftvoucher/index/uploadImageAjax");
        $output['giftCardType'] =  $this->getProduct()->getData('gift_card_type');
        $output['enableScheduleSend'] =  $this->giftVoucherHelper->getInterfaceConfig('schedule');
        $output['barCodeUrl'] =$this->_template->getViewFileUrl('Magestore_Giftvoucher::images/template/barcode/default.png');
        $output['defaultBackground'] =$this->_template->getViewFileUrl('Magestore_Giftvoucher::images/template/images/default.png');
        $timeLife = $this->giftVoucherHelper->getGeneralConfig('expire');
        $timeSite = date("m/d/Y", $this->giftVoucherHelper->getObjectManager()->get('Magento\Framework\Stdlib\DateTime\DateTime')->timestamp(time()));
        $expire_day = date('m/d/Y', strtotime($timeSite . '+' . $timeLife . ' days'));
        if ($this->giftVoucherHelper->getGeneralConfig('show_expiry_date')) {
            $output['expireDay'] = $expire_day;
        } else {
            $output['expireDay'] = '';
        }

        $output['logo_url'] = $this->sampleDataService->getLogo();
        $output['notes'] = $this->sampleDataService->getNotesSample();

        if ($this->giftVoucherHelper->getCustomerSession()->isLoggedIn()) {
            $output['customerName'] = $this->giftVoucherHelper->getObjectManager()->get('Magento\Customer\Helper\View')
                ->getCustomerName($this->giftVoucherHelper->getCustomerSession()->getCustomerData());
        } else {
            $output['customerName'] = '';
        }
        if ($this->giftVoucherHelper->getInterfaceConfig('postoffice_date')) {
            $output['postOfficeDate'] = $this->giftVoucherHelper->getInterfaceConfig('postoffice_date');
        } else {
            $output['postOfficeDate'] = '';
        }

        if ($product->getGiftCardType() == \Magestore\Giftvoucher\Model\Source\GiftCardTypeOptions::TYPE_PHYSICAL) {
            $output['defaultCheckedPostal'] = true;
        } else {
            $output['defaultCheckedPostal'] = false;
        }

        if ($product->getGiftCardType() == \Magestore\Giftvoucher\Model\Source\GiftCardTypeOptions::TYPE_VIRTUAL) {
            $output['defaultCheckedSender'] = true;
        } else {
            $output['defaultCheckedSender'] = false;
        }

        $output['defaultNotifySuccess'] = true;
        $output['isCustomImage'] = 0;

        $itemId = $this->request->getParam('id');

        $output['additionalInfo'] = array(
            'amount' => 0,
            'customer_name' => $output['customerName'],
            'recipient_name' => '',
            'recipient_email' => '',
            'message' => '',
            'day_to_send' => '',
            'timezone_to_send' => '',
            'recipient_address' => '',
            'notify_success' => 0,
            'giftcard_template_image' => '',
            'giftcard_use_custom_image' => false
        );

        if ($itemId) {
            $itemModel = $this->itemFactory->create()->load($itemId);
            if ($itemModel->getId()) {
                $output['additionalInfo']['amount'] = $this->getFormConfigData()->getData('amount');
                $output['additionalInfo']['base_gc_currency'] = $this->getFormConfigData()->getData('base_gc_currency');
                $output['additionalInfo']['base_gc_value'] = $this->getFormConfigData()->getData('base_gc_value');
                $output['additionalInfo']['day_to_send'] = $this->getFormConfigData()->getData('day_to_send');
                $output['additionalInfo']['gc_product_type'] = $this->getFormConfigData()->getData('gc_product_type');
                $output['additionalInfo']['giftcard_template_id'] = $this->getFormConfigData()->getData('giftcard_template_id');
                $output['additionalInfo']['giftcard_template_image'] = $this->getFormConfigData()->getData('giftcard_template_image');
                $output['additionalInfo']['info_buyRequest'] = $this->getFormConfigData()->getData('info_buyRequest');
                $output['additionalInfo']['notify_success'] = $this->getFormConfigData()->getData('notify_success');
                $output['additionalInfo']['price_amount'] = $this->getFormConfigData()->getData('price_amount');
                $output['additionalInfo']['recipient_email'] = $this->getFormConfigData()->getData('recipient_email');
                $output['additionalInfo']['recipient_name'] = $this->getFormConfigData()->getData('recipient_name');
                $output['additionalInfo']['send_friend'] = $this->getFormConfigData()->getData('send_friend');
                $output['additionalInfo']['timezone_to_send'] = $this->getFormConfigData()->getData('timezone_to_send');
                if ($this->getFormConfigData()->getData('giftcard_use_custom_image')) {
                    $output['additionalInfo']['giftcard_use_custom_image'] = true;
                } else {
                    $output['additionalInfo']['giftcard_use_custom_image'] = false;
                }

                $output['defaultCheckedSender'] =  $output['additionalInfo']['send_friend'];
                $output['defaultNotifySuccess'] = $output['additionalInfo']['notify_success'];
            }
        }
        if (!$output['additionalInfo']['timezone_to_send']) {
            $output['additionalInfo']['timezone_to_send'] = $this->_storeManager->getStore()->getConfig('general/locale/timezone');
        }
        $productTemplate = $product->getGiftTemplateIds();
        $productTemplateArray = explode(',', $productTemplate);
        $templateIdPreviewFile = array();
        foreach ($productTemplateArray as $templateId) {
            $templateIdPreviewFile[$templateId] = $this->IOService->getTemplateFile($templateId);
        }
        $output['previewTemplates'] = $templateIdPreviewFile;

        return $output;
    }

    /**
     * Get the price information of Gift Card product
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @return array
     */
    public function getGiftAmount($product)
    {
        $giftValue = $this->_giftproductData->getGiftValue($product);
        switch ($giftValue['type']) {
            case 'range':
                $giftValue['from'] = $this->convertPrice($product, $giftValue['from']);
                $giftValue['to'] = $this->convertPrice($product, $giftValue['to']);
                break;
            case 'dropdown':
                $giftValue['options'] = $this->_convertPrices($product, $giftValue['options']);
                $giftValue['prices'] = $this->_convertPrices($product, $giftValue['prices']);
                $giftValue['prices'] = array_combine($giftValue['options'], $giftValue['prices']);
                break;
            case 'static':
                $giftValue['value'] = $this->convertPrice($product, $giftValue['value']);
                $giftValue['price'] = $this->convertPrice($product, $giftValue['gift_price']);
                break;
            default:
                $giftValue['type'] = 'any';
        }
        return $giftValue;
    }

    /**
     * Formatted Gift Card price
     *
     * @param array $prices
     * @return array
     */
    protected function _formatPrices($prices)
    {
        foreach ($prices as $key => $price) {
            $prices[$key] = $this->_priceCurrency->format($price);
        }
        return $prices;
    }


    /**
     * Get Gift Card product price with all tax settings processing
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @param float $price
     * @return float
     */
    public function convertPrice($product, $price)
    {
        $includeTax = ( $this->_taxData->getPriceDisplayType() != 1 );

        $priceWithTax = $this->_catalogHelper->getTaxPrice($product, $price, $includeTax);
        return $this->_priceCurrency->convert($priceWithTax);
    }


    /**
     * Convert Gift Card base price
     *
     * @param \Magestore\Giftvoucher\Model\Product $product
     * @param float $basePrices
     * @return float
     */
    protected function _convertPrices($product, $basePrices)
    {
        foreach ($basePrices as $key => $price) {
            $basePrices[$key] = $this->convertPrice($product, $price);
        }
        return $basePrices;
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        $product = $this->getData('product');
        if ($product && $product->getTypeInstance()->getStoreFilter($product) === null) {
            $product->getTypeInstance()->setStoreFilter($this->_storeManager->getStore(), $product);
        }
        return $product;
    }

    /**
     * @return array
     */
    public function getAvailableTemplate()
    {
        $product = $this->getProduct();
        $productTemplate = $product->getGiftTemplateIds();
        if ($productTemplate) {
            $productTemplate = explode(',', $productTemplate);
        } else {
            $productTemplate = array();
        }

        $templates = $this->_giftTemplateFactory->create()->getCollection()
            ->addFieldToFilter('status', '1')
            ->addFieldToFilter('giftcard_template_id', array('in' => $productTemplate));
        $templateData = array();
        foreach ($templates as $template) {
            $template->setData('template_file', $this->IOService->getTemplateFile($template->getId()));
            $templateData[] = $template->getData();
        }
        return $templateData;
    }

    /**
     * @return $this
     */
    public function getFormConfigData()
    {
        $store = $this->_storeManager->getStore();
        $request = $this->request;
        $formData = array();
        $result = array();
        if ($this->isInConfigurePage()) {
            $options = $this->optionFactory->create()->getCollection()
                ->addItemFilter($request->getParam('id'));

            foreach ($options as $option) {
                $result[$option->getCode()] = $option->getValue();
            }

            if (isset($result['base_gc_value'])) {
                if (isset($result['gc_product_type']) && $result['gc_product_type'] == 'range') {
                    $currency = $store->getCurrentCurrencyCode();
                    $baseCurrencyCode = $store->getBaseCurrencyCode();

                    if ($currency != $baseCurrencyCode) {
                        $currentCurrency = $this->currencyFactory->create()
                            ->load($currency);
                        $baseCurrency = $this->currencyFactory->create()
                            ->load($baseCurrencyCode);

                        $value = $this->_priceCurrency
                            ->round($baseCurrency->convert($result['base_gc_value'], $currentCurrency));
                    } else {
                        $value = $this->_priceCurrency->round($result['base_gc_value']);
                    }
                }
            }

            foreach ($options as $option) {
                if ($option->getCode() == 'amount') {
                    if (isset($value)) {
                        $formData[$option->getCode()] = $value;
                    } else {
                        $formData[$option->getCode()] = $option->getValue();
                    }
                } else {
                    $formData[$option->getCode()] = $option->getValue();
                }
            }
        }
        $dataObject = $this->dataObject->setData($formData);

        return $dataObject;
    }

    /**
     * @return bool
     */
    public function isInConfigurePage()
    {
        $request = $this->request;
        $action = $request->getFullActionName();

        if ($action == 'checkout_cart_configure' && $request->getParam('id')) {
            return true;
        }
        return false;
    }
}
