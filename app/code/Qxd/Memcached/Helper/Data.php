<?php
namespace Qxd\Memcached\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{   
    public function __construct(
        \Qxd\Memcached\Model\Connector $connector,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Tax\Helper\Data $taxHelper
    ) {
        $this->_connector = $connector;
        $this->_wishlistHelper = $wishlistHelper;     
        $this->_taxHelper = $taxHelper; 
    }

    /*
    * Start memcached connection
    */

    public function initMemcached()
    {   
        $_memcached = $this->_connector->getInstance();
        $memcachedConnector=null;
        if($_memcached){ $memcachedConnector=$_memcached->getMemcachedConn(); }

        return $memcachedConnector;
    }

    public function QXDTaxCartSettings()
    {
        $result=array();

        $_memcached = $this->initMemcached();

        if(!$_memcached || !$_memcached->get('tax_cart'))
        {   
            $taxCartSettings['displayCartBothPrices'] = $this->_taxHelper->displayCartBothPrices();
            $taxCartSettings['displayCartPriceExclTax'] = $this->_taxHelper->displayCartPriceExclTax();
            $taxCartSettings['displayCartPriceInclTax'] = $this->_taxHelper->displayCartPriceInclTax();

            if($_memcached){ $_memcached->set('tax_cart',$taxCartSettings); }
            $result=$taxCartSettings;
        }
        else{ $result=$_memcached->get('tax_cart'); }

        return $result;
    }
    public function QXDWishlistCartSettings()
    {
        $result=array();

        $_memcached = $this->initMemcached();

        if(!$_memcached || !$_memcached->get('wishlist_cart'))
        {
            $wishlistCartSettings['isAllowInCart'] = $this->_wishlistHelper->isAllowInCart();

            if($_memcached){ $_memcached->set('wishlist_cart',$wishlistCartSettings); }
            $result=$wishlistCartSettings;
        }
        else{ $result=$_memcached->get('wishlist_cart'); }

        return $result;
    }
}
