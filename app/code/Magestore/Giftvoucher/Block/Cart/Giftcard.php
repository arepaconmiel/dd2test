<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Cart;

/**
 * Class Giftcard
 * @package Magestore\Giftvoucher\Block\Cart
 */
class Giftcard extends \Magestore\Giftvoucher\Block\Redeem\Form
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magestore_Giftvoucher::giftvoucher/giftcard/coupon.phtml');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $store_Id = $this->getQuote()->getStoreId();
        return (
            $this->helper->getGeneralConfig('active') &&
            $this->helper->getInterfaceCheckoutConfig('show_gift_card', $store_Id)
        );
    }
}
