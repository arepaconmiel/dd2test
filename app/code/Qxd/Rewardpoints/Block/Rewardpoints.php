<?php
namespace Qxd\Rewardpoints\Block;
 
class Rewardpoints extends \Magento\Framework\View\Element\Template
{

	/**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ){
        parent::__construct($context, $data);
        $this->_checkoutSession = $checkoutSession;
    }
	
    /**
    * Return earned reward points 
    *
    * @return int
    */

    public function displayRewardPoints(){
        $order = $this->_checkoutSession->getLastRealOrder();
        return $order->getRewardPoints();
    }

}




