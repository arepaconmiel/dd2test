<?php
namespace Qxd\Onsale\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{   
    public function __construct(
        \Magento\Catalog\Model\Product $product,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface $categoryLinkRepository,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_product = $product;
        $this->_configurableProduct = $configurableProduct; 
        $this->_resource = $resource;
        $this->_categoryLinkManagement = $categoryLinkManagement;
        $this->_categoryLinkRepository = $categoryLinkRepository;
        $this->_customerSession = $customerSession;
    }

    /*public function getOnSaleUrl()
    {
        $url = Mage::getUrl('', array(
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => array(
                'sale' => 1,
                'p' => NULL
            )
        ));

        return $url;
    }

    public function getNotOnSaleUrl()
    {
        $url = Mage::getUrl('', array(
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => array(
                'sale' => NULL,
                'p' => NULL
            )
        ));

        return $url;
    }*/

    public function processProduct($product,$currentDateTime,$saleCategory)
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sale_category.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        if($saleCategory)
        {
            $type=$product->getTypeId();
            $visibility=$product->getVisibility();
            $productId=$product->getId();
            $productSku = $product->getSku();

            $saleCategoryId=$saleCategory->getId();
            $categories = $product->getCategoryIds();

            $add=false;
            $parentId="";
            $parentSku="";
            $finalProduct = $product;

            $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

            if($type == 'configurable')
            {
                $simpleProducts = $this->_configurableProduct->getUsedProductCollection($product)->addAttributeToSelect('*')->addFilterByRequiredOptions();

                foreach($simpleProducts as $simpleProduct)
                { if($this->verifySpecialPrice($simpleProduct,$type,$currentDateTime)){ $add=true; } }
            }
            if($type == 'bundle') { $add=$this->verifySpecialPrice($product,$type,$currentDateTime); }
            if($type == 'simple' || $type == 'giftvoucher')
            {
                if($visibility == 4){ $add=$this->verifySpecialPrice($product,$type,$currentDateTime); }
                else{
                    $querySelectParent="SELECT parent_id FROM  `catalog_product_super_link` WHERE  `product_id` =".$productId;
                    $parentId = $connection->fetchOne($querySelectParent);

                    $logger->info('aca1');
                    $logger->info(print_r($parentId, true));

                    if($parentId)
                    {
                        $productId=$parentId;
                        $parentProduct = $this->_product->load($parentId);
                        $finalProduct = $parentProduct;
                        $productSku = $parentProduct->getSku();
                        $categories = $parentProduct->getCategoryIds();

                        $simpleProducts = $this->_configurableProduct->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();

                        foreach($simpleProducts as $simpleProduct)
                        { if($this->verifySpecialPrice($simpleProduct,$type,$currentDateTime)){ $add=true; } }
                    }else{ $add=false; }
                }
            }

            if($add)
            {   
                if(!in_array($saleCategoryId, $categories)){
                    $categories[] = $saleCategoryId;
                    //$finalProduct->setCategoryIds($categories);
                    $this->_categoryLinkManagement->assignProductToCategories($productSku,$categories);
                }
            }
            else
            { 
                if(in_array($saleCategoryId, $categories)){ 
                    $this->_categoryLinkRepository->deleteByIds($saleCategoryId ,$productSku);
                } 
            }
        }
    }

    public function verifySpecialPrice($product,$productType,$currentDateTime)
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sale_category.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $result=false;

        $productFrom=$product->getData('special_from_date');
        $productTo=$product->getData('special_to_date');

        $inStock=false;

        if($product->getExtensionAttributes()->getStockItem()) { 
            $logger->info(print_r('entre',true));
            $inStock = $product->getExtensionAttributes()->getStockItem()->getIsInStock(); 
        }

        if($inStock && $product->getData('special_price') && ( (($product->getData('special_price') < $product->getData('price')) && $productType!='bundle') || $productType=='bundle' ))
        {
            if(empty($productFrom) && empty($productTo)){  $result=true; }
            if(!empty($productFrom) && !empty($productTo)){ if($currentDateTime >= $productFrom && $currentDateTime <= $productTo){ $result=true; } }
            if(empty($productFrom) && !empty($productTo)){ if($currentDateTime <= $productTo){ $result=true; } }
            if(!empty($productFrom) && empty($productTo)){ if($currentDateTime >= $productFrom){ $result=true; } }
        }
        return $result;
    }
    
}
