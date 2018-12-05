<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Framework\Stdlib\DateTime\Filter\Date as FilterDate;

/**
 * Class Save
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
 */
class Save extends \Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
{
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory
     */
    protected $templateCollectionFactory;
    
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
     * @var \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory $templateCollectionFactory
     * @param \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $repository
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
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory $templateCollectionFactory,
        \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $repository,
        DataPersistorInterface $dataPersistor,
        AuthSession $authSession,
        FilterDate $filterDate
    ) {
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
        $this->authSession = $authSession;
        $this->filterDate = $filterDate;
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry, $modelFactory, $collectionFactory);
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
            $data['gift_code'] = trim($data['gift_code']);
            $id = $this->getRequest()->getParam('giftvoucher_id');
            /** @var \Magestore\Giftvoucher\Model\Giftvoucher $model */
            $model = $this->modelFactory->create();

            // Prepare Data
            if (empty($data['expired_at'])) {
                $data['expired_at'] = null;
            } else {
                $data['expired_at'] = $this->filterDate->filter($data['expired_at']);
            }
            if (isset($data['order_increment_id'])) {
                unset($data['order_increment_id']);
            }
            $data['amount'] = $data['balance'];

            $validator = new \Zend_Validate_EmailAddress();
            if (isset($data['customer_email']) && $data['customer_email'] != '') {
                if (!$validator->isValid($data['customer_email'])) {
                    $this->messageManager->addError(__('Customer email is not valid'));
                }
            }

            if (isset($data['recipient_email']) && $data['recipient_email'] != '') {
                if (!$validator->isValid($data['recipient_email'])) {
                    $this->messageManager->addError(__('Recipient email is not valid'));
                }
            }
            if ($this->getRequest()->getParam('giftvoucher_id')) {
                $data['action'] = \Magestore\Giftvoucher\Model\Actions::ACTIONS_UPDATE;
                $data['extra_content'] = __('Updated by %1', $this->authSession->getUser()->getUsername());
            } else {
                $data['extra_content'] = __('Created by %1', $this->authSession->getUser()->getUsername());
            }

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            if (isset($data['rule']['actions'])) {
                $data['actions'] = $data['rule']['actions'];
            }
            unset($data['rule']);

            if (!$id && empty($data['giftcard_template_id'])) {
                /** @var \Magestore\Giftvoucher\Model\GiftTemplate $template */
                $template = $this->templateCollectionFactory->create()->getFirstItem();
                $templateImages = explode(',', $template->getImages());
                
                $data['giftcard_template_id'] = $template->getId();
                $data['giftcard_template_image'] = $templateImages[0];
            }

            $model->setData($data);
            $model->setIncludeHistory(true);

            // Save Data
            try {
                if ($this->getRequest()->getParam('back') && $this->getRequest()->getParam('sendemail')) {
                    $data['is_sent'] = 1;
                }
                $model->loadPost($data);
                if (!$id) {
                    $model->setData('giftvoucher_id', null);
                }
                $this->repository->save($model);
                $this->messageManager->addSuccess(__('Gift Code was successfully saved'));
                $this->dataPersistor->clear('giftcard_code');

                if ($this->getRequest()->getParam('back')) {
                    // Send email
                    if ($this->getRequest()->getParam('sendemail')) {
                        $emailSent = (int)$model->setNotResave(true)->sendEmail()->getEmailSent();
                        if ($emailSent) {
                            $this->messageManager->addSuccess(__('and %1 email(s) were sent.', $emailSent));
                        } else if (!$model->getRecipientEmail()) {
                            $this->messageManager->addError(__('There is no email address to send.'));
                        } else {
                            $statusLabel = $model->getStatusLabel();
                            $configUrl = $this->getUrl('adminhtml/system_config/edit', ['section' => 'giftvoucher']);
                            $this->messageManager->addError(
                                __('Gift card is %1 should not send an email, %2',
                                    $statusLabel,
                                    '<a href="' . $configUrl . '">'
                                        . __(' view config select status of gift card when sending e-mail to friend')
                                    . '</a>'
                                )
                            );
                        }
                    }
                    // Back to current page
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the gift code.'));
            }

            $this->dataPersistor->set('giftcard_code', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
