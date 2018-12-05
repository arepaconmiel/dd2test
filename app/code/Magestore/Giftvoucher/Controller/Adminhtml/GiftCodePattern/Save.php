<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Stdlib\DateTime\Filter\Date as FilterDate;
use Magestore\Giftvoucher\Model\Status;

/**
 * Class Save
 * @package Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern
 */
class Save extends \Magestore\Giftvoucher\Controller\Adminhtml\GiftCodePattern
{
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory
     */
    protected $templateCollectionFactory;
    
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $dataHelper;
    
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var AuthSession
     */
    protected $authSession;

    /**
     * @var FilterDate
     */
    protected $filterDate;
    
    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherFactory
     */
    protected $giftvoucherFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Api\GiftCodePatternRepositoryInterface $repository
     * @param \Magestore\Giftvoucher\Model\GiftCodePatternFactory $modelFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\CollectionFactory $collectionFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory $templateCollectionFactory
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param \Magestore\Giftvoucher\Helper\Data $dataHelper
     * @param DataPersistorInterface $dataPersistor
     * @param AuthSession $authSession
     * @param FilterDate $filterDate
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Api\GiftCodePatternRepositoryInterface $repository,
        \Magestore\Giftvoucher\Model\GiftCodePatternFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\GiftCodePattern\CollectionFactory $collectionFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory $templateCollectionFactory,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magestore\Giftvoucher\Helper\Data $dataHelper,
        DataPersistorInterface $dataPersistor,
        AuthSession $authSession,
        FilterDate $filterDate
    ) {
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->giftvoucherFactory = $giftvoucherFactory;
        $this->dataHelper = $dataHelper;
        $this->dataPersistor = $dataPersistor;
        $this->authSession = $authSession;
        $this->filterDate = $filterDate;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry, $repository, $modelFactory, $collectionFactory);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('template_id');
            /** @var \Magestore\Giftvoucher\Model\GiftCodePattern $model */
            $model = $this->modelFactory->create();
            
            if ($id && 'duplicate' === $this->getRequest()->getParam('additional_action')) {
                try {
                    if ($newModel = $this->repository->getById($id)->duplicate()) {
                        $this->messageManager->addSuccess(__('The pattern has been duplicated successfully.'));
                        return $resultRedirect->setPath('*/*/edit', ['id' => $newModel->getId()]);
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
            
            // Check Pattern
            if (!$this->dataHelper->isExpression($data['pattern'])) {
                $this->messageManager->addError(__('Invalid pattern'));
                $this->dataPersistor->set('gift_code_pattern', $data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }

            // Prepare Expired At
            if (empty($data['expired_at'])) {
                if ($expiredConfig = $this->dataHelper->getGeneralConfig('expire')) {
                    $data['expired_at'] = date('Y-m-d', strtotime(
                        '+' . $expiredConfig . ' days'
                    ));
                } else {
                    $data['expired_at'] = null;
                }
            } else {
                $data['expired_at'] = $this->filterDate->filter($data['expired_at']);
            }
            
            // Rule Data
            $conditions = '';
            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
                $conditions = $data['conditions'];
            }
            unset($data['rule']);

            // Template
            if (!$id && empty($data['giftcard_template_id'])) {
                /** @var \Magestore\Giftvoucher\Model\GiftTemplate $template */
                $template = $this->templateCollectionFactory->create()->getFirstItem();
                $templateImages = explode(',', $template->getImages());
                
                $data['giftcard_template_id'] = $template->getId();
                $data['giftcard_template_image'] = $templateImages[0];
            }

            // Save Data
            try {
                $model->setData($data);
                $model->loadPost($data);
                if (!$id) {
                    $model->setData('template_id', null);
                }
                if ('generate' === $this->getRequest()->getParam('additional_action')) {
                    if ($id && $this->modelFactory->create()->load($id)->getIsGenerated()) {
                        $this->messageManager->addError(__('Each template only generate one time'));
                        return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                    }
                    $model->setIsGenerated(1);
                }
                // Save to database
                $this->repository->save($model);
                // Generate Gift Codes
                if ('generate' === $this->getRequest()->getParam('additional_action')) {
                    $data = $model->getData();
                    $data['conditions'] = $conditions;
                    $data['gift_code'] = $model->getData('pattern');
                    $data['amount'] = $model->getData('balance');
                    $data['status'] = Status::STATUS_ACTIVE;
                    $data['extra_content'] = __('Created by %1', $this->authSession->getUser()->getUsername());
                    for ($i = $model->getAmount(); $i > 0; $i--) {
                        /** @var \Magestore\Giftvoucher\Model\Giftvoucher $giftcode */
                        $giftcode = $this->giftvoucherFactory->create();
                        $giftcode->setData($data)->loadPost($data)
                            ->setIncludeHistory(true)
                            ->setGenerateGiftcode(true)
                            ->save();
                    }
                    $this->messageManager->addSuccess(__('The pattern has been generated successfully.'));
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                $this->messageManager->addSuccess(__('The pattern has been saved successfully.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
            // Go back to pattern form
            $this->dataPersistor->set('gift_code_pattern', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
