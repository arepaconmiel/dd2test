<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Giftvoucher;

/**
 * Adminhtml Giftvoucher Print View Block
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magestore\Giftvoucher\Model\Giftvoucher
     */
    protected $giftvoucher;

    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\Collection
     */
    protected $collection;
    
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftTemplate\ProcessorServiceInterface
     */
    protected $processor;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\Collection $collection
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magestore\Giftvoucher\Api\GiftTemplate\ProcessorServiceInterface $processor
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher,
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\Collection $collection,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magestore\Giftvoucher\Api\GiftTemplate\ProcessorServiceInterface $processor,
        array $data = []
    ) {
            $this->giftvoucher = $giftvoucher;
            $this->collection = $collection;
            $this->filter = $filter;
            $this->processor = $processor;
            parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getGiftVoucher()
    {
        if (!$this->hasData('gift_voucher')) {
            $this->setData(
                'gift_voucher',
                $this->giftvoucher->load($this->getRequest()->getParam('id'))
            );
        }
        return $this->getData('gift_voucher');
    }

    /**
     * Get list for gift codes for print
     *
     * @return \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\Collection
     */
    public function getGiftVouchers()
    {
        if (!$this->hasData('gift_vouchers')) {
            if ($giftvoucherIds = $this->getRequest()->getParam('ids')) {
                // Old style print & use for import print
                if (!is_array($giftvoucherIds)) {
                    $giftvoucherIds = explode(',', $giftvoucherIds);
                }
                $this->collection->addFieldToFilter('giftvoucher_id', ['in' => $giftvoucherIds]);
            } else {
                $this->filter->getCollection($this->collection);
            }
            $this->setData('gift_vouchers', $this->collection);
        }
        return $this->getData('gift_vouchers');
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
