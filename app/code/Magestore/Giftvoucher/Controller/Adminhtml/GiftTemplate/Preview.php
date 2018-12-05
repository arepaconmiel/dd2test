<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

/**
 * Class Preview
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate
 */
class Preview extends GiftTemplate
{
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\ProcessorServiceInterface
     */
    protected $giftTemplateProcessorService;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory
     * @param \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\ProcessorServiceInterface $giftTemplateProcessorService
     * @internal param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory,
        \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository,
        \Magestore\Giftvoucher\Api\GiftTemplate\ProcessorServiceInterface $giftTemplateProcessorService
    ) {
        $this->giftTemplateProcessorService = $giftTemplateProcessorService;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry, $giftTemplateFactory, $giftTemplateRepository);
    }

    
    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $giftTemplate = $this->giftTemplateFactory->create();

        if ($id) {
            $giftTemplate = $this->giftTemplateRepository->getById($id);
            if (!$giftTemplate->getId()) {
                $result = __('This template no longer exists.');
            } else {
                $result = $this->giftTemplateProcessorService->preview(null, $giftTemplate);
            }
        } else {
            $result = __('This template no longer exists.');
        }
        $response = $this->_objectManager->get('Magento\Framework\Controller\Result\RawFactory')->create();
       // $response->setHeader('Content-type', 'text/plain');
        $response->setContents(($result));
        return $response;
    }
}
