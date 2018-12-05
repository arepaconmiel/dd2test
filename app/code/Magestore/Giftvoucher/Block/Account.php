<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Giftvoucher Account block
 */
class Account extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Customer\Helper\View
     */
    public $viewHelper;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    public $currentCustomer;

    /**
     * @var CustomerRepositoryInterface
     */
    public $customerRepository;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    public $httpContext;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;
    
    /**
     * Date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public $datetime;
    
    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    public $urlDecoder;
    
    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;
    
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory
     */
    protected $_collectionFactory;
    
    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherFactory
     */
    protected $_giftvoucherFactory;

    /**
     * Account constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CustomerRepositoryInterface $accountManagement
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Magento\Framework\Url\DecoderInterface $decode
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $collectionFactory
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CustomerRepositoryInterface $accountManagement,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Framework\Url\DecoderInterface $decode,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $collectionFactory,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerRepository = $accountManagement;
        $this->viewHelper = $viewHelper;
        $this->httpContext = $httpContext;
        $this->currentCustomer = $currentCustomer;
        $this->objectManager = $objectManager;
        $this->_isScopePrivate = true;
        $this->datetime = $datetime;
        $this->urlDecoder = $decode;
        $this->_imageFactory = $imageFactory;
        $this->_priceCurrency = $priceCurrency;
        $this->_helper = $helper;
        $this->_collectionFactory = $collectionFactory;
        $this->_giftvoucherFactory = $giftvoucherFactory;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerLoggedIn()
    {
        return $this->getCustomerSessionModel()->isLoggedIn();
    }

    /**
     * Return the full name of the customer currently logged in
     *
     * @return string|null
     */
    public function getCurrentCustomerName()
    {
        try {
            $customer = $this->customerRepository->getById($this->currentCustomer->getCustomerId());
            return $this->escapeHtml($this->viewHelper->getCustomerName($customer));
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getCustomerSessionModel()
    {
        return $this->objectManager->create('Magento\Customer\Model\Session');
    }

    /**
     * @return mixed
     */
    public function getMediaPath()
    {
        return $this->objectManager->get('Magento\Framework\Filesystem')->getUri(DirectoryList::MEDIA);
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->objectManager->get('Magento\Framework\Url')->getBaseUrl();
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->getCustomerSessionModel()->getCustomer();
    }

    /**
     * @return null
     */
    public function getCustomerData()
    {
        if (!$this->getCustomerSessionModel()->getCustomer()) {
            return null;
        }
        return $this->getCustomerSessionModel()->getCustomer()->getCustomerData();
    }

    /**
     * @return null
     */
    public function getCustomerDataObject()
    {
        if (!$this->getCustomerSessionModel()->getCustomer()) {
            return null;
        }
        return $this->getCustomerSessionModel()->getCustomer()->getCustomerDataObject();
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->getCustomerSessionModel()->getCustomer()->getId();
    }

    /**
     * @return mixed
     */
    public function getCustomerFirstname()
    {
        return $this->getCustomerSessionModel()->getCustomer()->getFirstname();
    }

    /**
     * @return mixed
     */
    public function getCustomerLastname()
    {
        return $this->getCustomerSessionModel()->getCustomer()->getLastname();
    }

    /**
     * @return mixed
     */
    public function getCustomerEmail()
    {
        return $this->getCustomerSessionModel()->getCustomer()->getEmail();
    }

    /**
     * @return mixed
     */
    public function getCurrencyModel()
    {
        return $this->objectManager->create('Magento\Framework\Locale\Currency');
    }

    /**
     * @return mixed
     */
    public function getDefaultCurrency()
    {
        return $this->getCurrencyModel()->getDefaultCurrency();
    }

    /**
     * @return mixed
     */
    public function getCurrencyFactory()
    {
        return $this->objectManager->create('Magento\Directory\Model\CurrencyFactory');
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        $currencyCode = $this->getDefaultCurrency();
        $currency = $this->getCurrencyFactory()->create();
        $currency->load($currencyCode);
        return $currency;
    }

    /**
     * @param $price
     * @param bool $addBrackets
     * @return mixed
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    /**
     * @param $price
     * @param $precision
     * @param bool $addBrackets
     * @return mixed
     */
    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
    }

    /**
     * @return \Magestore\Giftvoucher\Helper\Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * @param $modelName
     * @return mixed
     */
    public function getModel($modelName)
    {
        return $this->objectManager->create($modelName);
    }

    /**
     * @param $modelName
     * @return mixed
     */
    public function getSingleton($modelName)
    {
        return $this->objectManager->get($modelName);
    }

    /**
     * @param $file
     * @return string
     */
    public function getMediaDirPath($file)
    {
        return $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($file);
    }

    /**
     * @param $file
     * @return string
     */
    public function getSkinUrl($file)
    {
        return $this->getViewFileUrl('Magestore_Giftvoucher::' . $file);
    }

    /**
     * @return \Magento\Framework\Image\Adapter\AdapterInterface
     */
    public function getImage()
    {
        return $this->_imageFactory->create();
    }

    /**
     * convert amount from base currency to current currency
     *
     * @param $value
     * @param bool $format
     * @param null $currency
     * @return float|string
     */
    public function converCurrency($value, $format = true, $currency = null)
    {
        return $format ? $this->_priceCurrency->convertAndFormat(
            $value,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->getStore(),
            $currency
        ) : $this->_priceCurrency->convert($value, $this->getStore(), $currency);
    }

    /**
     * @param $amount
     * @param null $currency
     * @return \Magestore\Giftvoucher\Helper\type
     */
    public function formatCurrency($amount, $currency = null)
    {
        return $this->getHelper()->getCurrencyFormat($amount, $currency);
    }
    
    /**
     * Returns the HTML codes of the gift code's column
     *
     * @param mixed $row
     * @param string $type
     * @return string
     */
    public function getCodeTxt($row, $type)
    {
        $input = '<input style="width:auto;" id="input-gift-code' . $row->getId()
        . '" readonly type="text" class="input-text" value="' . $row->getGiftCode()
        . '" onblur="hiddencode' . $row->getId() . '(this);">';
        if ($type) {
            $aelement = '<a href="javascript:void(0);" onclick="">'
                . $this->getHelper()->getHiddenCode($row->getGiftCode()) . '</a>';
        } else {
            $aelement = '<a href="javascript:void(0);" onclick="viewgiftcode' . $row->getId() . '()">'
                . $this->getHelper()->getHiddenCode($row->getGiftCode()) . '</a>';
        }
        
        $html = '<span id="inputboxgiftvoucher' . $row->getId() . '" >' . $aelement . '</span>
            <script type="text/javascript">
                //<![CDATA[
                viewgiftcode' . $row->getId() . ' = function(){
                    document.getElementById(\'inputboxgiftvoucher' . $row->getId() . '\').innerHTML=\'' . $input . '\';
                    document.getElementById(\'input-gift-code' . $row->getId() . '\').focus();
                }
                hiddencode' . $row->getId() . ' = function(el) {
                    document.getElementById(\'inputboxgiftvoucher' . $row->getId() . '\').innerHTML=\'' . $aelement . '\';
                }
                //]]>
            </script>';
        return $html;
    }
}
