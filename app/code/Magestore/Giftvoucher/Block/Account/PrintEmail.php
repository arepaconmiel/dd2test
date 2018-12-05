<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Account;

use Magestore\Giftvoucher\Block\Account;

/**
 * Print Gift Code block
 */
class PrintEmail extends Account
{
    /**
     * @var \Magestore\Giftvoucher\Model\Giftvoucher
     */
    protected $_giftVoucher;
    
    /**
     * @var \Magestore\Giftvoucher\Service\GiftTemplate\ProcessorService
     */
    protected $processor;

    /**
     * PrintEmail constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $accountManagement
     * @param \Magento\Customer\Helper\View $viewHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Magento\Framework\Url\DecoderInterface $decode
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $collectionFactory
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $_giftVoucher
     * @param \Magestore\Giftvoucher\Service\GiftTemplate\ProcessorService $processor
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $accountManagement,
        \Magento\Customer\Helper\View $viewHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        \Magento\Framework\Url\DecoderInterface $decode,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\CollectionFactory $collectionFactory,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory,
        \Magestore\Giftvoucher\Model\Giftvoucher $_giftVoucher,
        \Magestore\Giftvoucher\Service\GiftTemplate\ProcessorService $processor,
        array $data = []
    ) {
        parent::__construct($context, $accountManagement, $viewHelper, $httpContext, $currentCustomer, $objectManager, $datetime, $decode, $imageFactory, $priceCurrency, $helper, $collectionFactory, $giftvoucherFactory, $data);
        $this->_giftVoucher = $_giftVoucher;
        $this->processor = $processor;
    }

    /**
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     */
    public function getGiftVoucher()
    {
        return $this->_giftVoucher;
    }
    
    /**
     * Print a giftcode to HTML
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftCode
     * @return string
     */
    public function printGiftcodeHtml($giftCode)
    {
        return $this->processor->printGiftCodeHtml($giftCode);
    }
}
