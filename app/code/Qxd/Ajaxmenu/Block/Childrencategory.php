<?php
namespace Qxd\Ajaxmenu\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\Block\Template\Context;

class Childrencategory extends \Magento\Framework\View\Element\Template
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_categoryCollection = $categoryCollection;
    }


    public function getCategories(){
        $categories = $this->_categoryCollection->create()                              
            ->addAttributeToSelect('*')
            ->setStore($this->_storeManager->getStore()); //categories from current store will be fetched

        return $categories;
    }
    
}