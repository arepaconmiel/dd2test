<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Product;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class View
 * @package Magestore\Giftvoucher\Block\Product
 */
class View extends \Magento\Catalog\Block\Product\View\AbstractView
{

    /**
     * @var array
     */
    protected $options;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $catalogProduct;

    /**
     * @var \Magento\Bundle\Model\Product\PriceFactory
     */
    protected $productPriceFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Magento\Framework\Locale\FormatInterface
     */
    protected $_localeFormat;

    /**
     * Giftproduct data
     *
     * @var \Magento\Bundle\Helper\Giftproduct
     */
    protected $_giftproductData = null;
    
    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogHelper;
    
    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;
    
    /**
     * Giftvoucher data
     *
     * @var \Magento\Bundle\Helper\Giftvoucher
     */
    protected $_giftvoucherData = null;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_dataObject;
    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherConfigProvider\CompositeConfigProvider
     */
    protected $_configModel;

    /**
     * View constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magestore\Giftvoucher\Helper\Giftproduct $helperData
     * @param \Magestore\Giftvoucher\Helper\Data $giftvoucherData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Giftvoucher\Model\Product\PriceFactory $productPrice
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Framework\DataObject $dataObject
     * @param \Magestore\Giftvoucher\Model\GiftvoucherConfigProvider\CompositeConfigProvider $configModel
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magestore\Giftvoucher\Helper\Giftproduct $helperData,
        \Magestore\Giftvoucher\Helper\Data $giftvoucherData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Giftvoucher\Model\Product\PriceFactory $productPrice,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Framework\DataObject $dataObject,
        \Magestore\Giftvoucher\Model\GiftvoucherConfigProvider\CompositeConfigProvider $configModel,
        array $data = []
    ) {
        $this->_objectManager = $objectManager;
        $this->_giftvoucherData = $giftvoucherData;
        $this->_priceCurrency = $priceCurrency;
        $this->_catalogHelper = $context->getCatalogHelper();
        $this->_giftproductData = $helperData;
        $this->catalogProduct = $catalogProduct;
        $this->productPriceFactory = $productPrice;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_localeFormat = $localeFormat;
        $this->_dataObject = $dataObject;
        $this->_configModel = $configModel;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }
    /**
     * Retrieve webpos configuration
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getGiftvoucherConfig()
    {
        return $this->_configModel->getConfig();
    }

    /**
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $product = $this->getProduct();
        $media = $this->getLayout()->getBlock('product.info.media.image');

        if ($media && $product->getTypeId() == 'giftvoucher'
            && $product->getGiftCardType()!= \Magestore\Giftvoucher\Model\Source\GiftCardTypeOptions::TYPE_PHYSICAL ) {
            $media->setTemplate('Magestore_Giftvoucher::giftvoucher/product/media.phtml');
        }
    }
    

    /**
     * @return $this
     */
    public function getFormConfigData()
    {
        $store = $this->_storeManager->getStore();
        $request = $this->_request;
        $formData = array();
        $result = array();
        if ($this->isInConfigurePage()) {
            $options = $this->_objectManager->create('Magento\Quote\Model\Quote\Item\Option')->getCollection()
                ->addItemFilter($request->getParam('id'));

            foreach ($options as $option) {
                $result[$option->getCode()] = $option->getValue();
            }

            if (isset($result['base_gc_value'])) {
                if (isset($result['gc_product_type']) && $result['gc_product_type'] == 'range') {
                    $currency = $store->getCurrentCurrencyCode();
                    $baseCurrencyCode = $store->getBaseCurrencyCode();

                    if ($currency != $baseCurrencyCode) {
                        $currentCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                            ->load($currency);
                        $baseCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
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
        $dataObject = $this->_dataObject->setData($formData);

        return $dataObject;
    }

    /**
     * @return bool
     */
    public function isInConfigurePage()
    {
        $request = $this->_request;
        $action = $request->getFullActionName();
        
        if ($action == 'checkout_cart_configure' && $request->getParam('id')) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function contentCondition()
    {
        $giftProduct = $this->_objectManager->create('Magestore\Giftvoucher\Model\Product')
            ->loadByProduct($this->getProduct());
        if ($giftProduct->getGiftcardDescription()) {
            return $giftProduct->getGiftcardDescription();
        }
        return false;
    }

    /**
     * @return \Magento\Bundle\Helper\Giftvoucher|\Magestore\Giftvoucher\Helper\Data
     */
    public function getGiftvoucherHelper()
    {
        return $this->_giftvoucherData;
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequestInterface()
    {
        return $this->_request;
    }

    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * @return \Magento\Framework\Json\EncoderInterface
     */
    public function getJsonEncode()
    {
        return $this->_jsonEncoder;
    }

    /**
     * @return \Magento\Tax\Helper\Data
     */
    public function getTaxHelper()
    {
        return $this->_taxData;
    }

    /**
     * @return \Magento\Catalog\Helper\Data
     */
    public function getCatalogHelper()
    {
        return $this->_catalogHelper;
    }

    /**
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }
}
