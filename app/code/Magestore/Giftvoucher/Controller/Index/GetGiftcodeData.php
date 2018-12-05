<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Controller\Index;

/**
 * Giftvoucher Index Check Action
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class GetGiftcodeData extends \Magestore\Giftvoucher\Controller\Checkout\AbstractAction
{

    /**
     * @var \Magestore\Giftvoucher\Block\Redeem\Form
     */
    protected $checkingService;

    /**
     * @var \Magestore\Giftvoucher\Block\Check
     */
    protected $checkingForm;

    /**
     * GetGiftcodeData constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magestore\Giftvoucher\Block\Redeem\Form $redeemForm
     * @param \Magestore\Giftvoucher\Service\GiftCode\CheckingService $checkingService
     * @param \Magestore\Giftvoucher\Block\Check $checkingForm
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magestore\Giftvoucher\Service\Redeem\CheckoutService $checkoutService,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magestore\Giftvoucher\Block\Redeem\Form $redeemForm,
        \Magestore\Giftvoucher\Service\GiftCode\CheckingService $checkingService,
        \Magestore\Giftvoucher\Block\Check $checkingForm
    ) {
        parent::__construct($context, $checkoutService, $resultJsonFactory, $redeemForm);
        $this->checkingService = $checkingService;
        $this->checkingForm = $checkingForm;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $code = $this->getBodyParams('code');
        $result = $this->checkingService->check($code);
        $this->_processResponseMessages($result);
        $response = $this->checkingForm->getFormData(false);
        $response['data'] = $this->checkingService->getCodeData($code, true);
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($response);
        return $resultJson;
    }
}
