<?php
 
namespace Qxd\Ajaxmenu\Controller\Ajax;

 
class Getmenu extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
 
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Magento\Framework\App\ResponseFactory $responseFactory,
            \Magento\Framework\App\Request\Http $request,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory,
            \Magento\Catalog\Model\CategoryFactory $categoryFactory,
            \Qxd\Memcached\Helper\Data $memcached
        )
    {
        $this->_responseFactory = $responseFactory;
        $this->_request = $request;
        $this->_resultJsonFactory = $resultJsonFactory;        
        $this->_memcached = $memcached;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_categoryFactory = $categoryFactory;
        
        parent::__construct($context);
    }

 
    public function execute()
    {   

        $result=array();
        $memcached = $this->_memcached->initMemcached();
        $resultPage = $this->_resultPageFactory ->create();
        $layout = $resultPage->getLayout();

        //MENU
   
        if(!$memcached || !$memcached->get('_menu'))
        {   
            $catFactory = $this->_categoryFactory->create();
            $cat = $catFactory->load(2);
            $subcatIds = $cat->getChildren();
            $subcatArrays=explode(',',$subcatIds);
  
            foreach($subcatArrays as $catId) { 
                $result['#category_'.$catId]=$layout->createBlock('Qxd\Ajaxmenu\Block\Childrencategory')->setData('category',$catId)->setTemplate('Qxd_Ajaxmenu::childrencategory.phtml')->toHtml(); 
            }

            if($memcached){ $memcached->set('_menu',$result); }
        }
        else{ $result=$memcached->get('_menu'); }


        $jsonResult = $this->_resultJsonFactory->create();
        $jsonResult->setData($result);
        return $jsonResult;
        
    }

}