<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Service\GiftCode;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GiftCodeManagementService
 * @package Magestore\Giftvoucher\Service\GiftCode
 */
class GiftCodeManagementService implements \Magestore\Giftvoucher\Api\GiftCode\GiftCodeManagementServiceInterface
{
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory
     */
    protected $giftCodeCollectionFactory;

    /**
     * @var \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface
     */
    protected $giftCodeRepository;

    /**
     * @var \Magestore\Giftvoucher\Model\HistoryFactory
     */
    protected $giftCodeHistoryFactory;


    /**
     * @var \Magestore\Giftvoucher\Helper\System
     */
    protected $helperSystem;


    /**
     * @var \Magestore\Giftvoucher\Model\GiftvoucherFactory
     */
    protected $giftvoucherFactory;

    /**
     * GiftCodeManagementService constructor.
     * @param \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $giftCodeCollectionFactory
     * @param \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $giftCodeRepository
     * @param \Magestore\Giftvoucher\Model\HistoryFactory $giftCodeHistoryFactory
     * @param \Magestore\Giftvoucher\Helper\System $helperSystem
     * @param \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
     */
    public function __construct(
        \Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher\CollectionFactory $giftCodeCollectionFactory,
        \Magestore\Giftvoucher\Api\GiftvoucherRepositoryInterface $giftCodeRepository,
        \Magestore\Giftvoucher\Model\HistoryFactory $giftCodeHistoryFactory,
        \Magestore\Giftvoucher\Helper\System $helperSystem,
        \Magestore\Giftvoucher\Model\GiftvoucherFactory $giftvoucherFactory
    )
    {

        $this->giftCodeCollectionFactory = $giftCodeCollectionFactory;
        $this->giftCodeRepository = $giftCodeRepository;
        $this->giftCodeHistoryFactory = $giftCodeHistoryFactory;
        $this->helperSystem = $helperSystem;
        $this->giftvoucherFactory = $giftvoucherFactory;
    }

