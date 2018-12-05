<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Index;

/**
 * Remove Action
 */
class Remove extends \Magestore\Giftvoucher\Controller\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @var \Magestore\Giftvoucher\Model\CustomerVoucher
     */
    protected $model;

    /**
     * Remove constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magestore\Giftvoucher\Model\CustomerVoucher $model
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Model\Session $customerSession,
        \Magestore\Giftvoucher\Model\CustomerVoucher $model
    ) {
        parent::__construct($context, $storeManager, $resultPageFactory, $priceCurrency);
        $this->customerSession = $customerSession;
        $this->model = $model;
    }

    /**
     *
     */
    public function execute()
    {
        if (!$this->getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
            $this->_redirect("customer/account/login");
            return;
        }
        $customerVoucherId = $this->getRequest()->getParam('id');
        $voucher = $this->model->load($customerVoucherId);
        if ($voucher->getCustomerId() == $this->customerSession->getCustomer()->getId()) {
            try {
                $voucher->delete();
                $this->messageManager->addSuccess(__('Gift card was successfully removed'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect("giftvoucher/index/index");
    }
}
