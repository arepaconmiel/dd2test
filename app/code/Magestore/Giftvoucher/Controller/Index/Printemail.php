<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Controller\Index;

use Magestore\Giftvoucher\Controller\Action;

/**
 * Print from Email Action
 */
class Printemail extends Action
{
    /**
     * @var \Magestore\Giftvoucher\Model\Giftvoucher
     */
    protected $_giftVoucher;

    /**
     * Printemail constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $_giftVoucher
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Giftvoucher\Model\Giftvoucher $_giftVoucher
    ) {
        parent::__construct($context, $storeManager, $resultPageFactory, $priceCurrency);
        $this->_giftVoucher = $_giftVoucher;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|mixed
     */
    public function execute()
    {
        if ($key = $this->getRequest()->getParam('k')) {
            $keyDecode = explode('$', base64_decode($key));
            $giftvoucher = $this->_giftVoucher->load($keyDecode[1]);
            if ($giftvoucher && $giftvoucher->getId() && $giftvoucher->getGiftCode() == $keyDecode[0]) {
                return $this->getPageFactory();
            }
        } else {
            return $this->_redirect('*/*/index');
        }
    }
}