    /**
     * Get gift codes generated from gift card item
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $orderItem
     * @return \Magestore\Giftvoucher\Api\Data\GiftVoucherInterface[]
     */
    public function getGiftCodesFromOrderItem($orderItem)
    {
        $items = [];
        $collection = $this->giftCodeCollectionFactory->create()
            ->addItemFilter($orderItem->getQuoteItemId());
        if ($collection->getSize()) {
            foreach ($collection as $item) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftVoucherInterface $giftCode
     * @param int $orderIncrementId
     * @return $this
     */
    public function refundGiftCode($giftCode, $orderIncrementId)
    {
        $giftCode->setStatus(\Magestore\Giftvoucher\Model\Source\Status::STATUS_REFUNDED);
        $giftCode->setGiftvoucherComments(__('Refunded by %1', $this->helperSystem->getCurUser()->getUserName())
            . "\n" . $giftCode->getGiftvoucherComments()
        );
        $this->giftCodeRepository->save($giftCode);
        $history = $this->giftCodeHistoryFactory->create();
        $history->setData([
            'giftvoucher_id' => $giftCode->getGiftvoucherId(),
            'status' => \Magestore\Giftvoucher\Model\Source\Status::STATUS_REFUNDED,
            'comments' => __('Refund order %1', $orderIncrementId),
            'amount' => $giftCode->getBalance(),
            'action' => \Magestore\Giftvoucher\Model\Actions::ACTIONS_REFUND,
            'order_increment_id' => $orderIncrementId,
            'currency' => $giftCode->getCurrency(),
            'extra_content' => __('Refunded by %1', $this->helperSystem->getCurUser()->getUserName()),
        ])->save();
    }

    /**
     * Cancel gift code
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftVoucherInterface $giftCode
     * @param int $orderIncrementId
     * @return $this
     */
    public function cancelGiftCode($giftCode, $orderIncrementId)
    {
        $giftCode->setStatus(\Magestore\Giftvoucher\Model\Source\Status::STATUS_DISABLED);
        $giftCode->setGiftvoucherComments(__('Canceled by %1', $this->helperSystem->getCurUser()->getUserName())
            . "\n" . $giftCode->getGiftvoucherComments()
        );
        $this->giftCodeRepository->save($giftCode);
        $history = $this->giftCodeHistoryFactory->create();
        $history->setData([
            'giftvoucher_id' => $giftCode->getGiftvoucherId(),
            'status' => \Magestore\Giftvoucher\Model\Source\Status::STATUS_DISABLED,
            'comments' => __('Cancel order %1', $orderIncrementId),
            'amount' => $giftCode->getBalance(),
            'action' => \Magestore\Giftvoucher\Model\Actions::ACTIONS_CANCEL,
            'order_increment_id' => $orderIncrementId,
            'currency' => $giftCode->getCurrency(),
            'extra_content' => __('Canceled by %1', $this->helperSystem->getCurUser()->getUserName()),
        ])->save();
    }

    /**
     * Check gift code
     *
     * @param string $giftCode
     * @return \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface || null
     */
    public function check($giftCode)
    {
        $model = $this->giftvoucherFactory->create()->load($giftCode, 'gift_code');
        if ($model->getId()) {
            return $model;
        } else {
            return null;
        }
    }

    /**
     * Check gift code
     *
     * @param string $giftCode
     * @return \Magestore\Giftvoucher\Api\Data\HistoryInterface[] || null
     */
    public function checkHistory($giftCode)
    {
        $giftVoucher = $this->giftvoucherFactory->create()->loadByCode($giftCode);
        if (!$giftVoucher->getId()) {
            return null;
        }
        $collection = $this->giftCodeHistoryFactory->create()
            ->getCollection()
            ->addFieldToFilter('giftvoucher_id', $giftVoucher->getId());

        return $collection->getItems();
    }

    /**
     * Send email
     *
     * @param \Magestore\Giftvoucher\Api\Data\GiftcodeSendEmailJsonInterface $data
     * @return bool
     * @throws NoSuchEntityException
     */
    public function sendEmail($data)
    {

        $giftCode = $data->getGiftCode();
        $sendToFriend = $data->getType();
        $giftVoucher = $this->giftvoucherFactory->create()->loadByCode($giftCode);
        if (!$giftVoucher->getId()) {
            throw new NoSuchEntityException(
                __('Gift card %1 is not exists', $giftCode)
            );
        }
        if ($sendToFriend == 'to_friend') {
            if ($giftVoucher->getRecipientEmail()) {
                $giftVoucher->sendEmailToFriend();
            } else {
                throw new NoSuchEntityException(
                    __('The email address of Gift Card recipient does not exist.')
                );
            }
        } elseif ($sendToFriend == 'to_all') {
            if ($giftVoucher->getCustomerEmail()) {
                $giftVoucher->sendEmail();
            } else {
                throw new NoSuchEntityException(
                    __('Email of Gift card Purchaser does not exist.')
                );
            }
        } elseif ($sendToFriend == 'to_owner') {
            if ($giftVoucher->getCustomerEmail()) {
                $giftVoucher->setRecipientEmail('');
                $giftVoucher->sendEmail();
            } else {
                throw new NoSuchEntityException(
                    __('Email of Gift card Purchaser does not exist.')
                );
            }
        } else {
            throw new NoSuchEntityException(
                __('Invalid data')
            );
        }
        return true;
    }

    /**
     * Get gift code from gift code array
     * Return an array with key is gift code and value is gift code model
     *
     * @param array $giftcode
     * @return array
     */
    public function getUsableGiftCodeCollection($giftcode = [])
    {
        $result = [];
        $collection = $this->giftCodeCollectionFactory->create()
            ->addFieldToFilter(\Magestore\Giftvoucher\Api\Data\GiftvoucherInterface::GIFT_CODE, ['in' => $giftcode])
            ->addFieldToFilter(\Magestore\Giftvoucher\Api\Data\GiftvoucherInterface::BALANCE, ['gt' => 0]);
        /** @var \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface $item */
        foreach ($collection as $item) {
            $result[$item->getGiftCode()] = $item;
        }
        return $result;
    }
}
