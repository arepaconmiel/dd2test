<?php
namespace Qxd\SingleSubscriber\Controller\Index;

use Magento\Framework\Controller\ResultFactory; 

class Ajax extends \Magento\Framework\App\Action\Action
{
	//protected $_pageFactory;
	protected $request; 

	public function __construct(
		\Magento\Framework\App\Action\Context $context)
		//\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		//$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		//return $this->_pageFactory->create();
		$cookie = isset($_COOKIE['popup-shown']) ? $_COOKIE['popup-shown'] : "";
		if (!$cookie) {
			//setcookie('popup-shown', 'true', time() + 604800, "/");
			setcookie('popup-shown', 'true', time() + 60, "/");
		}
		//return 'var check_cookie = '.$cookie;
		//$this->getResponse()->setBody($cookie);

    	$resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
    	$resultJson->setData($cookie); 
    	return $resultJson; 
	}
}