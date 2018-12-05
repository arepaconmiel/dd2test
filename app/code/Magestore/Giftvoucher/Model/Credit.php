<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Model;

/**
 * Giftvoucher Credit Model
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class Credit extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Credit constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
    ) {
        $this->_customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init('Magestore\Giftvoucher\Model\ResourceModel\Credit');
    }

    /**
     * @return Credit
     */
    public function getCreditAccountLogin()
    {
        $customerId = $this->_customerSession->getCustomerId();
        return $this->getCreditByCustomerId($customerId);
    }

    /**
     * @param $customerId
     * @return $this
     */
    public function getCreditByCustomerId($customerId)
    {
        $collection = $this->getCollection()->addFieldToFilter('customer_id', $customerId);
        if ($collection->getSize()) {
            $id = $collection->getFirstItem()->getId();
            $this->load($id);
        }
        return $this;
    }
}
