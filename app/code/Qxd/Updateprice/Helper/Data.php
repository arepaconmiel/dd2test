<?php
namespace Qxd\Updateprice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{   
    public function __construct(
        \Magento\Catalog\Model\Product $product,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProduct,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface $categoryLinkRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
    ) {
        $this->_product = $product;
        $this->_configurableProduct = $configurableProduct; 
        $this->_resource = $resource;
        $this->_categoryLinkManagement = $categoryLinkManagement;
        $this->_categoryLinkRepository = $categoryLinkRepository;
        $this->_customerSession = $customerSession;
        $this->_stockRegistryInterface = $stockRegistryInterface;
    }

    
    public function displayRange($product)
    {
        $result=array();
        $flagClearResult=true;
        $simpleProducts= array();

        switch($product->getTypeId())
        {
            case 'configurable': {
                $simpleProducts = $product->getTypeInstance()->getUsedProducts($product);
            }break;
            case 'bundle': { $simpleProducts = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds($product), $product); }break;
        }

        //$bla = '';
        $bla = [];
        foreach($simpleProducts as $simpleProduct)
        {   

            $inventory=$this->_stockRegistryInterface->getStockItem($simpleProduct->getId());
  
            if($inventory->getQty() > 0)
            {   

                if($this->validateSpecialPrice($simpleProduct))
                {   
                    $flagClearResult=false;
                    if(!in_array($simpleProduct->getData('special_price'), $result)){ 
                        $result[]=$simpleProduct->getData('special_price'); 
                    }
                }else{ 
                    if(!in_array($simpleProduct->getData('price'), $result)){ 
                        $result[]=$simpleProduct->getData('price'); 
                    } 
                }
            }
        }

        if($flagClearResult){ $result=array(); }
        sort($result);

        return $result;
    }

    public function displayChildrenRegularPrice($product,$regularPrice)
    {
        $result=array();
        if($regularPrice){  $result[]=$regularPrice;  }
        $flagClearResult=true;

        switch($product->getTypeId())
        {
            case 'configurable': {
                $configurable= Mage::getModel('catalog/product_type_configurable')->setProduct($product);
                $simpleProducts = $configurable->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
            }break;
            case 'bundle': { $simpleProducts = $product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product); }break;
        }
        foreach($simpleProducts as $simpleProduct)
        {
            $inventory=Mage::getModel('cataloginventory/stock_item')->loadByProduct($simpleProduct);
            if($inventory->getQty() > 0)
            {
                $flagClearResult=false;
                if(!in_array($simpleProduct->getData('price'), $result)){ $result[]=$simpleProduct->getData('price'); }
            }
        }

        if($flagClearResult){ $result=array(); }
        sort($result);

        return $result;
    }

    public function getPrices($product)
    {
        $result=array();

        switch($product->getTypeId())
        {
            case 'configurable': {
                $attributes=$product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                $attributesArray=array();

                foreach ($attributes as $productAttribute) { $attributesArray[] = $productAttribute['attribute_code']; }

                $configurable= Mage::getModel('catalog/product_type_configurable')->setProduct($product);
                $simpleProducts = $configurable->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();

                foreach($simpleProducts as $simpleProduct)
                {
                    $colorID='';
                    $sizeID='';
                    $inventory=Mage::getModel('cataloginventory/stock_item')->loadByProduct($simpleProduct);
                    if($inventory->getQty() > 0)
                    {

                        foreach($attributesArray as $attribute)
                        {
                            if($attribute == 'color'){ $colorID=$simpleProduct->getData($attribute); }
                            if($attribute == 'size'){ $sizeID=$simpleProduct->getData($attribute); }
                        }
                        if($this->validateSpecialPrice($simpleProduct)) { $result[]=array('color_id'=>$colorID,'size_id'=>$sizeID,'price'=>$simpleProduct->getData('special_price')); }
                        else{ $result[]=array('color_id'=>$colorID,'size_id'=>$sizeID,'price'=>$simpleProduct->getData('price')); }
                    }
                }
            }break;
        }

        return json_encode($result);
    }

    public function validateSpecialPrice($product)
    {
        $result=false;
        $special_price=$product->getData('special_price');
        $from=$product->getData('special_from_date');
        $to=$product->getData('special_to_date');
        $current=date("Y-m-d h:i:s");

        if(!empty($special_price))
        {
            if(empty($from) && empty($to)) {  $result=true; }
            if(!empty($from) && !empty($to)) { if($current >= $from && $current <= $to){ $result=true; } }
            if(empty($from) && !empty($to)){ if($current <= $to){ $result=true; } }
            if(!empty($from) && empty($to)) { if($current >= $from){ $result=true; } }
        }

        return $result;
    }

    public function displayRangeRegular($product)
    {
        $result=array();
        $simpleProducts = array();

        switch($product->getTypeId())
        {
            case 'configurable': {
                $simpleProducts = $product->getTypeInstance()->getUsedProducts($product);
            }break;
            case 'bundle': { $simpleProducts = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds($product), $product); }break;
        }

        foreach($simpleProducts as $simpleProduct)
        {
            $inventory=$this->_stockRegistryInterface->getStockItem($simpleProduct->getId());
            if($inventory->getQty() > 0)
            {
                if(!in_array($simpleProduct->getPrice(), $result)){ $result[]=$simpleProduct->getPrice(); }
            }
        }
        sort($result);

        return $result;
    }

    public function onlyConfigurableHasSpecialPrice($product)
    {
        $result=true;

        switch($product->getTypeId())
        {
            case 'configurable': {
                $configurable= Mage::getModel('catalog/product_type_configurable')->setProduct($product);
                $simpleProducts = $configurable->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
            }break;
            case 'bundle': { $simpleProducts = $product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product); }break;
            case 'simple': { $result=false; }break;
            case 'virtual': { $result=false; }break;
        }

        foreach($simpleProducts as $simpleProduct)
        {
            $inventory=Mage::getModel('cataloginventory/stock_item')->loadByProduct($simpleProduct);
            if($inventory->getQty() > 0) { if($simpleProduct->getSpecialPrice()){ $result=false; } }
        }

        return $result;
    }
    
}
