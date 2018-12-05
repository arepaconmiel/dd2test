<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block;

/**
 * Class Check
 * @package Magestore\Giftvoucher\Block
 */
class Check extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magestore\Giftvoucher\Service\GiftCode\CheckingService
     */
    protected $checkingService;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helper;

    /**
     * Check constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Giftvoucher\Service\GiftCode\CheckingService $checkingService
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Giftvoucher\Service\GiftCode\CheckingService $checkingService,
        \Magestore\Giftvoucher\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->checkingService = $checkingService;
        $this->helper = $helper;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_request->getParam('code', null);
    }

    /**
     * @param bool $isJson
     * @param string $key
     * @return array|mixed|string
     */
    public function getFormData($isJson = true, $key = '')
    {
        $data = [];
        $data['check_url'] = $this->getUrl('giftvoucher/index/getGiftcodeData');
        $data['can_check'] = $this->helper->isAvailableToCheckCode();
        $data['data'] = $this->checkingService->getCodeData($this->getCode(), true);
        if ($key) {
            $data = (isset($data[$key]))?$data[$key]:'';
        }
        return ($isJson)?\Zend_Json::encode($data):$data;
    }
}
