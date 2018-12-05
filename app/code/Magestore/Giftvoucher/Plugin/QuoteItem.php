<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Plugin;

/**
 * Class QuoteItem
 * @package Magestore\Giftvoucher\Plugin
 */
class QuoteItem
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional
    ) {

        if ($item->getProduct()->getTypeId() == \Magestore\Giftvoucher\Model\Product\Type\Giftvoucher::GIFT_CARD_TYPE) {
            $item->setOriginalPrice($item->getPrice());
            $item->setBaseOriginalPrice($item->getBasePrice());
        }



        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);
        /** @var $quoteItem \Magento\Quote\Model\Quote\Item */
        $quoteItem = $item;

        $keys = [
            'amount',
            'customer_name',
            'recipient_name',
            'recipient_email',
            'message',
            'day_to_send',
            'timezone_to_send',
            'recipient_address',
            'notify_success',
            'giftcard_template_image',
            'giftcard_use_custom_image'
        ];
        $productOptions = $orderItem->getProductOptions();
        foreach ($keys as $key) {
            $option = $quoteItem->getProduct()->getCustomOption($key);
            if ($option) {
                $productOptions[$key] = $option->getValue();
            }
        }

        $orderItem->setProductOptions($productOptions);

        return $orderItem;
    }
}
