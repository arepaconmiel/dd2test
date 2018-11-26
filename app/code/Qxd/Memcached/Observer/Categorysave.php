<?php
namespace Qxd\Memcached\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class Categorysave implements ObserverInterface
{
 	public function __construct(
        \Qxd\Memcached\Helper\Data $helper

    ) {
        $this->_helper = $helper;
    }


    /*
    *   Clear memcached key related with the category
    */

    public function execute(Observer $observer)
    {
        $_memcached = $this->_helper->initMemcached();
        if($_memcached)
        {	
            $_memcachedKeys=$_memcached->getMulti(array('_homeCategories'));
            foreach($_memcachedKeys as $k=>$data) { if($data){ $_memcached->delete($k); } }
        }
    }
}