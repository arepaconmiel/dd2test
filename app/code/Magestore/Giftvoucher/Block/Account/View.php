<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Account;

/**
 * Giftvoucher Account View block
 */
class View extends \Magestore\Giftvoucher\Block\Account
{
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\Collection
     */
    protected $_loadCollection;

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->getStore()->getConfig('general/locale/timezone');
    }

    /**
     * @return mixed
     */
    public function getSearchString()
    {
        return $this->getRequest()->getParam('qgc');
    }
    
    protected function _processSearch()
    {
        $q = $this->getSearchString();
        $statusArray = \Magestore\Giftvoucher\Model\Status::getOptionArray();
        if (in_array($q, $statusArray)) {
            $this->_loadCollection->addFieldToFilter('status', array_search($q, $statusArray));
        } else {
            $this->_loadCollection->addFieldToFilter([
                'gift_code',
                'balance',
                'added_date',
                'expired_at',
            ], array_fill(0, 4, ['like' => "%$q%"]));
        }
    }
    
    /**
     * Get Customer Gift Codes
     *
     * @return \Magestore\Giftvoucher\Model\ResourceModel\CustomerVoucher\Collection
     */
    public function getGiftCodes()
    {
        if (!$this->_loadCollection) {
            $customerId = $this->getCustomer()->getId();
            $timezone   = $this->datetime->getGmtOffset('hours');
            $this->_loadCollection = $this->_collectionFactory->create()
                ->joinCustomer($customerId, $timezone)
                ->setOrder('customer_voucher_id', 'DESC');
            // Process search
            $this->_processSearch();
        }
        return $this->_loadCollection;
    }

    /**
     * @return $this
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getGiftCodes()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'giftvoucher.index.index'
            )->setCollection(
                $this->getGiftCodes()
            );
            $this->setChild('pager', $pager);
            $this->getGiftCodes()->load();
        }
        return $this;
    }


    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    /**
     * Get Gift Voucher Model
     *
     * @param int $id
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     */
    public function getGiftcodeModel($id)
    {
        return $this->_giftvoucherFactory->create()->load($id);
    }
    
    /**
     * Can print gift code
     *
     * @param \Magestore\Giftvoucher\Model\CustomerVoucher $row
     * @return boolean
     */
    public function canPrint($row)
    {
        return !$row->getSetId() || ($row->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE);
    }
    
    /**
     * Check gift code is available
     *
     * @param \Magestore\Giftvoucher\Model\CustomerVoucher $row
     * @return boolean
     */
    public function isAvailable($row)
    {
        return $row->getStatus() < \Magestore\Giftvoucher\Model\Status::STATUS_DISABLED;
    }
}
