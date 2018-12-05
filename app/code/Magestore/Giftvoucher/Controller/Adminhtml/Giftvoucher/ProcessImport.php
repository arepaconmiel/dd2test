<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher;

/**
 * Adminhtml Giftvoucher ProcessImport Action
 */
class ProcessImport extends \Magestore\Giftvoucher\Controller\Adminhtml\Giftvoucher
{
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory
     */
    protected $templateCollectionFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
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
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory $templateCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $filterDate
     * @param \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $repository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $modelFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $collectionFactory,
        \Magento\Framework\File\Csv $csv,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\CollectionFactory $templateCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $filterDate,
        \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $repository
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultForwardFactory,
            $coreRegistry,
            $modelFactory,
            $collectionFactory
        );
        $this->csv = $csv;
        $this->authSession = $authSession;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->storeManager = $storeManager;
        $this->filterDate = $filterDate;
        $this->repository = $repository;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $file = $this->getRequest()->getFiles('filecsv');
        if ($file) {
            if (substr($file["name"], -4)!='.csv') {
                $this->messageManager->addError(__('Please choose a CSV file'));
                return $resultRedirect->setPath('*/*/import');
            }
            try {
                $fileName = $file['tmp_name'];
                $data= $this->csv->getData($fileName);
                $count = array();
                $fields = array();
                $giftVoucherImport = array();
                foreach ($data as $row => $cols) {
                    if ($row == 0) {
                        $fields = $cols;
                    } else {
                        $giftVoucherImport[] = array_combine($fields, $cols);
                    }
                }
                
                $statuses = array(
                    '1' => 1, 'pending' => 1,
                    '2' => 2, 'active' => 2,
                    '3' => 3, 'disabled' => 3,
                    '4' => 4, 'used' => 4,
                    '5' => 5, 'expired' => 5,
                );
                $extraContent = __('Imported by %1', $this->authSession->getUser()->getUsername());
                $template = $this->templateCollectionFactory->create()->getFirstItem();
                
                foreach ($giftVoucherImport as $giftVoucherData) {
                    $giftVoucher = $this->modelFactory->create();
                    if (!empty($giftVoucherData['gift_code'])) {
                        $giftVoucher->loadByCode($giftVoucherData['gift_code']);
                        if ($giftVoucher->getId()) {
                            $this->messageManager->addError(
                                __('Gift code %1 already existed', $giftVoucher->getGiftCode())
                                );
                            continue;
                        } else {
                            //Mage::helper('giftvoucher')->createBarcode($giftVoucherData['gift_code']);
                        }
                    }
                    // Prepare Expired At
                    if (empty($giftVoucherData['expired_at'])) {
                        $giftVoucherData['expired_at'] = null;
                    } else {
                        $giftVoucherData['expired_at'] = $this->filterDate->filter($giftVoucherData['expired_at']);
                    }
                    // Prepare Status
                    if (!empty($giftVoucherData['status'])) {
                        $giftVoucherData['status'] = $statuses[$giftVoucherData['status']];
                    }
                    // Prepare Amount
                    if (!empty($giftVoucherData['history_amount'])) {
                        $giftVoucherData['amount'] = $giftVoucherData['history_amount'];
                    } elseif (!empty($giftVoucherData['balance'])) {
                        $giftVoucherData['amount'] = $giftVoucherData['balance'];
                    }
                    // Prepare Extra Content
                    if (!empty($giftVoucherData['extra_content'])) {
                        $giftVoucherData['extra_content'] = str_replace(
                            '\n',
                            chr(10),
                            $giftVoucherData['extra_content']
                        );
                    } else {
                        $giftVoucherData['extra_content'] = $extraContent;
                    }
                    // Prepare Address
                    $giftVoucherData['recipient_address'] = str_replace(
                        '\n',
                        chr(10),
                        $giftVoucherData['recipient_address']
                    );
                    // Prepare Message
                    $giftVoucherData['message'] = str_replace('\n', chr(10), $giftVoucherData['message']);
                    // Prepare Currency
                    if (!isset($giftVoucherData['currency'])) {
                        $giftVoucherData['currency'] = $this->storeManager->getStore($giftVoucherData['store_id'])
                            ->getBaseCurrencyCode();
                    }
                    // Prepare Template
                    if (!isset($giftVoucherData['giftcard_template_id'])) {
                        $images = explode(',', $template->getImages());
                        $giftVoucherData['giftcard_template_image'] = $images[0];
                        $giftVoucherData['giftcard_template_id'] = $template->getId();
                    }
                    // Save Gift Code
                    try {
                        $giftVoucher->setData($giftVoucherData)
                            ->setIncludeHistory(true)
                            ->setId(null);
                        $this->repository->save($giftVoucher);
                        $count[] = $giftVoucher->getId();
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
                
                if (count($count)) {
                    $successMessage = __('Imported total %1 Gift Code(s)', count($count));
                    if ($this->getRequest()->getParam('print')) {
                        $url = $this->getUrl('*/*/massPrint', array(
                            'ids' => implode(',', $count)
                        ));
                        $successMessage .= "<script type='text/javascript'>window.onload = function(){"
                            . "var bob=window.open('" . $url . "','_blank');"
                            . "};</script>";
                    }
                    $this->messageManager->addSuccess($successMessage);
                    return $resultRedirect->setPath('*/*/index');
                } else {
                    $this->messageManager->addError(__('No gift code imported'));
                    return $resultRedirect->setPath('*/*/import');
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Please check your import file content again.'));
                return $resultRedirect->setPath('*/*/import');
            }
        }
    }
}
