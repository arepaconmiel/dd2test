<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Service\GiftTemplate;

use Magestore\Giftvoucher\Api\Data\GiftTemplateInterface;

/**
 * Class TransferDataService
 * @package Magestore\Giftvoucher\Service\GiftTemplate
 */
class TransferDataService implements \Magestore\Giftvoucher\Api\GiftTemplate\TransferDataServiceInterface
{
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface
     */
    protected $sampleDataService;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\MediaServiceInterface
     */
    protected $mediaService;
    
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magestore\Giftvoucher\Helper\Barcode
     */
    protected $barcodeHelper;

    /**
     * TransferDataService constructor.
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface $sampleDataService
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\MediaServiceInterface $mediaService
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magestore\Giftvoucher\Helper\Barcode $barcodeHelper
     */
    public function __construct(
        \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface $sampleDataService,
        \Magestore\Giftvoucher\Api\GiftTemplate\MediaServiceInterface $mediaService,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magestore\Giftvoucher\Helper\Barcode $barcodeHelper
    ) {
    
        $this->sampleDataService = $sampleDataService;
        $this->mediaService = $mediaService;
        $this->timezone = $timezone;
        $this->priceHelper = $priceHelper;
        $this->barcodeHelper = $barcodeHelper;
    }
    
    /**
     * Get print data
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftcode
     * @return array
     */
    public function toPrintData($giftcode)
    {
        return [
            GiftTemplateInterface::LOGO_URL_PRINT => $this->sampleDataService->getLogo(),
            GiftTemplateInterface::IMAGE_URL_PRINT => $this->mediaService->getImageUrl($giftcode->getGiftcardTemplateImage()),
            GiftTemplateInterface::MESSAGE_PRINT => $giftcode->getMessage(),
            GiftTemplateInterface::VALUE_PRINT => $this->priceHelper->currency($giftcode->getBalance(), true, false),
            GiftTemplateInterface::GIFTCODE_PRINT => $giftcode->getGiftCode(),
            GiftTemplateInterface::BARCODE_URL_PRINT => $this->barcodeHelper->getBarcodeImageSource($giftcode->getGiftCode()),
            GiftTemplateInterface::EXPIRED_DATE_PRINT => $giftcode->getExpiredAt()
                                        ? $this->timezone->formatDate($giftcode->getExpiredAt(), \IntlDateFormatter::MEDIUM)
                                        : null,
            GiftTemplateInterface::SENDER_NAME_PRINT => $giftcode->getCustomerName(),
            GiftTemplateInterface::RECIPIENT_NAME_PRINT => $giftcode->getRecipientName(),
        ];
    }
}
