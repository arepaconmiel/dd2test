<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    protected $orderFactory;

    /**
     * @var []
     */
    protected $_calculators;

    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    protected $_calculatorFactory;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * UpgradeData constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Math\CalculatorFactory $_calculatorFactory
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Math\CalculatorFactory $_calculatorFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\State $appState
    )
    {
        $this->orderFactory = $orderFactory;
        $this->_calculatorFactory = $_calculatorFactory;
        $this->productMetadata = $productMetadata;
        $this->_appState = $appState;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.2.0', '<')) {
            $version = $this->productMetadata->getVersion();
            try {
                if (version_compare($version, '2.2.0', '>=')) {
                    $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
                } else {
                    $this->_appState->setAreaCode('admin');
                }
            } catch (\Exception $e) {
                $this->_appState->getAreaCode();
            }
            $this->convertOrder($setup);
        }
    }

    protected function convertOrder(ModuleDataSetupInterface $setup)
    {
        $orderTable = $setup->getTable('sales_order');
        $select = $setup->getConnection()->select();
        $select->from(['main_table' => $orderTable], ['entity_id'])
            ->where('base_gift_voucher_discount > ?', 0);
        $data = $setup->getConnection()->fetchAll($select);
        foreach ($data as $item) {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderFactory->create()->load($item['entity_id']);
            $orderItems = $order->getAllItems();
            $store = $order->getStore();
            $totalItemsBaseGiftVoucherDiscountInvoiced = $totalItemsGiftVoucherDiscountInvoiced
                = $totalItemsBaseGiftVoucherDiscountRefunded = $totalItemsGiftVoucherDiscountRefunded = 0;
            foreach ($orderItems as $orderItem) {
                $qtyOrdered = $orderItem->getQtyOrdered();
                $qtyInvoiced = $orderItem->getQtyInvoiced();
                $qtyRefunded = $orderItem->getQtyRefunded();
                $baseGiftVoucherDiscount = $this->roundPrice($orderItem->getBaseGiftVoucherDiscount(), true, $store);
                $giftVoucherDiscount = $this->roundPrice($orderItem->getGiftVoucherDiscount(), true, $store);
                $baseDiscountAmount = $orderItem->getBaseDiscountAmount();
                $discountAmount = $orderItem->getDiscountAmount();
                $baseDiscountInvoiced = $orderItem->getBaseDiscountInvoiced();
                $discountInvoiced = $orderItem->getDiscountInvoiced();
                $baseDiscountRefunded = $orderItem->getBaseDiscountRefunded() ? $orderItem->getBaseDiscountRefunded() : 0;
                $discountRefunded = $orderItem->getDiscountRefunded() ? $orderItem->getDiscountRefunded() : 0;
                $baseGiftvoucherDiscountInvoiced = $baseGiftVoucherDiscount / $qtyOrdered * $qtyInvoiced;
                $giftvoucherDiscountInvoiced = $giftVoucherDiscount / $qtyOrdered * $qtyInvoiced;
                $baseGiftvoucherDiscountRefunded = $baseGiftVoucherDiscount / $qtyOrdered * $qtyRefunded;
                $giftvoucherDiscountRefunded = $giftVoucherDiscount / $qtyOrdered * $qtyRefunded;
                $orderItem->setBaseDiscountAmount(
                    $baseDiscountAmount + $this->roundPrice($baseGiftVoucherDiscount, true, $store)
                );
                $orderItem->setDiscountAmount(
                    $discountAmount + $this->roundPrice($giftVoucherDiscount, true, $store)
                );
                $orderItem->setBaseDiscountInvoiced(
                    $baseDiscountInvoiced + $this->roundPrice($baseGiftvoucherDiscountInvoiced, true, $store)
                );
                $orderItem->setDiscountInvoiced(
                    $discountInvoiced + $this->roundPrice($giftvoucherDiscountInvoiced, true, $store)
                );
                $orderItem->setBaseDiscountRefunded(
                    $baseDiscountRefunded + $this->roundPrice($baseGiftvoucherDiscountRefunded, true, $store)
                );
                $orderItem->setBaseDiscountRefunded(
                    $discountRefunded + $this->roundPrice($giftvoucherDiscountRefunded, true, $store)
                );
                $orderItem->setMagestoreBaseDiscount($orderItem->getMagestoreBaseDiscount() + $baseGiftVoucherDiscount);
                $orderItem->setMagestoreDiscount($orderItem->getMagestoreDiscount() + $giftVoucherDiscount);
                $totalItemsBaseGiftVoucherDiscountInvoiced += $baseGiftvoucherDiscountInvoiced;
                $totalItemsGiftVoucherDiscountInvoiced += $giftvoucherDiscountInvoiced;
                $totalItemsBaseGiftVoucherDiscountRefunded += $baseGiftvoucherDiscountRefunded;
                $totalItemsGiftVoucherDiscountRefunded += $giftvoucherDiscountRefunded;
                $orderItem->save();
            }
            $baseDiscountAmount = $order->getBaseDiscountAmount();
            $discountAmount = $order->getDiscountAmount();
            $baseDiscountInvoiced = $order->getBaseDiscountInvoiced();
            $discountInvoiced = $order->getDiscountInvoiced();
            $baseDiscountRefunded = $order->getBaseDiscountRefunded() ? $order->getBaseDiscountRefunded() : 0;
            $discountRefunded = $order->getDiscountRefunded() ? $order->getDiscountRefunded() : 0;
            $baseGiftVoucherDiscount = $order->getBaseGiftVoucherDiscount();
            $giftVoucherDiscount = $order->getGiftVoucherDiscount();
            $order->setBaseDiscountAmount($baseDiscountAmount - $baseGiftVoucherDiscount);
            $order->setDiscountAmount($discountAmount - $giftVoucherDiscount);
            $order->setBaseDiscountInvoiced($baseDiscountInvoiced - $this->roundPrice($totalItemsBaseGiftVoucherDiscountInvoiced, true, $store));
            $order->setDiscountInvoiced($discountInvoiced - $this->roundPrice($totalItemsGiftVoucherDiscountInvoiced, true, $store));
            $order->setBaseDiscountRefunded($baseDiscountRefunded - $this->roundPrice($totalItemsBaseGiftVoucherDiscountRefunded, true, $store));
            $order->setDiscountRefunded($discountRefunded - $this->roundPrice($totalItemsGiftVoucherDiscountRefunded, true, $store));
            $order->save();
        }
    }


    /**
     * Round price considering delta
     *
     * @param float $price
     * @param string $type
     * @param bool $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @return float
     */
    public function roundPrice($price, $negative = false, $store)
    {
        $store->getStoreId();
        if ($price) {
            if (!isset($this->_calculators[$store->getStoreId()])) {
                $this->_calculators[$store->getStoreId()] = $this->_calculatorFactory->create(['scope' => $store]);
            }
            $price = $this->_calculators[$store->getStoreId()]->deltaRound($price, $negative);
        }
        return $price;
    }

}