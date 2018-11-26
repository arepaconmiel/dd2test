<?php
 
namespace Qxd\Ajaxmenu\Controller\Ajax;

 
class Getallblocks extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
 
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\App\ResponseFactory $responseFactory,
            \Magento\Framework\App\Request\Http $request,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Qxd\Memcached\Helper\Data $memcached
        )
    {
        $this->_responseFactory = $responseFactory;
        $this->_request = $request;
        $this->_resultJsonFactory = $resultJsonFactory;        
        $this->_memcached = $memcached;
        $this->_resultPageFactory = $resultPageFactory;
        
        parent::__construct($context);
    }

 
    public function execute()
    {       
        $result=array('stalker'=>"",'menu'=>"",'banner'=>'','quickMessage'=>'','singleSubscriber'=>'');
        $memcached = $this->_memcached->initMemcached();
        $resultPage = $this->_resultPageFactory ->create();
        $layout = $resultPage->getLayout();

        $resultStalker="";
        try
        {
           /* if(!$memcached || !$memcached->get('_stalker'))
            {
                $block = $layout->createBlock('Qxd/Singlesubscriber/Block/Stalker')->setTemplate('singlesubscriber/stalker.phtml');
                $resultStalker=$block->toHtml();

                if($memcached){ $memcached->set('_stalker',$resultStalker); }
            }
            else{ $resultStalker=$memcached->get('_stalker'); }*/
        }catch (Exception $e){ 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_AJAX_ERROR.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer); 
            $logger->info(print_r($e->getMessage(), true));
        }
    }
}