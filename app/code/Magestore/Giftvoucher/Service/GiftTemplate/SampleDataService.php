<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Service\GiftTemplate;

use Magestore\Giftvoucher\Api\Data\GiftTemplateInterface;
use Magento\Framework\UrlInterface;

/**
 * Class SampleDataService
 * @package Magestore\Giftvoucher\Service\GiftTemplate
 */
class SampleDataService implements \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface
{
    /**
     * @var \Magento\Theme\Block\Html\Header\Logo
     */
    protected $logo;
    
    /**
     * @var \Magento\Framework\View\Element\Template
     */
    protected $view;
    
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;
    
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * SampleDataService constructor.
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     * @param \Magento\Framework\View\Element\Template $view
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Framework\View\Element\Template $view,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
    
        $this->logo = $logo;
        $this->view = $view;
        $this->priceHelper = $priceHelper;
        $this->timezone = $timezone;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }
    
    /**
     *
     * @return string
     */
    public function getLogo()
    {
        if ($printLogo = $this->helper->getPrintConfig('logo')) {
            return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
                    .'giftvoucher/pdf/logo/'
                    . $printLogo;
        }
        return $this->logo->getLogoSrc();
    }
    
    /**
     *
     * @return string
     */
    public function getMessageSample()
    {
        return __('Write message here...');
    }
    
    /**
     *
     * @return float
     */
    public function getGiftCardValueSample()
    {
        return $this->priceHelper->currency(100, true, false);
    }
    
    /**
     *
     * @return string
     */
    public function getBarcodeFileSample()
    {
        return $this->view->getViewFileUrl('Magestore_Giftvoucher::images/barcode/barcode.png');
    }
    
    /**
     *
     * @return string
     */
    public function getExpiredDataSample()
    {
        return $this->timezone->formatDate($this->timezone->date(), \IntlDateFormatter::MEDIUM);
    }
    
    /**
     *
     * @return string
     */
    public function getGiftCodeSample()
    {
        return 'GIFT-XXXX-XXXX';
    }
    
    /**
     *
     * @return string
     */
    public function getNotesSample()
    {
        return __('Converting to cash is not allowed. You can redeem this gift card when checkout at your online store.');
    }
    
    /**
     *
     * @return string
     */
    public function getTextColorSample()
    {
        return '6C6C6C';
    }

    /**
     *
     * @return string
     */
    public function getStyleColorSample()
    {
        return '6C6C6C';
    }
    
    /**
     *
     * @return array
     */
    public function getSampleData()
    {
        return [
            GiftTemplateInterface::LOGO_URL_PRINT => $this->getLogo(),
            GiftTemplateInterface::IMAGE_URL_PRINT => '',
            GiftTemplateInterface::MESSAGE_PRINT => $this->getMessageSample(),
            GiftTemplateInterface::VALUE_PRINT => $this->getGiftCardValueSample(),
            GiftTemplateInterface::GIFTCODE_PRINT => $this->getGiftCodeSample(),
            GiftTemplateInterface::BARCODE_URL_PRINT => $this->getBarcodeFileSample(),
            GiftTemplateInterface::EXPIRED_DATE_PRINT => $this->getExpiredDataSample(),
            GiftTemplateInterface::NOTES_PRINT => $this->getNotesSample(),
            GiftTemplateInterface::TEXT_COLOR_PRINT => $this->getTextColorSample(),
            GiftTemplateInterface::STYLE_COLOR_PRINT => $this->getStyleColorSample()
        ];
    }
}
