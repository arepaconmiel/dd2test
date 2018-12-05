<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;

use Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftTemplate
 */
class Save extends GiftTemplate
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    
    /**
     * @var \Magestore\Giftvoucher\Service\GiftTemplate\MediaService
     */
    protected $mediaService;

    /**
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory
     * @param \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository
     * @param DataPersistorInterface $dataPersistor
     * @param \Magestore\Giftvoucher\Service\GiftTemplate\MediaService $mediaService
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftTemplateFactory $giftTemplateFactory,
        \Magestore\Giftvoucher\Api\GiftTemplateRepositoryInterface $giftTemplateRepository,
        DataPersistorInterface $dataPersistor,
        \Magestore\Giftvoucher\Service\GiftTemplate\MediaService $mediaService
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->mediaService = $mediaService;
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
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getParams();
        if ($data) {
            $id = $this->getRequest()->getParam('id');

            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Block::STATUS_ENABLED;
            }
            if (empty($data['giftcard_template_id'])) {
                $data['giftcard_template_id'] = null;
            }

            /** @var \Magestore\Giftvoucher\Model\GiftTemplate $model */
            $model = $this->giftTemplateFactory->create();
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This gift card template no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($data);

            try {
                /* process upload & update images */
                $this->mediaService->updateMedia($model);
                $this->giftTemplateRepository->save($model);
                
                $this->messageManager->addSuccess(__('You saved the gift card template.'));
                $this->dataPersistor->clear('giftcard_template');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the gift card template.'));
            }

            $this->dataPersistor->set('giftcard_template', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
