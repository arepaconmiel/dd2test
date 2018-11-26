<?php

namespace Qxd\AddOrder\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;

class AddOrderCustomerExist implements \Magento\Framework\Event\ObserverInterface
{
	protected $_customerInterface;

	public function __construct(CustomerRepositoryInterface $customerInterface) {
        $this->_customerInterface = $customerInterface;
    }

	public function execute(\Magento\Framework\Event\Observer $observer) {

        error_log('I am on event', 3, '/tmp/magento2.log');
        error_log("\n\n", 3, '/tmp/magento2.log');

        $order = $observer->getEvent()->getOrder();

        $email = $order->getCustomerEmail();

        error_log($email, 3, '/tmp/magento2.log');
        error_log("\n\n", 3, '/tmp/magento2.log');

        if($order->getCustomerIsGuest())
        {
        	error_log('customer get', 3, '/tmp/magento2.log');
        	error_log("\n\n", 3, '/tmp/magento2.log');
            //$customer = Mage::getModel('customer/customer');
            $websiteId = Mage::app()->getWebsite()->getId();
            
            if ($websiteId) {
                $customer->setWebsiteId($websiteId);
            }
            $customer = $this->_customerInterface->get($email);
            error_log($customer->getId(), 3, '/tmp/magento2.log');
        	error_log("\n\n", 3, '/tmp/magento2.log');
            if ($cid = $customer->getId()){

                // Set customer at order
                $order->setCustomerId($cid);
                $order->setCustomer($customer);
                $order->setCustomerIsGuest(false);
                $order->setCustomerGroupId($customer->getGroupId());
            }
        }
    }
}