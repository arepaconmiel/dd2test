<?php
namespace Qxd\ExportData\Controller\Adminhtml\Description;

use Magento\Backend\App\Action\Context;
use Magento\ImportExport\Model\Export\Adapter\Csv;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection;
use Magento\CatalogInventory\Api\StockStateInterface;

use Zend\Log\Logger;


 
class Index extends \Magento\Backend\App\Action
{   

    protected $csv;
    protected $_productCollectionFactory;
    protected $_category;
    protected $_eavConfig;
    protected $_attributeSet;
    protected $_configurableProduct;
    protected $_stockInterface;

    //specific values
    protected $brands;
    protected $attributesSetArray;
    protected $logger;
    protected $currentDate = NULL; //$currentDate=date("Y-m-d h:i:s");


    public function __construct(Context $context, Csv $csv, CollectionFactory $productCollectionFactory, Category $category, Config $eavConfig, Collection $attributeSet, Configurable $configurableProduct, Logger $logger, StockStateInterface $stockInterface)
    {
        parent::__construct($context);
        $this->_csv = $csv;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_category = $category;
        $this->_configurableProduct = $configurableProduct;

        $this->_eavConfig = $eavConfig;
        $this->_attributeSet = $attributeSet;
        $this->_attributeSet = $attributeSet;
        $this->_stockInterface = $stockInterface;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_exporter.log');
        $this->logger = $logger;
        $this->logger->addWriter($writer);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $csv = "";

        $this->_csv->setHeaderCols(["Id","Sku","Product Sku","Name",'Type','Season Code','Attribute_Set','Price','Brand','Visibility','Category','Qty on hand','$ on hand',"special order","colors in stock"]);

        $products = $this->getSimpleCollection();
        $data = $this->writeToFile($products);
        $csv .= $data;

        $products = $this->getSpecialCollection();
        $data = $this->writeToFile($products);
        $csv .= $data;

        //$this->_prepareDownloadResponse('incompleteDescriptionProducts.csv', $csv);
    }

 
    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Qxd_ExportData::description');
    }


    protected function getSpecialCollection()//($field, $value)
    {
        //$collection = $this->_productCollection;//->load();
        //$collection->addFilter('type_id', 'configurable');
        //$collection->addFilter('status', '1');

        $collection = $this->_productCollectionFactory->create()
                    ->addAttributeToSelect(array('name','manufacturer','product_sku','season_code', 'description', 'price','special_price','special_from_date','special_to_date','visibility'))
                    ->addAttributeToFilter('status', array('eq' => 1))
                    ->addAttributeToFilter('type_id', array('in' => array('configurable', 'bundle')));

        return $collection;
    }

    protected function getSimpleCollection() {
        $collection = $this->_productCollectionFactory->create()
            ->addAttributeToFilter('status', array('eq' => 1))
            ->addAttributeToFilter('type_id', array('in' => array('simple', 'virtual')))
            ->addAttributeToSelect(array('name','manufacturer', 'description','product_sku','season_code', 'price','special_price','special_from_date','special_to_date','visibility'));
            //->setOrder('entity_id');

        $collection->getSelect()->joinLeft(array('link_table' => $collection->getResource()->getTable('catalog_product_super_link')),
            'link_table.product_id = e.entity_id',
            array('product_id')
        );
        $collection->getSelect()->where('link_table.product_id IS NULL');


        $product_ids = $collection->getAllIds();

        //$this->stockCollection = Mage::getModel('cataloginventory/stock_item')->getCollection()->addFieldToFilter('product_id', array('in' => $product_ids));

        return $collection;
    }


    private function _getCategory($_product, $category = '') {
        $categoryIds = $_product->getCategoryIds();//array of product categories
        if(!empty($categoryIds)) {
            $categoryData = $this->_category->load($categoryIds[0]);
            $category = $categoryData->getName();
        }
        return $category;
    }

    private function _getSpecialOrder($_product){
        $special_order = $_product->getData('special_order');
        if(!$special_order)
            $special_order=0;
        return $special_order;
    }

    private function _getVisibility($_product){
        $visibility="Catalog Search";
        if($_product->getVisibility() == 1)
            $visibility='Not Visible Individually';
        return $visibility;
    }

    private function _getBrands() {
        if(!$this->brands){
            //var_dump('brands');
            //$this->attributesSetArray = Mage::getResourceModel('eav/entity_attribute_set_collection')->load();

            //$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'manufacturer');

            //foreach ($attribute->getSource()->getAllOptions(true, true) as $instance) {
            //    $this->brands[$instance['value']] = $instance['label'];
            //}


            $attribute = $this->_eavConfig->getAttribute('catalog_product', 'manufacturer');
            $options = $attribute->getSource()->getAllOptions(true,true);

            foreach ($options as $instance) {
                $this->brands[$instance['value']] = $instance['label'];
            }
        }
        return $this->brands;
    }


    private function _getManufacturer($_product, $manufacturer = '') {
        $this->brands = $this->_getBrands();
        if(isset($this->brands[$_product->getManufacturer()]))
            $manufacturer=$this->brands[$_product->getManufacturer()];
        return $manufacturer;
    }

    private function _getAttributeSet($_product, $attribute_set = '') {
        if(!$this->attributesSetArray){
            $this->attributesSetArray = $this->_attributeSet->load();
        }

        foreach($this->attributesSetArray as $attSetData) {
            if($attSetData->getData('attribute_set_id') == $_product->getAttributeSetId())
                $attribute_set = $attSetData->getData('attribute_set_name');
        }
        return $attribute_set;
    }

    private function _getPrice($product,$current)
    {
        $result=$product->getPrice();
        $flagPrice=false;


        $from=$product->getData('special_from_date');
        $to=$product->getData('special_to_date');

        if($product->getSpecialPrice())
        {
            if(empty($from) && empty($to)) {  $flagPrice=true; }
            if(!empty($from) && !empty($to)) { if($current >= $from && $current <= $to){ $flagPrice=true; } }
            if(empty($from) && !empty($to)){ if($current <= $to){ $flagPrice=true; } }
            if(!empty($from) && empty($to)) { if($current >= $from){ $flagPrice=true; } }
        }

        if($flagPrice) { $result=$product->getSpecialPrice(); }

        return $result;
    }


    private function _getConfigData($_product){

        $parentId = $_product->getId();
        $colorsInStock = array();

        //$childrenIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getChildrenIds($parentId);
        $childrenIds = $this->_configurableProduct->getChildrenIds($parentId);


        $price = $qty = $money = 0;
        if(empty($childrenIds['0'])){
            //Mage::log('Error on product: '. $parentId, null, 'exporter.log');
            $logger->info('Error on product: '. $parentId);
            return array('price' => $price, 'qty' => $qty, 'money' => $money);
        }

        //$childrenCollection = Mage::getModel('catalog/product')->getCollection()
            //->addIdFilter($childrenIds)
            //->addAttributeToSelect(array('price','special_price','special_from_date','special_to_date','color'));

        $childrenCollection = $this->_productCollectionFactory->create()
                    ->addAttributeToSelect(array('price','special_price','special_from_date','special_to_date','color','qty'))
                    ->addIdFilter($childrenIds);
                    //->addAttributeToFilter('type_id', 'configurable'); 


       foreach ($childrenCollection as $simpleProduct) {
            
            //$simpleQty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($simpleProduct)->getQty();
            $simpleQty = $this->_stockInterface->getStockQty($simpleProduct->getId(), $simpleProduct->getStore()->getWebsiteId());
            $qty = $qty + $simpleQty;
        

            if ($simpleQty > 0) {
                $colorsInStock[$simpleProduct->getAttributeText('color')] = $simpleProduct->getAttributeText('color'); 
            }

            $simplePrice = $this->_getPrice($simpleProduct,$this->currentDate);
            ////var_dump($simpleProduct->getEntityId());
            ////var_dump($simplePrice);
            if($simplePrice > $price)
                $price=$simplePrice;

            $money=$money+($simpleQty*$simplePrice);
        }

        $colors = "";
        foreach ($colorsInStock as $color) {
            $colors = $colors . $color . "|";
        }
        //var_dump(array('price' => $price, 'qty' => $qty, 'money' => $money));
        return array('price' => $price, 'qty' => $qty, 'money' => $money, 'colors' => $colors);
    }

    private function _getBundleData($_product) {
        $childProducts = $_product->getTypeInstance(true)
            ->getSelectionsCollection(
                $_product->getTypeInstance(true)->getOptionsIds($_product),
                $_product
            );

        $price = $qty = $money=0;

        foreach ($childProducts as $childProduct) {
            //var_dump($childProduct->getTypeId());

            if($childProduct->getTypeId() == 'simple' || $childProduct->getTypeId() == 'virtual') {
                //$simpleQty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                $simpleQty = $this->_stockInterface->getStockQty($childProduct->getId(), $childProduct->getStore()->getWebsiteId());
                $qty = $qty + $simpleQty;

                $simplePrice = $this->_getPrice($childProduct,$this->currentDate);
                ////var_dump($childProduct->getEntityId());
                ////var_dump($simplePrice);
                if($simplePrice > $price)
                    $price = $simplePrice;

                $money = $money + ($simpleQty*$simplePrice);
            }

            if($childProduct->getTypeId() == 'configurable') {
                $productInfo = $this->_getConfigData($childProduct);
                $money = $money + $productInfo['money'];
                $qty = $qty + $productInfo['qty'];

                if($productInfo['price'] > $price)
                    $price = $productInfo['price'];
            }
        }
        return array('price' => $price, 'qty' => $qty, 'money' => $money, 'colors' => "");
    }

    private function _getSimpleData($_product) {
        $price = $qty = $money = 0;
        /*foreach($this->stockCollection as $stock) {
            if($stock->getData('product_id') == $_product->getId()) {
                $qty = $stock->getData('qty');
            }
        }*/ //check this implementation
        $qty = $this->_stockInterface->getStockQty($_product->getId(), $_product->getStore()->getWebsiteId());
        $money = $qty * $_product->getPrice();
        $price = $this->_getPrice($_product, $this->currentDate);

        return array('price' => $price, 'qty' => $qty, 'money' => $money, 'colors' => "");
    }

    protected function getProductInfoDesc($_product) {
        //$websiteIDS = $_product->getWebsiteIds();
        //if(strlen($_product->getDescription()) < 10 && in_array(1, $websiteIDS)) {


            $category = $this->_getCategory($_product);
            $special_order = $this->_getSpecialOrder($_product);
            $visibility = $this->_getVisibility($_product);
            $manufacturer = $this->_getManufacturer($_product);
            $attribute_set = $this->_getAttributeSet($_product);
            
        
            switch($_product->getTypeId()) {
                case 'configurable': {
                    $product_info = $this->_getConfigData($_product);
                }
                    break;
                case 'bundle':{
                    $product_info = $this->_getBundleData($_product);
                }
                    break;
                case 'simple':{
                    $product_info = $this->_getSimpleData($_product);
                }
                    break;
                case 'virtual':{
                    $product_info = $this->_getSimpleData($_product);
                }
                    break;
            }

            $row = array(
                    'id'            => $_product->getId(),
                    'sku'           => $_product->getSku(),
                    'product_sku'   => $_product->getProductSku(),
                    'name'          => $_product->getName(),
                    'type'          => $_product->getTypeId(),
                    'season_code'   => $_product->getSeasonCode(),
                    'attribute_set' => $attribute_set,
                    'price'         => $product_info['price'],
                    'brand'         => $manufacturer,
                    'visibility'    => $visibility,
                    'category'      => $category,
                    'qty'           => $product_info['qty'],
                    'money'         => $product_info['money'],
                    'special_order' => $special_order,
                    'colors'        => $product_info['colors']
                );

            return $row;
        //}//endif websites
    }

    protected function writeToFile($collection) {

        $collection->setPageSize('150');//Add a page size to the result set.
        $pages = $collection->getLastPageNumber();//discover how many page the result will be.

        $currentPage = 1;
        $csv = "";
        $i=0;

        do{
            $collection->setCurPage($currentPage);//Tell the products which page to load.
            $collection->load();
            foreach ($collection as $product){

                $info = $this->getProductInfoDesc($product);
                $csv .= implode(',', $info)."\n";
                $this->_csv->writeRow(['Id'             => $info['id'],
                                       'Sku'            => $info['sku'],
                                       'Product Sku'    => $info['product_sku'],
                                       'Name'           => $info['name'],
                                       'Type'           => $info['type'],
                                       'Season Code'    => $info['season_code'],
                                       'Attribute_Set'  => $info['attribute_set'],
                                       'Price'          => $info['price'],
                                       'Brand'          => $info['brand'],
                                       'Visibility'     => $info['visibility'],
                                       'Category'       => $info['category'],
                                       'Qty on hand'    => $info['qty'],
                                       '$ on hand'      => $info['money'],
                                       'special order'  => $info['special_order'],
                                       'colors in stock'=> $info['colors'],
                                    ]);

                /*if(empty($productArray)) continue;

                if(is_array($productArray[0])){
                    foreach ($productArray as $productInfo) {
                        $data = array();
                        fputcsv($fp, $productInfo);

                        //add to csv
                        foreach($productInfo as $row){  $data[] = '"'.$row.'"'; }
                        $csv .= implode(',', $data)."\n";
                    }
                }else{
                    fputcsv($fp, $productArray);

                    //add to csv
                    foreach($productArray as $row){  $data[] = '"'.$row.'"'; }
                    $csv .= implode(',', $data)."\n";
                }*/
            }

            $currentPage++;
            $collection->clear();//make the products unload the data in memory so it will pick up the next page when load() is called.
            if($i==1000) break;
            $i++;
        } while ($currentPage <= $pages);

        /*foreach ($collection as $product) {
            
            $info = $this->getProductInfoDesc($product);
            $csv .= implode(',', $info)."\n";
            $this->_csv->writeRow(['Id'=>$product->getId(), 
                                       'Sku' => $product->getSku(), 
                                       'Product Sku' => '', 
                                       'Name' => $product->getName(),
                                       'Type' => $product->getTypeId(),
                                       'Season Code' => '',
                                       'Attribute_Set' => '',
                                       'Price' => $product->getFinalPrice(),
                                       'Brand' => '',
                                       'Visibility' => $product->getVisibility(),
                                       'Category' => '',
                                       'Qty on hand' => '',
                                       '$ on hand' => '',
                                       'special order' => '',
                                       'colors in stock' => '',
                                    ]);
        }*/
        return $csv;
    }

}
