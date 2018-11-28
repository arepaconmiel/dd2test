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


    public function getTreeCategories($parentId, $isChild){
        $allCats = $this->_categoryCollection->create()                              
            ->addAttributeToSelect('*')
            ->setStore($this->_storeManager->getStore())
            ->addAttributeToFilter('is_active',1)
            ->addAttributeToFilter('include_in_menu',1)
            ->addAttributeToFilter('parent_id',array('eq' => $parentId))
            ->addAttributeToSort('position', 'ASC');

        $class = ($isChild) ? "" : "parent";
        $html = '';

        foreach ($allCats as $category)
        {
            if($category->getData('level') == 3)
            {
                $html .= '<li class="level1 column-order1 first "'.$class.'>';
                $html .='<a href="'.$category->getUrl().'" class="level1 has-children">'.$category->getName().'</a>';
                $html .='<ul class="level1">';
            }

            if($category->getData('level') == 4)
            {
                $html .='<li class="level2 column-order1-1 first"><a href="'.$category->getUrl().'" class="level2 ">'.$category->getName().'</a></li>';
            }

            $subcats = $category->getChildren();
            if($subcats != ''){
                $html .= $this->getTreeCategories($category->getId(), true);
            }
            if($category->getData('level') == 3){ $html .= '</ul></li>'; }
        }
        
        return $html;
    }
}