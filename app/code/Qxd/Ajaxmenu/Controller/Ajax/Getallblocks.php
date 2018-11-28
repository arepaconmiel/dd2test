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
            \Magento\Catalog\Model\CategoryFactory $categoryFactory,
            \Magento\Framework\Session\SessionManagerInterface $coreSession,
            \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
            \Qxd\Memcached\Helper\Data $memcached
        )
    {
        $this->_responseFactory = $responseFactory;
        $this->_request = $request;
        $this->_resultJsonFactory = $resultJsonFactory;        
        $this->_memcached = $memcached;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_coreSession = $coreSession;
        $this->_remoteAddress = $remoteAddress;
        
        parent::__construct($context);
    }

 
    public function execute()
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_AJAX_ERROR.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer); 


        $result=array('stalker'=>"",'menu'=>"",'banner'=>'','quickMessage'=>'','singleSubscriber'=>'');
        $memcached = $this->_memcached->initMemcached();
        $resultPage = $this->_resultPageFactory ->create();
        $layout = $resultPage->getLayout();

        //STALKER
        $resultStalker="";
        try{
            if(!$memcached || !$memcached->get('_stalker'))
            {   
                $block = $layout->createBlock('Qxd\Singlesubscriber\Block\Stalker')->setTemplate('Qxd_SingleSubscriber::stalker.phtml');
                $resultStalker=$block->toHtml();

                if($memcached){ $memcached->set('_stalker',$resultStalker); }
            }
            else{ $resultStalker=$memcached->get('_stalker'); }
        }catch (Exception $e){ 
            $logger->info(print_r($e->getMessage(), true));
        }

        $result['stalker']=$resultStalker;

        //MENU
        $resultMenu="";
        try{
            if(!$memcached || !$memcached->get('_menu'))
            {   
                $catFactory = $this->_categoryFactory->create();
                $cat = $catFactory->load(2);
                $subcatIds = $cat->getChildren();
                $subcatArrays=explode(',',$subcatIds);
  
                foreach($subcatArrays as $catId) { 
                    $resultMenu['#category_'.$catId]=$layout->createBlock('Qxd\Ajaxmenu\Block\Childrencategory')->setData('category',$catId)->setTemplate('Qxd_Ajaxmenu::childrencategory.phtml')->toHtml(); 
                }

                if($memcached){ $memcached->set('_menu',$resultMenu); }
            }
            else{ $resultMenu=$memcached->get('_menu'); }

            
        }catch (Exception $e){ 
            $logger->info(print_r($e->getMessage(), true)); 
        }

        $result['menu']=$resultMenu;

        //BANNER
        $resultBanner="";
        try
        {
            if(!$memcached || !$memcached->get('_banner'))
            {
                $resultBanner=$layout->createBlock('Magento\Cms\Block\Block')->setBlockId('sitewide-banner')->toHtml();
                if($memcached){ $memcached->set('_banner',$resultBanner); }
            }
            else{ $resultBanner=$memcached->get('_banner'); }

        }catch (Exception $e){ 
            $logger->info(print_r($e->getMessage(), true));
        }

        $result['banner']=$resultBanner;

        //QUICK MESSAGE
        $resultQuickMessage="";
        try
        {
            if(!$memcached || !$memcached->get('_quickMessage'))
            {
                $resultQuickMessage=$layout->createBlock('Magento\Cms\Block\Block')->setBlockId('header_message')->toHtml();
                if($memcached){ $memcached->set('_quickMessage',$resultQuickMessage); }
            }
            else{ $resultQuickMessage=$memcached->get('_quickMessage'); }
        }catch (Exception $e){ 
            $logger->info(print_r($e->getMessage(), true));
        }

        $result['quickMessage']=$resultQuickMessage;

        //POP UP
        $resultSingleSubscriber="";
        try
        {
            $cookie = isset($_COOKIE['popup-shown']) ? $_COOKIE['popup-shown'] : "";
            if (!$cookie) {
                setcookie('popup-shown', 'true', time() + 604800, "/");
            }
            $resultSingleSubscriber=$cookie;
        }catch (Exception $e){ 
            $logger->info(print_r($e->getMessage(), true)); 
        }

        $result['singleSubscriber']=$resultSingleSubscriber;

        //CUSTOMER LOCATION
        $customerLocation= $this->_coreSession->getCustomerLocation();
        try
        {
            if(empty($customerLocation))
            {   
                $customerIp=$this->_remoteAddress->getRemoteAddress();
                $customerLocation=$this->getCustomerLocationByIp($customerIp);
                $this->_coreSession->setCustomerLocation($customerLocation);
            }

        }catch (Exception $e){ 
            $logger->info(print_r($e->getMessage(), true));  
        }

        $result['customerLocation']=json_encode($customerLocation);
        $jsonResult = $this->_resultJsonFactory->create();
        $jsonResult->setData($result);
        return $jsonResult;
        
    }


    public function getCustomerLocationByIp($ip)
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_AJAX_ERROR.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer); 
        $result=array('country'=>"",'region'=>"",'regionId'=>"",'city'=>"");

        try
        {
            $url="https://ipapi.co/$ip/json";
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);

            $responseJson = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($status == 200 || $status == 202)
            {
                $response = json_decode($responseJson, true);

                if( isset($response['country']) ){ $result['country']=$response['country']; }
                if( isset($response['region']) ){ $result['region']=$response['region']; }
                if( isset($response['regionId']) ){ $result['regionId']=$response['regionId']; }
                if( isset($response['city']) ){ $result['city']=$response['city']; }
            }

            curl_close($ch);

        }catch (Exception $e){ 
            $logger->info(print_r($e->getMessage(), true)); 
        }

        return $result;
    }
}