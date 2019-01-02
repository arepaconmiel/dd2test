<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Qxd\Overwrite\Checkout\Controller\Cart;

use Magento\Framework;
use Magento\Checkout\Model\Cart as CustomerCart;

class EstimatePost extends \Magento\Checkout\Controller\Cart\EstimatePost
{	

	public function __construct(
        Framework\App\Action\Context $context,
        Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->_coreSession = $coreSession;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quoteRepository
        );
    }

	/**
     * Initialize shipping information
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $country = (string)$this->getRequest()->getParam('country_id');
        $postcode = (string)$this->getRequest()->getParam('estimate_postcode');
        $city = (string)$this->getRequest()->getParam('estimate_city');
        $regionId = (string)$this->getRequest()->getParam('region_id');
        $region = (string)$this->getRequest()->getParam('region');

        $this->cart->getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
        $this->quoteRepository->save($this->cart->getQuote());
        $this->cart->save();
        $this->_coreSession->setEstimateAsked('true'); 
        return $this->_goBack();
    }
}