<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Service\Sales;

/**
 * Class AbstractOrderService
 * @package Magestore\Giftvoucher\Service\Sales
 */
class AbstractOrderService
{
    
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface
     */
    protected $giftCodeManagementService;
    
    /**
     * @var \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface
     */
    protected $giftCodeRepository;
    
    /**
     * @var string
     */
    protected $process = 'order_process';


    /**
     * AbstractOrderService constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface $giftCodeManagementService
     * @param \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $giftCodeRepository
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface $giftCodeManagementService,
        \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $giftCodeRepository
    ) {
    
        $this->registry = $registry;
        $this->giftCodeManagementService = $giftCodeManagementService;
        $this->giftCodeRepository = $giftCodeRepository;
    }

    /**
     * Mark item processed
     *
     * @param \Magento\Sales\Model\AbstractModel $item
     */
    public function markProcessed($item)
    {
        $key = $this->process . $item->getId();
        if (!$this->registry->registry($key)) {
            $this->registry->register($key, true);
        }
    }
    
    /**
     * Check item processed or not
     *
     * @param \Magento\Sales\Model\AbstractModel $item
     * @return boolean
     */
    public function isProcessed($item)
    {
        $key = $this->process . $item->getId();
        if ($this->registry->registry($key)) {
            return true;
        }
        return false;
    }
    
    /**
     * Check item is processable or not
     *
     * @param \Magento\Sales\Model\AbstractModel $item
     * @return boolean
     */
    public function canProcess($item)
    {
        return !$this->isProcessed($item);
    }
}
