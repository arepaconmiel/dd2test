<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Adminhtml\Checkout;

use \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface;

/**
 * Class AbstractAction
 * @package Magestore\Giftvoucher\Controller\Adminhtml\Checkout
 */
abstract class AbstractAction extends \Magestore\Giftvoucher\Controller\Adminhtml\AbstractAction
{
    /**
     * @var \Magestore\Giftvoucher\Service\Redeem\CheckoutService
     */
    protected $checkoutService;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Apply constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory, $coreRegistry);
        $this->checkoutService = $checkoutService;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Giftvoucher::giftcard');
    }

    /**
     * @param \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface $response
     */
    protected function _processResponseMessages($response)
    {
        if (!empty($response[ResponseInterface::NOTICES])) {
            foreach ($response[ResponseInterface::NOTICES] as $message) {
                $this->messageManager->addNotice($message);
            }
        }
        if (!empty($response[ResponseInterface::ERRORS])) {
            foreach ($response[ResponseInterface::ERRORS] as $message) {
                $this->messageManager->addError($message);
            }
        }
        if (!empty($response[ResponseInterface::SUCCESS])) {
            foreach ($response[ResponseInterface::SUCCESS] as $message) {
                $this->messageManager->addSuccess($message);
            }
        }
    }
}
