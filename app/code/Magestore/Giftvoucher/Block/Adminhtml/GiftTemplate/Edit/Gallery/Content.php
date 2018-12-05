<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\GiftTemplate\Edit\Gallery;

/**
 * Class Content
 * @package Magestore\Giftvoucher\Block\Adminhtml\GiftTemplate\Edit\Gallery
 */
class Content extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content
{
    
    /**
     * @var \Magestore\Giftvoucher\Service\GiftTemplate\MediaService
     */
    protected $mediaService;
    
    /**
     * @var \Magestore\Giftvoucher\Service\GiftTemplate\SampleDataService
     */
    protected $sampleDataService;
    
    /**
     * @var \Magestore\Giftvoucher\Service\GiftTemplate\ProcessorService
     */
    protected $templateProcessorService;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Service\GiftTemplate\MediaService $mediaService
     * @param \Magestore\Giftvoucher\Service\GiftTemplate\SampleDataService $sampledataService
     * @param \Magestore\Giftvoucher\Service\GiftTemplate\ProcessorService $templateProcessorService
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Service\GiftTemplate\MediaService $mediaService,
        \Magestore\Giftvoucher\Service\GiftTemplate\SampleDataService $sampledataService,
        \Magestore\Giftvoucher\Service\GiftTemplate\ProcessorService $templateProcessorService,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->mediaService = $mediaService;
        $this->sampleDataService = $sampledataService;
        $this->templateProcessorService = $templateProcessorService;
        parent::__construct($context, $jsonEncoder, $mediaConfig, $data);
    }
    
    /**
     * @return AbstractBlock
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('giftvoucheradmin/gifttemplate/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );
        
        $this->setTemplate('Magestore_Giftvoucher::gifttemplate/edit/gallery.phtml');
        return $this;
    }
    
    /**
     * Retrieve media attributes
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        return [];
    }
    
    /**
     * @return string
     */
    public function getImagesJson()
    {
        return $this->mediaService->getImagesJson($this->getElement()->getModelData());
    }
    
    /**
     *
     * @return \Magestore\Giftvoucher\Api\Data\GiftTemplateInterface
     */
    public function getGiftTemplate()
    {
        return $this->getElement()->getModelData();
    }
    
    /**
     *
     * @return string
     */
    public function getTemplatePreview()
    {
        $giftTemplateId = $this->getGiftTemplate()->getDesignPattern();
        $variables = $this->sampleDataService->getSampleData();
        $variables['giftImageUrl'] = '<%- data.url %>';
        $variables['textColor'] = '<%- data.textColor %>';
        $variables['styleColor'] = '<%- data.styleColor %>';
        $variables['notes'] = '<%- data.notes %>';
        try {
            return $this->templateProcessorService->getProcessedTemplate($variables, $giftTemplateId);
        } catch (\Exception $e) {
            return __("We're sorry, an error has occurred while generating this gift card template. ". $e->getMessage());
        }
    }
}
