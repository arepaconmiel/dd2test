<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Index;

/**
 * Send Email Action
 */
class SendEmail extends \Magestore\Giftvoucher\Controller\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magestore\Giftvoucher\Model\Giftvoucher
     */
    protected $model;

    /**
     * SendEmail constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $model
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Customer\Model\Session $customerSession,
        \Magestore\Giftvoucher\Model\Giftvoucher $model
    ) {
        parent::__construct($context, $storeManager, $resultPageFactory, $priceCurrency);
        $this->customerSession = $customerSession;
        $this->model = $model;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id = $data['giftcard_id'];
            $giftCard = $this->model->load($id);

            $customer = $this->customerSession->getCustomer();
            //if (!$customer || ($giftCard->getCustomerId() != $customer->getId() && $giftCard->getCustomerEmail() != $customer->getEmail())
            if (!$customer || ($giftCard->getCustomerEmail() != $customer->getEmail())
            ) {
                $this->messageManager->addError(__('The Gift Card email has been failed to send.'));
                return $this->_redirect('*/*/');
            }

            $giftCard->setNotResave(true);
            foreach ($data as $key => $value) {
                if ($value) {
                    $giftCard->setData($key, $value);
                }
            }

            try {
                $giftCard->save();
            } catch (\Exception $ex) {
                $this->messageManager->addError($ex->getMessage());
            }
            if ($giftCard->sendEmailToRecipient()) {
                $this->messageManager->addSuccess(__('The Gift Card email has been sent successfully.'));
            } else {
                $this->messageManager->addError(__('The Gift Card email cannot be sent to your friend!'));
            }
            //$translate->setTranslateInline(true);
        }
        $this->_redirect('*/*/');
    }
}
