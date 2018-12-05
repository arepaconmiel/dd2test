<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Service\GiftTemplate;

use Magestore\Giftvoucher\Api\Data\GiftTemplateInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class ProcessorService
 * @package Magestore\Giftvoucher\Service\GiftTemplate
 */
class ProcessorService implements \Magestore\Giftvoucher\Api\GiftTemplate\ProcessorServiceInterface
{
    
    /**
     * @var \Magento\Email\Model\Template\FilterFactory
     */
    protected $filterFactory;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface
     */
    protected $ioService;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface
     */
    protected $sampleDataService;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\MediaServiceInterface
     */
    protected $mediaService;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\TransferDataServiceInterface
     */
    protected $transferDataService;

    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface
     */
    protected $giftTemplateRepository;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * ProcessorService constructor.
     * @param \Magento\Email\Model\Template\FilterFactory $filterFactor
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $ioService
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface $sampleDataService
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\MediaServiceInterface $mediaService
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\TransferDataServiceInterface $transferDataService
     * @param \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        \Magento\Email\Model\Template\FilterFactory $filterFactor,
        \Magestore\Giftvoucher\Api\GiftTemplate\IOServiceInterface $ioService,
        \Magestore\Giftvoucher\Api\GiftTemplate\SampleDataServiceInterface $sampleDataService,
        \Magestore\Giftvoucher\Api\GiftTemplate\MediaServiceInterface $mediaService,
        \Magestore\Giftvoucher\Api\GiftTemplate\TransferDataServiceInterface $transferDataService,
        \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
    
        $this->filterFactory = $filterFactor;
        $this->ioService = $ioService;
        $this->sampleDataService = $sampleDataService;
        $this->mediaService = $mediaService;
        $this->transferDataService = $transferDataService;
        $this->giftTemplateRepository = $giftTemplateRepository;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }
    

    /**
     * Process gift template to HTML
     *
     * @param array $variables
     * @param string $giftTemplateId
     * @return string
     */
    public function getProcessedTemplate(array $variables, $giftTemplateId)
    {
        $processor = $this->getTemplateFilter();
        $variables['this'] = $this;
        $processor->setVariables($variables);
        $processedResult = $processor->filter($this->getTemplateContent($giftTemplateId));
        return $processedResult;
    }

    /**
     * Get HTML preview of gift card or gift template
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher|null $giftCode
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $giftTemplate
     * @return string
     */
    public function preview($giftCode, $giftTemplate)
    {
        $variables = [];
        $storeId = 0;
        if (is_null($giftCode) || !$giftCode->getId()) {
            $variables = $this->sampleDataService->getSampleData();
            $variables[GiftTemplateInterface::IMAGE_URL_PRINT] = $this->mediaService->getFirstImageUrl($giftTemplate);
        } else {
            $variables = $this->transferDataService->toPrintData($giftCode);
            $storeId = $giftCode->getStoreId();
        }
        /* insert data of gift template */
        $variables[GiftTemplateInterface::TEXT_COLOR_PRINT] = $this->getTemplateTextColor($giftTemplate);
        $variables[GiftTemplateInterface::STYLE_COLOR_PRINT] = $this->getTemplateStyleColor($giftTemplate);
        $variables[GiftTemplateInterface::NOTES_PRINT] = $this->getProcessedNotes($giftTemplate->getNotes(), $storeId);
        
        return $this->getProcessedTemplate($variables, $giftTemplate->getDesignPattern());
    }
    
    /**
     * Print-out gift code to a gift card HTML
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftCode
     * @return string
     */
    public function printGiftCodeHtml($giftCode)
    {
        $giftTemplate = $this->giftTemplateRepository->getById($giftCode->getGiftcardTemplateId());
        return $this->preview($giftCode, $giftTemplate);
    }

    /**
     *
     * @param string $note
     * @param int $storeId
     * @return string
     */
    protected function getProcessedNotes($note, $storeId = 0)
    {
        $store = $this->storeManager->getStore($storeId);
        $storeName = $this->config->getValue('general/store_information/name', ScopeInterface::SCOPE_STORE, $store->getCode());
        $storeName = $storeName ? $storeName : $store->getName();
        $storeUrl = $store->getBaseUrl();
        $storeAddress = $this->config->getValue('general/store_information/street_line1', ScopeInterface::SCOPE_STORE, $store->getCode());
        $note = str_replace('{store_name}', $storeName, $note);
        $note = str_replace('{store_url}', $storeUrl, $note);
        $note = str_replace('{store_address}', $storeAddress, $note);
        return $note;
    }
    
    /**
     *
     * @param string $giftTemplateId
     * @return string
     */
    protected function getTemplateContent($giftTemplateId)
    {
        return $this->preProcessTemplate($this->ioService->getTemplateContent($giftTemplateId));
    }
    
    /**
     *
     * @param string $content
     * @return string
     */
    protected function preProcessTemplate($content)
    {
        /* @TODO: remove HTML comments */
        return $content;
    }
    
    /**
     * Get filter object for template processing
     *
     * @return \Magento\Email\Model\Template\Filter
     */
    protected function getTemplateFilter()
    {
        if (empty($this->templateFilter)) {
            $this->templateFilter = $this->filterFactory->create();
        }
        return $this->templateFilter;
    }
    
    /**
     * Get text color code of gifttemplate
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $giftTemplate
     * @return string
     */
    public function getTemplateTextColor($giftTemplate)
    {
        return str_replace('#', '', $giftTemplate->getTextColor());
    }
    
    /**
     * Get style color code of gifttemplate
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface $giftTemplate
     * @return string
     */
    public function getTemplateStyleColor($giftTemplate)
    {
        return str_replace('#', '', $giftTemplate->getStyleColor());
    }    
}
