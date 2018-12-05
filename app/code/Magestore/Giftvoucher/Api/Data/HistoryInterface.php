<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Api\Data;

/**
 * Gift Code History interface.
 * @api
 */
interface HistoryInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */

    const HISTORY_ID = 'history_id';
    const GIFTVOUCHER_ID = 'giftvoucher_id';
    const ACTION = 'action';
    const CREATED_AT = 'created_at';
    const AMOUNT = 'amount';
    const CURRENCY = 'currency';
    const STATUS = 'status';
    const COMMENTS = 'comments';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const QUOTE_ITEM_ID = 'quote_item_id';
    const ORDER_AMOUNT = 'order_amount';
    const EXTRA_CONTENT = 'extra_content';
    const BALANCE = 'balance';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_EMAIL = 'customer_email';

    /**#@-*/


    /**
     * Get history ID
     *
     * @return int|null
     */
    public function getHistoryId();

    /**
     * Set history ID
     *
     * @param int $historyId
     * @return HistoryInterface
     */
    public function setHistoryId($historyId);
    /**
     * Get giftvoucher ID
     *
     * @return int|null
     */
    public function getGiftvoucherId();

    /**
     * Set giftvoucher ID
     *
     * @param int $giftvoucherId
     * @return HistoryInterface
     */
    public function setGiftvoucherId($giftvoucherId);
    /**
     * Get action
     *
     * @return int|null
     */
    public function getAction();

    /**
     * Set action
     *
     * @param int $action
     * @return HistoryInterface
     */
    public function setAction($action);
    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return HistoryInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount();

    /**
     * Set amount
     *
     * @param string $amount
     * @return HistoryInterface
     */
    public function setAmount($amount);
    /**
     * Get Gift code currency
     *
     * @return string|null
     */
    public function getCurrency();

    /**
     * Set Gift code currency
     *
     * @param string $currency
     * @return HistoryInterface
     */
    public function setCurrency($currency);
    /**
     * Get Gift code status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Gift code status
     *
     * @param int $status
     * @return HistoryInterface
     */
    public function setStatus($status);
    /**
     * Get comments
     *
     * @return string|null
     */
    public function getComments();

    /**
     * Set comments
     *
     * @param string $comments
     * @return HistoryInterface
     */
    public function setComments($comments);
    /**
     * Get order increment id
     *
     * @return int|null
     */
    public function getOrderIncrementId();

    /**
     * Set order increment id
     *
     * @param int $orderIncrementId
     * @return HistoryInterface
     */
    public function setOrderIncrementId($orderIncrementId);
    /**
     * Get quote item id
     *
     * @return int|null
     */
    public function getQuoteItemId();

    /**
     * Set quote item id
     *
     * @param int $quoteItemId
     * @return HistoryInterface
     */
    public function setQuoteItemId($quoteItemId);
    /**
     * Get order amount
     *
     * @return string
     */
    public function getOrderAmount();

    /**
     * Set order amount
     *
     * @param string $amount
     * @return HistoryInterface
     */
    public function setOrderAmount($amount);
    /**
     * Get extra content
     *
     * @return string|null
     */
    public function getExtraContent();

    /**
     * Set extra comments
     *
     * @param string $extraContent
     * @return HistoryInterface
     */
    public function setExtraContent($extraContent);
    /**
     * Get balance
     *
     * @return string
     */
    public function getBalance();

    /**
     * Set balance
     *
     * @param string $balance
     * @return HistoryInterface
     */
    public function setBalance($balance);
    /**
     * Get customer ID
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return HistoryInterface
     */
    public function setCustomerId($customerId);
    /**
     * Get customer email
     *
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer email
     *
     * @param $customerEmail
     * @return HistoryInterface
     * @internal param string $extraContent
     */
    public function setCustomerEmail($customerEmail);
}
