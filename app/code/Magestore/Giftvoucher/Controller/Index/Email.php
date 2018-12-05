<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Index;

/**
 * Email Action
 */
class Email extends \Magestore\Giftvoucher\Controller\Action
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
     * Email constructor.
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
     * @return \Magento\Framework\App\ResponseInterface|mixed
     */
    public function execute()
    {
        $linked = $this->model
            ->load($this->getRequest()->getParam('id'));
        if ($linked->getCustomerId() != $this->customerSession->getCustomerId()) {
            return $this->_redirect('*/*/index');
        }
        $resultPageFactory = $this->getPageFactory();
        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
        $navigationBlock = $resultPageFactory->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('giftvoucher');
        }
        return $resultPageFactory;
    }
}
