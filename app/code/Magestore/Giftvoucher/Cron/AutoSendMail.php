<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Cron;

/**
 * Class AutoSendMail
 * @package Magestore\Giftvoucher\Cron
 */
class AutoSendMail
{
    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $helperGiftvoucher;

    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherFactory
     */
    protected $giftvoucherFactory;

    /**
     * AutoSendMail constructor.
     * @param \Magestore\Giftvoucher\Helper\Data $helper
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     */
    public function __construct(
        \Magestore\Giftvoucher\Helper\Data $helper,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
    ) {
        $this->helperGiftvoucher = $helper;
        $this->giftvoucherFactory = $giftvoucherFactory;
    }

    /**
     *
     */
    public function execute()
    {
        if ($this->helperGiftvoucher->getEmailConfig('autosend')) {
            $giftVouchers = $this->giftvoucherFactory->create()->getCollection()
                ->addFieldToFilter('status', array('neq' => \Magestore\Giftvoucher\Model\Status::STATUS_DELETED))
                ->addExpireAfterDaysFilter($this->helperGiftvoucher->getEmailConfig('daybefore'));
            foreach ($giftVouchers as $giftVoucher) {
                $giftVoucher->sendEmail();
            }
        }
    }
}
