<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model;

use Magestore\Giftvoucher\Api\Data\HistoryInterface;

/**
 * Giftvoucher History Model
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class History extends \Magento\Framework\Model\AbstractModel implements \Magestore\Giftvoucher\Api\Data\HistoryInterface
{
    const STATUS_PENDING = 2;
    const STATUS_COMPLETE = 1;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magestore\Giftvoucher\Model\ResourceModel\History');
    }
    
    /**
     * Filter Gift Card history
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftVoucher
     * @param \Magento\Sales\Model\Order $order
     * @param int|array $action
     * @return \Magestore\Giftvoucher\Model\ResourceModel\History\Collection
     */
    public function getCollectionByOrderAction($giftVoucher, $order, $action)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('giftvoucher_id', $giftVoucher->getId())
            ->addFieldToFilter('order_increment_id', $order->getIncrementId());
        if (is_array($action)) {
            $collection->addFieldToFilter('action', ['in' => $action]);
        } else {
            $collection->addFieldToFilter('action', $action);
        }
        return $collection;
    }
    
    /**
     * Get the total amount of Gift Card spent in order
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftVoucher
     * @param \Magento\Sales\Model\Order $order
     * @return float
     */
    public function getTotalSpent($giftVoucher, $order)
    {
        $total = 0;
        $histories = $this->getCollectionByOrderAction(
            $giftVoucher,
            $order,
            \Magestore\Giftvoucher\Model\Actions::ACTIONS_SPEND_ORDER
        );
        foreach ($histories as $history) {
            $total += $history->getAmount();
        }
        return $total;
    }
    
    /**
     * Get the total amount of Gift Card refunded in order
     *
     * @param \Magestore\Giftvoucher\Model\Giftvoucher $giftVoucher
     * @param \Magento\Sales\Model\Order $order
     * @return float
     */
    public function getTotalRefund($giftVoucher, $order)
    {
        $total = 0;
        $histories = $this->getCollectionByOrderAction(
                $giftVoucher, $order, [
                    \Magestore\Giftvoucher\Model\Actions::ACTIONS_REFUND,
                    \Magestore\Giftvoucher\Model\Actions::ACTIONS_CANCEL,
                ]
        );
        foreach ($histories as $history) {
            $total += $history->getAmount();
        }
        return $total;
    }

    /**
     * Get history ID
     *
     * @return int|null
     */
    public function getHistoryId()
    {
        return $this->getData(self::HISTORY_ID);
    }

    /**
     * Set history ID
     *
     * @param int $historyId
     * @return HistoryInterface
     */
    public function setHistoryId($historyId)
    {
        $this->setData(self::HISTORY_ID, $historyId);
        return $this;
    }
    /**
     * Get giftvoucher ID
     *
     * @return int|null
     */
    public function getGiftvoucherId()
    {
        return $this->getData(self::GIFTVOUCHER_ID);
    }

    /**
     * Set giftvoucher ID
     *
     * @param int $giftvoucherId
     * @return HistoryInterface
     */
    public function setGiftvoucherId($giftvoucherId)
    {
        $this->setData(self::HISTORY_ID, $giftvoucherId);
        return $this;
    }
    /**
     * Get action
     *
     * @return int|null
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * Set action
     *
     * @param int $action
     * @return HistoryInterface
     */
    public function setAction($action)
    {
        $this->setData(self::ACTION, $action);
        return $this;
    }
    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return HistoryInterface
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);
        return $this;
    }
    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return HistoryInterface
     */
    public function setAmount($amount)
    {
        $this->setData(self::AMOUNT, $amount);
        return $this;
    }
    /**
     * Get Gift code currency
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * Set Gift code currency
     *
     * @param string $currency
     * @return HistoryInterface
     */
    public function setCurrency($currency)
    {
        $this->setData(self::CURRENCY, $currency);
        return $this;
    }
    /**
     * Get Gift code status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set Gift code status
     *
     * @param int $status
     * @return HistoryInterface
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }
    /**
     * Get comments
     *
     * @return string|null
     */
    public function getComments()
    {
        return $this->getData(self::COMMENTS);
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return HistoryInterface
     */
    public function setComments($comments)
    {
        $this->setData(self::COMMENTS, $comments);
        return $this;
    }
    /**
     * Get order increment id
     *
     * @return int|null
     */
    public function getOrderIncrementId()
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * Set order increment id
     *
     * @param int $orderIncrementId
     * @return HistoryInterface
     */
    public function setOrderIncrementId($orderIncrementId)
    {
        $this->setData(self::ORDER_INCREMENT_ID, $orderIncrementId);
        return $this;
    }
    /**
     * Get quote item id
     *
     * @return int|null
     */
    public function getQuoteItemId()
    {
        return $this->getData(self::QUOTE_ITEM_ID);
    }

    /**
     * Set quote item id
     *
     * @param int $quoteItemId
     * @return HistoryInterface
     */
    public function setQuoteItemId($quoteItemId)
    {
        $this->setData(self::QUOTE_ITEM_ID, $quoteItemId);
        return $this;
    }
    /**
     * Get order amount
     *
     * @return string
     */
    public function getOrderAmount()
    {
        return $this->getData(self::ORDER_AMOUNT);
    }

    /**
     * Set order amount
     *
     * @param string $amount
     * @return HistoryInterface
     */
    public function setOrderAmount($amount)
    {
        $this->setData(self::ORDER_AMOUNT, $amount);
        return $this;
    }
    /**
     * Get extra content
     *
     * @return string|null
     */
    public function getExtraContent()
    {
        return $this->getData(self::EXTRA_CONTENT);
    }

    /**
     * Set extra comments
     *
     * @param string $extraContent
     * @return HistoryInterface
     */
    public function setExtraContent($extraContent)
    {
        $this->setData(self::EXTRA_CONTENT, $extraContent);
        return $this;
    }
    /**
     * Get balance
     *
     * @return string
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * Set balance
     *
     * @param string $balance
     * @return HistoryInterface
     */
    public function setBalance($balance)
    {
        $this->setData(self::BALANCE, $balance);
        return $this;
    }
    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return HistoryInterface
     */
    public function setCustomerId($customerId)
    {
        $this->setData(self::CUSTOMER_ID, $customerId);
        return $this;
    }
    /**
     * Get customer email
     *
     * @return string|null
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * Set customer email
     *
     * @param $customerEmail
     * @return HistoryInterface
     * @internal param string $extraContent
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
        return $this;
    }
}
