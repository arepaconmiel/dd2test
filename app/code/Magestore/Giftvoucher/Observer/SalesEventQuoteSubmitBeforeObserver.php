<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesEventQuoteSubmitBeforeObserver
 * @package Magestore\Giftvoucher\Observer
 */
class SalesEventQuoteSubmitBeforeObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\DataObject\Copy\Config
     */
    protected $fieldsetConfig;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $orderInterface;

    /**
     * @var \Magento\Sales\Api\Data\OrderItemInterface
     */
    protected $orderItemInterface;

    /**
     * SalesEventQuoteSubmitBeforeObserver constructor.
     * @param \Magento\Framework\DataObject\Copy\Config $fieldsetConfig
     * @param \Magento\Sales\Api\Data\OrderInterface $orderInterface
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItemInterface
     */
    public function __construct(
        \Magento\Framework\DataObject\Copy\Config $fieldsetConfig,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Sales\Api\Data\OrderItemInterface $orderItemInterface
    ) {
        $this->fieldsetConfig = $fieldsetConfig;
        $this->orderInterface = $orderInterface;
        $this->orderItemInterface = $orderItemInterface;
    }

    /**
     * Convert Quote To Order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $source = $observer->getEvent()->getQuote();
        $target = $observer->getEvent()->getOrder();
        $this->copyFieldsetToTarget('sales_convert_quote', 'to_order', 'global', $source, $target);
        $this->copyFieldsetItemsToTarget('quote_convert_item', 'to_order_item', 'global', $source, $target);
        return $this;
    }

    /**
     * @param $fieldset
     * @param $aspect
     * @param $root
     * @param $source
     * @param $target
     */
    public function copyFieldsetToTarget($fieldset, $aspect, $root, $source, $target)
    {

        $fields = $this->fieldsetConfig->getFieldset($fieldset, $root);
        $methods = get_class_methods($this->orderInterface);
        foreach ($fields as $code => $node) {
            if (isset($node[$aspect])) {
                $targetCode = (string)$node[$aspect];
                $targetCode = $targetCode == '*' ? $code : $targetCode;
                if (!in_array($this->getMethodName($targetCode), $methods)) {
                    $target->setData($targetCode, $source->getData($code));
                }
            }
        }
    }

    /**
     * @param $fieldset
     * @param $aspect
     * @param $root
     * @param $source
     * @param $target
     */
    public function copyFieldsetItemsToTarget($fieldset, $aspect, $root, $source, $target)
    {
        $fields = $this->fieldsetConfig->getFieldset($fieldset, $root);
        $methods = get_class_methods($this->orderItemInterface);
        foreach ($fields as $code => $node) {
            if (isset($node[$aspect])) {
                $targetCode = (string)$node[$aspect];
                $targetCode = $targetCode == '*' ? $code : $targetCode;
                if (!in_array($this->getMethodName($targetCode), $methods)) {
                    foreach ($source->getAllItems() as $quoteItem) {
                        $orderItem = $target->getItemByQuoteItemId($quoteItem->getId());
                        if ($orderItem) {
                            $orderItem->setData($targetCode, $quoteItem->getData($code));
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $key
     * @return string
     */
    public function getMethodName($key)
    {
        return 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
    }
}
