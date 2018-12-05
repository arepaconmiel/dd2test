<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Checkout;

use \Magestore\Giftvoucher\Api\Data\Redeem\ResponseInterface;

/**
 * Class AbstractAction
 * @package Magestore\Giftvoucher\Controller\Checkout
 */
abstract class AbstractAction extends \Magento\Framework\App\Action\Action
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
     * @var \Magestore\Giftvoucher\Block\Redeem\Form
     */
    protected $redeemForm;

    /**
     * AbstractAction constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magestore\Giftvoucher\Block\Redeem\Form $redeemForm
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magestore\Giftvoucher\Block\Redeem\Form $redeemForm
    ) {
        parent::__construct($context);
        $this->checkoutService = $checkoutService;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->redeemForm = $redeemForm;
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

    /**
     * @return array|mixed|string
     */
    protected function _getRedeemFormData()
    {
        return $this->redeemForm->getFormData(false);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getBodyParams($key = '')
    {
        $request = $this->getRequest();
        $content = $request->getContent();
        $content = \Zend_Json::decode($content);
        return ($key)?(isset($content[$key])?$content[$key]:''):$content;
    }
}
