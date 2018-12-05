<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Index;

use Magestore\Giftvoucher\Controller\Action;

/**
 * Add Action
 */
class Addlist extends Action
{
    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherFactory
     */
    protected $giftvoucherFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\Session
     */
    protected $session;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\CustomerVoucherFactory
     */
    protected $modelFactory;

    /**
     * Addlist constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param \Magestore\Giftvoucher\Model\Session $session
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $collectionFactory
     * @param \Magestore\Giftvoucher\Model\CustomerVoucherFactory $modelFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magestore\Giftvoucher\Model\Session $session,
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $collectionFactory,
        \Magestore\Giftvoucher\Model\CustomerVoucherFactory $modelFactory
    ) {
        parent::__construct($context, $storeManager, $resultPageFactory, $priceCurrency);
        $this->giftvoucherFactory = $giftvoucherFactory;
        $this->session = $session;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        $this->modelFactory = $modelFactory;
    }

    /**
     *
     */
    public function execute()
    {
        if (!$this->customerLoggedIn()) {
            $this->_redirect("customer/account/login");
            return;
        }
        $max = $this->getHelper()->getGeneralConfig('maximum');
        $nowTime = date(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        if ($code = $this->getRequest()->getParam('giftvouchercode')) {
            $giftVoucher = $this->giftvoucherFactory->create()->loadByCode($code);
            $codes = $this->session->getCodes();
            if (!$this->helper->isAvailableToAddCode()) {
                $this->messageManager->addError(__('The maximum number of times to enter gift codes is %1!', $max));
                $this->_redirect("giftvoucher/index/index");
                return;
            }
            if (!$giftVoucher->getId()) {
                $codes[] = $code;
                $codes = array_unique($codes);
                $this->session->setCodes($codes);
                $errorMessage = __('Gift code "%1" is invalid.', $code);
                if ($max) {
                    $errorMessage .= ' ';
                    $errorMessage .= __('You have %1 time(s) remaining to re-enter Gift Card code.', $max - count($codes));
                }
                $this->messageManager->addError($errorMessage);
                $this->_redirect("*/*/add");
                return;
            } else {
                if (!$this->helper->canUseCode($giftVoucher)) {
                    $this->messageManager->addError(__('The gift code usage has exceeded the number of users allowed.'));
                    return $this->_redirect("giftvoucher/index/index");
                }

                $customer = $this->customerSession->getCustomer();
                $collection = $this->collectionFactory->create();
                $collection->addFieldToFilter('customer_id', $customer->getId())
                        ->addFieldToFilter('voucher_id', $giftVoucher->getId());
                if ($collection->getSize()) {
                    $this->messageManager->addError(__('This gift code has already existed in your list.'));
                    $this->_redirect("giftvoucher/index/add");
                    return;
                } elseif (($giftVoucher->getStatus() != 1 && $giftVoucher->getStatus() != 2
                    && $giftVoucher->getStatus() != 4) || !$giftVoucher->isValidWebsite()) {
                    $this->messageManager->addError(__('Gift code "%1" is not avaliable', $code));
                    $this->_redirect("giftvoucher/index/add");
                    return;
                } else {
                    $model = $this->modelFactory->create()
                            ->setCustomerId($customer->getId())
                            ->setVoucherId($giftVoucher->getId())
                            ->setAddedDate($nowTime);
                    try {
                        $model->save();
                        $this->messageManager->addSuccess(__('The gift code has been added to your list successfully.'));
                        $this->_redirect("giftvoucher/index/index");
                        return;
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        $this->_redirect("giftvoucher/index/add");
                        return;
                    }
                }
            }
        }

        $this->_redirect("giftvoucher/index/index");
    }
}
