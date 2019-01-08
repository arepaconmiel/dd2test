<?php

namespace Qxd\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

require_once(str_replace('app/code/Qxd/Shell/Console/Command','',realpath(dirname(__FILE__)))."magmi/inc/magmi_defs.php");
require_once(str_replace('app/code/Qxd/Shell/Console/Command','',realpath(dirname(__FILE__)))."magmi/integration/inc/magmi_datapump.php");

class TestLogger
{
    protected $errors=array();
    public function log($data, $type){ $this->errors[]="$type:$data"; }
    public function getErrors()
    { return $this->errors; }

}

class ImportProductsQxd extends Command
{   

    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Eav\Model\Config $eavConfig,
        \Qxd\Onsale\Helper\DataFactory $onsaleHelper,
        \Qxd\Memcached\Helper\DataFactory $memcached,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\App\State $state
    )
    {     

        $this->_resource = $resource;
        $this->_directoryList = $directoryList;
        $this->_storeManager     = $storeManager;
        $this->_productCollection  = $productCollection;
        $this->_eavConfig  = $eavConfig;
        $this->_attributeSetCollection = $attributeSetCollection;
        $this->_category = $categoryFactory;
        $this->_onsaleHelper = $onsaleHelper;
        $this->_memcached = $memcached;
        $this->_state = $state;

        parent::__construct();


        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_ImportIProducts-Error.log');
        $this->logger = new \Zend\Log\Logger();
        $this->logger->addWriter($writer);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('qxd:ImportProducts_qxd')->setDescription("Import Products");
    }

    /**
     * {@inheritdoc}
     */
     protected function execute(InputInterface $input, OutputInterface $output)
    {      
        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $output->writeln("Start import products");
       
        try{
            $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

            $output->writeln("generate report before import \n");
            $this->generateReportBeforeImport();
            $output->writeln("report before import generated \n");

            $output->writeln("importing products \n");
            $this->Import($connection,$output);
            $output->writeln("products imported \n");

            $output->writeln("generate report after import \n");
            $this->generateReportAfterImport($connection);
            $output->writeln("report after import generated \n");

        }catch (Exception $e){ 
            $logger->info($e->getMessage().' '.$e->getLine()); 
        }
    }


    public function generateReportBeforeImport()
    {   

        try {
            $fromPath=$this->_directoryList->getRoot()."/feeds/";

            $arrayFileParsed=array();
            if(file_exists($fromPath."dbo_zz_dd_mage_import_table.csv"))
            {
                $fileRead = fopen($fromPath."dbo_zz_dd_mage_import_table.csv","r");

                $headers=fgetcsv($fileRead);

                while(! feof($fileRead))
                {
                    $rowData=array();
                    $rowAUX=fgetcsv($fileRead);

                    if($rowAUX[3])
                    {
                        $arrayFileParsed[]=$rowAUX[3];
                    }
                }
                fclose($fileRead);
            }

            $file = fopen($this->_directoryList->getPath('var') ."/QXD_export/BeforeImportReport.csv","w");
            $headers=array("Sku","Name",'Type','Visibility','Status');
            fputcsv($file,$headers);

            $index=0;

            if(!empty($arrayFileParsed))
            {
                $products = $this->_productCollection->create()->addAttributeToSelect('*')
                ->addFieldToFilter('sku', array('in' => $arrayFileParsed));


                $productsCount=count($products);
                foreach($products as $product)
                {
                    $visibility='Not Visible Individually';
                    $status='Enabled';

                    if($product->getStatus() == 2){ $status='Disabled'; }
                    if($product->getVisibility() == 4){ $visibility='Catalog Search'; }

                    $row=array($product->getSku(),$product->getName(),$product->getTypeId(),$visibility,$status);
                    fputcsv($file,$row);

                    print_r(++$index." of ".$productsCount."\n");
                }
                fclose($file);
            }
        }catch (Exception $e){ print_r($e->getMessage()); }
    }

    public function Import($connection,$output)
    {   
        try{
            $fromPath=$this->_directoryList->getRoot()."/feeds/";
            $toPath=$this->_directoryList->getPath('var')."/QXD_import/Products_Processed/";

            $dp=\Magmi_DataPumpFactory::getDataPumpInstance("productimport");

            //$fpc_api = Mage::helper('ewpagecache/api');

            $colors=$this->returnOptions('color');
            $sizes=$this->returnOptions('size');
            $genders=$this->returnOptions('gender');
            $brands=$this->returnOptions('manufacturer');

            /*$this->logger->info(print_r($colors, true));
            $this->logger->info(print_r($sizes, true));
            $this->logger->info(print_r($genders, true));
            $this->logger->info(print_r($brands, true));*/    

            $attributeSetCollection = $this->_attributeSetCollection->create();

            $attributesSetArray = $attributeSetCollection->getItems();

            $arrayFileParsed=array();

            if(file_exists($fromPath."dbo_zz_dd_mage_import_table.csv"))
            {
                $file = fopen($fromPath."dbo_zz_dd_mage_import_table.csv","r");

                $headers=$rowAUX=fgetcsv($file);

                while(! feof($file))
                {
                    $rowData=array();
                    $rowAUX=fgetcsv($file);

                    if($rowAUX[3])
                    {
                        $rowData['store']='admin';
                        $rowData['attribute_set']=$rowAUX[1];
                        $rowData['type']=$rowAUX[2];
                        $rowData['sku']=$rowAUX[3];
                        $rowData['product_sku']=$rowAUX[4];
                        $rowData['name']=$rowAUX[6];
                        $rowData['manufacturer']=$this->getRealOptionValue($brands,$rowAUX[7]);
                        $rowData['price']=$rowAUX[8];
                        if($rowAUX[8] != $rowAUX[9]){ $rowData['special_price']=$rowAUX[9]; }
                        else{  $rowData['special_price']=''; }
                        $rowData['special_from_date']=$rowAUX[10];
                        $rowData['special_to_date']=$rowAUX[11];
                        $rowData['weight']=$rowAUX[12];
                        $rowData['color']=$this->getRealOptionValue($colors,$rowAUX[13]);
                        $rowData['size']=$this->getRealOptionValue($sizes,$rowAUX[14]);
                        $rowData['tax_class_id']=$rowAUX[15];
                        $rowData['free_freight']=$rowAUX[16];
                        $rowData['visibility']=$rowAUX[17];
                        $rowData['status']=$rowAUX[18];
                        if(isset($genders[$rowAUX[20]])){ $rowData['gender']=$genders[$rowAUX[20]]; }
                        $rowData['season_code']=$rowAUX[21];
                        $rowData['upccode']=$rowAUX[22];
                        $rowData['special_order']=$rowAUX[23];
                        if($rowAUX[24] != 0){ $rowData['category_ids']=$rowAUX[24]; }
                        $rowData['configurable_attributes']=$rowAUX[25];
                        $rowData['simples_skus']=$rowAUX[26];
                        $rowData['reward_points']=$rowAUX[9];

                        $arrayFileParsed[$rowData['sku']]=$rowData;
                    }
                }
                fclose($file);
            }

            if(!empty($arrayFileParsed))
            {
                $tl=new TestLogger();
                $dp->beginImportSession("default", "create",$tl);

                $configurableSKUS=array();
                $countProductsProcessed=0;
                $totalCount=count($arrayFileParsed);
                $arrayProductsImported=array();
                $arrayProductsNotImported=array();

                $productSku=array_keys($arrayFileParsed);

                $magentoCollection = $this->_productCollection->create()->addAttributeToSelect('*')
                ->addFieldToFilter('sku', array('in' => $productSku));

                
                uasort($arrayFileParsed, function ($a, $b) { 
                    return strcmp($a['type'], $b['type']); 
                });

                $sortType=array_reverse($arrayFileParsed);

                $updateMultiStock=array();
                $clearCacheArray=array();

                foreach($sortType as $productData)
                {
                    try
                    {
                        $freeFreight=$productData['free_freight'];
                        $productData=$this->filterProductData($productData);
                        $productData=$this->filterExistedProduct($magentoCollection,$productData,$connection,$attributesSetArray);

                        $productData=array_filter($productData);

                        if($freeFreight == 0) { $productData['free_freight']=0; }

                        if(!isset($productData['special_price']))
                        {
                            $productData['special_price']="";
                            $productData['reward_points']=$productData['price'];
                        }

                        //print_r($productData);
                        $this->logger->info(print_r($productData, true));

                        $importResult=$dp->ingest($productData);

                        if( isset($productData['type']) && $productData['type']== 'simple') { $updateMultiStock[]=$productData['sku']; }
                        if(!isset($productData['name'])){ $clearCacheArray[]=$productData['sku']; }

                        if($importResult['ok'])
                        {
                            if( isset($productData['type']) && $productData['type']== 'configurable') { $configurableSKUS[]=$productData['sku']; }
                            $arrayProductsImported[]=$productData['sku'];
                        }
                        else{ $arrayProductsNotImported[]=$productData['sku']; }

                        echo("\r".++$countProductsProcessed.' of '.$totalCount);

                    }catch (Exception $e){ 
                        $this->logger->info($e->getMessage().' '.$e->getLine());
                    }
                }
                $this->logger->info(print_r("importnados con exito", true));
                $this->logger->info(print_r($arrayProductsImported, true));
                $this->logger->info(print_r("No importados", true));
                $this->logger->info(print_r($arrayProductsNotImported, true));

                //create rows for multistock
                print_r("\n configure multistock");

                $multiStockCollection=$this->_productCollection->create()->addAttributeToSelect('*')
                ->addFieldToFilter('sku', array('in' => $updateMultiStock));
                foreach($multiStockCollection as $productData) { $this->updateMultistock($productData->getId(),$connection); }

                print_r("\n multistock configured");

                //remove old parents
                // $this->unsetOldParentForSimpleProduct($configurableSKUS,$readConnection,$writeConnection);

                //add-remove in Sales Category
                print_r("\n add-remove in Sales Category");

                $currentDateTime = date('Y-m-d H:i:s');

                $saleCategory = $this->_category->create()->getCollection()->addAttributeToFilter('url_key','sale')->getFirstItem();

                $checkOnSalesCategory=$this->_productCollection->create()->addAttributeToSelect('*')
                ->addFieldToFilter('sku', array('in' => $arrayProductsImported));

                $this->logger->info('on sale section');
                foreach($checkOnSalesCategory as $productToSale) { 
                    $this->logger->info(print_r($productToSale->getSku(), true));
                    $this->_onsaleHelper->create()->processProduct($productToSale,$currentDateTime,$saleCategory); 
                }

                print_r("\n Sales Category updated");

                //Clear cache
                print_r("\n clearing cache for products");

                $clearCacheCollection=$this->_productCollection->create()->addAttributeToSelect('*')
                    ->addFieldToFilter('sku', array('in' => $clearCacheArray))
                    ->addAttributeToFilter('visibility', '4')
                    ->addAttributeToFilter('status', '1')
                    ->getAllIds();

                print_r("\n clearing memcached for products");

                $_memcached=$this->_memcached->create(['data' => ['flag_code_area' => true]])->initMemcached();

                if($_memcached)
                {
                    foreach($clearCacheCollection as $memP)
                    {
                        $_memcachedKeys=$_memcached->getMulti(array($memP.'_QA','cart_item_'.$memP,$memP.'_media',$memP.'_reviewsContent',$memP.'_reviewSummary',$memP.'_description',$memP.'_attributes',$memP.'_scrollOptions','cart_item_'.$memP));
                        foreach($_memcachedKeys as $k=>$data) { if($data){ $_memcached->delete($k); } }
                    }
                }

                print_r("\n memcached for products cleared");

                // here clear regular cache
                /*$productTags=$fpc_api->getTagsFromProductIds($clearCacheCollection);
                $flushed = $fpc_api->flushPagesMatchingAnyTag($productTags);*/

                print_r("\n cache for products cleared");

                print_r("\n clearing cache for categories");

                /*$categories=$this->getProductCategoriesToClear($readConnection,$clearCacheCollection);
                $categoryTags=$fpc_api->getTagsFromCategoryIds($categories);
                $flushed = $fpc_api->flushPagesMatchingAnyTag($categoryTags);*/

                print_r("\n cache for categories cleared");

                $dp->endImportSession();

                $estTZ=new \DateTime("now", new \DateTimeZone('US/Eastern'));
                rename($fromPath."dbo_zz_dd_mage_import_table.csv", $toPath.$estTZ->format('m-d-Y h:i:s')."_productsImported.csv");

                $emailBody="Products Imported ".count($arrayProductsImported)."\n\n";
                $emailBody=$emailBody.implode("\n",$arrayProductsImported);
                $emailBody=$emailBody."\n\n";

                $emailBody=$emailBody."Products Not Imported ".count($arrayProductsNotImported)."\n\n";
                $emailBody=$emailBody.implode("\n",$arrayProductsNotImported);
                $emailBody=$emailBody."\n\n";

                $emailBody=$emailBody."Errors Reported\n";
                $emailBody=$emailBody.implode("\n",$tl->getErrors());
                $this->sendEmail($emailBody);

            }else{
                $estTZ=new \DateTime("now", new \DateTimeZone('US/Eastern'));
                $emailBody="No products found at ".$estTZ->format('m-d-Y h:i:s')."\n\n";
                $this->sendEmail($emailBody);
                print_r("\n No csv :O");
            }

        }catch (Exception $e){ 
            $this->logger->info($e->getMessage().' '.$e->getLine()); 
        }
    }
    public function generateReportAfterImport($connection)
    {   
        try {
            $fromPath =$this->_directoryList->getPath('var') . "/QXD_export/";

            $arrayFileParsed=array();
            if(file_exists($fromPath."BeforeImportReport.csv"))
            {
                $fileRead = fopen($fromPath."BeforeImportReport.csv","r");

                $headers=fgetcsv($fileRead);

                while(! feof($fileRead))
                {
                    $rowData=array();
                    $rowAUX=fgetcsv($fileRead);

                    if($rowAUX[0])
                    {
                        $arrayFileParsed[$rowAUX[0]]=array('name'=>$rowAUX[1],'type'=>$rowAUX[2],'visibility'=>$rowAUX[3],'status'=>$rowAUX[4]);
                    }
                }
                fclose($fileRead);
            }

            $file = fopen($this->_directoryList->getPath('var') ."/QXD_export/AfterImportReport.csv","w");
            $headers=array("Sku","Name",'Type','Visibility','Status');
            fputcsv($file,$headers);

            $index=0;

            if(!empty($arrayFileParsed))
            {
                $products=$this->_productCollection->create()->addAttributeToSelect('*')
                    ->addFieldToFilter('sku', array('in' => array_keys($arrayFileParsed)));

                $productsCount=count($products);
                foreach($products as $product)
                {
                    $correctFlag=true;
                    $visibility='Not Visible Individually';
                    $status='Enabled';

                    if($product->getStatus() == 2){ $status='Disabled'; }
                    if($product->getVisibility() == 4){ $visibility='Catalog Search'; }

                    if(isset($arrayFileParsed[$product->getSku()]))
                    {
                        $dataCompare=$arrayFileParsed[$product->getSku()];
                        if($product->getName() != $dataCompare['name']){ $correctFlag=false; }
                        if($visibility != $dataCompare['visibility']){ $correctFlag=false; }
                        if($status != $dataCompare['status']){ $correctFlag=false; }

                        if($product->getTypeId() == 'simple')
                        {
                            $querySelect = 'SELECT id FROM advancedinventory_item WHERE product_id = ' . (int)$product->getId(). ' LIMIT 1';
                            $id = $connection->fetchOne($querySelect);

                            $querySelect2 = 'SELECT item_id FROM cataloginventory_stock_item WHERE product_id = ' . (int)$product->getId(). ' LIMIT 1';
                            $inventoryId = $connection->fetchOne($querySelect2);

                            if(!$id && !$inventoryId){ $correctFlag=false; }
                        }
                    }


                    if(!$correctFlag)
                    {
                        $row=array($product->getSku(),$product->getName(),$product->getTypeId(),$visibility,$status);
                        fputcsv($file,$row);
                    }

                    print_r(++$index." of ".$productsCount."\n");
                }
                fclose($file);
            }
        }catch (Exception $e){ print_r($e->getMessage()); }
    }

    public function updateMultistock($productId,$connection)
    {
        try{
            $querySelect = 'SELECT id FROM advancedinventory_item WHERE product_id = ' . (int)$productId. ' LIMIT 1';
            $id = $connection->fetchOne($querySelect);

            $querySelect2 = 'SELECT item_id FROM cataloginventory_stock_item WHERE product_id = ' . (int)$productId. ' LIMIT 1';
            $inventoryId = $connection->fetchOne($querySelect2);

            if(!$id)
            {
                $queryInsert="INSERT INTO advancedinventory_item (product_id,multistock_enabled) VALUES (".(int)$productId.",1)";
                $connection->query($queryInsert);
                $lastID=$connection->lastInsertId();

                if(!empty($lastID))
                {
                    $queryInsert="INSERT INTO advancedinventory_stock (product_id,item_id,place_id,manage_stock,quantity_in_stock,backorder_allowed,use_config_setting_for_backorders)
                              VALUES (".(int)$productId.",".$lastID.",1,1,0,0,1)";
                    $connection->query($queryInsert);

                    $queryInsert="INSERT INTO advancedinventory_stock (product_id,item_id,place_id,manage_stock,quantity_in_stock,backorder_allowed,use_config_setting_for_backorders)
                              VALUES (".(int)$productId.",".$lastID.",2,1,0,0,1)";
                    $connection->query($queryInsert);

                    $queryInsert="INSERT INTO advancedinventory_stock (product_id,item_id,place_id,manage_stock,quantity_in_stock,backorder_allowed,use_config_setting_for_backorders)
                              VALUES (".(int)$productId.",".$lastID.",3,1,0,0,1)";
                    $connection->query($queryInsert);

                    $queryInsert="INSERT INTO advancedinventory_stock (product_id,item_id,place_id,manage_stock,quantity_in_stock,backorder_allowed,use_config_setting_for_backorders)
                              VALUES (".(int)$productId.",".$lastID.",4,1,0,0,1)";
                    $connection->query($queryInsert);

                    $queryInsert="INSERT INTO advancedinventory_stock (product_id,item_id,place_id,manage_stock,quantity_in_stock,backorder_allowed,use_config_setting_for_backorders)
                              VALUES (".(int)$productId.",".$lastID.",5,1,0,0,1)";
                    $connection->query($queryInsert);

                    $queryInsert="INSERT INTO advancedinventory_stock (product_id,item_id,place_id,manage_stock,quantity_in_stock,backorder_allowed,use_config_setting_for_backorders)
                              VALUES (".(int)$productId.",".$lastID.",6,1,0,0,1)";
                    $connection->query($queryInsert);

                    $queryInsert="INSERT INTO advancedinventory_stock (product_id,item_id,place_id,manage_stock,quantity_in_stock,backorder_allowed,use_config_setting_for_backorders)
                              VALUES (".(int)$productId.",".$lastID.",7,1,0,0,1)";
                    $connection->query($queryInsert);

                    $queryInsert="INSERT INTO advancedinventory_stock (product_id,item_id,place_id,manage_stock,quantity_in_stock,backorder_allowed,use_config_setting_for_backorders)
                              VALUES (".(int)$productId.",".$lastID.",8,1,0,0,1)";
                    $connection->query($queryInsert);
                }
            }

            if(!$inventoryId)
            {
                $queryInsert="INSERT INTO cataloginventory_stock_item (product_id,stock_id,qty,min_qty,use_config_min_qty,is_qty_decimal,backorders,use_config_backorders,min_sale_qty,
                                                                      use_config_min_sale_qty,max_sale_qty,use_config_max_sale_qty,is_in_stock,low_stock_date,notify_stock_qty,
                                                                      use_config_notify_stock_qty,manage_stock,use_config_manage_stock,stock_status_changed_auto,
                                                                      use_config_qty_increments,qty_increments,use_config_enable_qty_inc,enable_qty_increments,is_decimal_divided,website_id)
                              VALUES (".(int)$productId.",1,0,0,1,0,1,0,1,1,0,1,0,NULL,NULL,1,0,1,0,1,0,1,0,0,0)";
                $connection->query($queryInsert);
            }

        }catch (Exception $e){ 
            $this->logger->info($e->getMessage().' '.$e->getLine());
        }
    }
    public function filterProductData($productData)
    {
        switch($productData['type'])
        {
            case 'simple':
            {
                unset($productData['simples_skus']);
                unset($productData['configurable_attributes']);
            }
                break;
            case 'configurable':
            {
                unset($productData['color']);
                unset($productData['size']);
            }
                break;
        }

        return $productData;
    }
    public function filterExistedProduct($productCollection,$productData,$connection,$attributesSetArray)
    {
        $type=$productData['type'];
        $simpleskus="";

        if($type=='configurable'){ $simpleskus=$productData['simples_skus']; }
        $possibleID=$this->existInMagento($productCollection->getData(),$productData['sku'],$type,$simpleskus,$attributesSetArray,$connection);

        if($possibleID)
        {
            unset($productData['attribute_set']);
            unset($productData['name']);
            unset($productData['visibility']);
            unset($productData['status']);

            $querySelect = 'SELECT type_id FROM  `catalog_product_entity` WHERE  `entity_id` =' . (int)$possibleID. ' LIMIT 1';
            $oldType = $connection->fetchOne($querySelect);
            $productData['type']=$oldType;

            $querySelect = 'SELECT sku FROM  `catalog_product_entity` WHERE  `entity_id` =' . (int)$possibleID. ' LIMIT 1';
            $oldSku = $connection->fetchOne($querySelect);
            $productData['sku']=$oldSku;

            $productData['category_ids']=$this->getProductCategories($possibleID,$productData['category_ids'],$connection);
        }
        return $productData;
    }
    public function existInMagento($productCollection,$sku,$type,$simpleskus,$attributesSetArray,$writeConnection)
    {
        $result="";
        $attribute_set = "";
        if($type=='configurable') { $simpleSkusArray = explode(",", $simpleskus); }

        foreach($productCollection as $data) {
            if(strtolower($data['sku']) == strtolower($sku))
            {
                $result=$data['entity_id'];

                if($type=='configurable')
                {
                    $attribute_set=$data['attribute_set_id'];
                    //$simplesCollection=Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('sku', array('in' => $simpleSkusArray));
                    $simplesCollection = $this->_productCollection->create()->addAttributeToSelect('*')
                        ->addFieldToFilter('sku', array('in' => $simpleSkusArray));

                    $this->updateSimpleAttributeSet($simplesCollection,$attribute_set,$attributesSetArray,$writeConnection,true);
                }
            }
        }

        if(!$result && $type=='configurable')
        {
            //$simplesCollection=Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('sku', array('in' => $simpleSkusArray));
            $simplesCollection = $this->_productCollection->create()->addAttributeToSelect('*')
                        ->addFieldToFilter('sku', array('in' => $simpleSkusArray));
            $this->updateSimpleAttributeSet($simplesCollection,$attribute_set,$attributesSetArray,$writeConnection,false);
        }

        return $result;
    }
    public function getProductCategories($id,$feedCategories,$readConnection)
    {
        $result=$feedCategories;
        $categoriesExploded=explode(',',$feedCategories);

        $query="SELECT category_id FROM catalog_category_product WHERE product_id=".$id;
        $queryResult = $readConnection->fetchAll($query);
        $arrayMerged=array();
        foreach($queryResult as $qd){ $categoriesExploded[]=$qd['category_id']; }

        $categoriesExploded=array_unique($categoriesExploded);
        $result=implode(',',$categoriesExploded);

        return $result;
    }
    public function returnOptions($attribute_code)
    {
        $result=array();

        $attribute = $this->_eavConfig->getAttribute('catalog_product', $attribute_code);

        foreach ($attribute->getSource()->getAllOptions(true, true) as $instance) {
            if (!in_array($instance['label'], $result)){ $result[$instance['value']] = $instance['label']; }
        }

        return $result;
    }
    public function getRealOptionValue($options,$value)
    {
        $result=$value;
        $found=false;

        if(in_array($value,$options)){ $found=true; }

        if(!$found)
        {
            $valueUnformatted=strtolower($value);
            foreach($options as $od){ if(strtolower($od) == $valueUnformatted){ $result=$od; } }
        }
        return $result;
    }
    public function updateSimpleAttributeSet($simplesCollection,$attribute_set,$attributesSetArray,$writeConnection,$attibute_setFound)
    {
        if(!$attibute_setFound)
        {
            foreach($attributesSetArray as $attSetData)
            {
                if($attSetData['attribute_set_name'] == $attribute_set){ $attribute_set=$attSetData['attribute_set_id']; }
            }
        }

        foreach($simplesCollection as $product)
        {
            if($product->getAttributeSet() != $attribute_set)
            {
                $query="UPDATE `catalog_product_entity` SET `attribute_set_id`='".$attribute_set."' WHERE `entity_id`='".$product->getId()."';";
                $writeConnection->query($query);
            }
        }
    }
    public function unsetOldParentForSimpleProduct($configurableSkus,$readConnection,$writeConnection)
    {
        $configurableData=Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('sku', array('in' => $configurableSkus));

        foreach($configurableData as $configurable)
        {
            $simpleProducts = $configurable->getUsedProductCollection()->addFilterByRequiredOptions();
            $querySelectChildren="SELECT * FROM  `catalog_product_super_link` WHERE  `parent_id` =".$configurable->getId();
            $childrenIds = $readConnection->fetchAll($querySelectChildren);

            foreach($childrenIds as $spData)
            {
                $querySelectParents="SELECT * FROM  `catalog_product_super_link` WHERE  `product_id` =".$spData['product_id'];
                $parentIds = $readConnection->fetchAll($querySelectParents);

                if(count($parentIds) > 1)
                {
                    foreach($parentIds as $pid)
                    {
                        if($pid['parent_id'] != $configurable->getId())
                        {
                            $deleteQuery="DELETE FROM  `ddirect_production`.`catalog_product_super_link` WHERE  `catalog_product_super_link`.`link_id` =".$pid['link_id'];
                            $writeConnection->query($deleteQuery);
                        }
                    }
                }
            }
        }
    }
    public function getProductCategoriesToClear($readConnection,$product_ids)
    {
        $result=array();

        $querySelect="SELECT DISTINCT category_id FROM catalog_category_product WHERE product_id IN(".implode(",",$product_ids).")";
        $categories = $readConnection->fetchAll($querySelect);

        foreach($categories as $category){ $result[]=$category['category_id']; }

        return $result;
    }
    public function sendEmail($emailBody)
    {
        $emailResult = wordwrap($emailBody, 70);
        $from='magenotify@diversdirect.com';
        $estTZ=new \DateTime("now", new \DateTimeZone('US/Eastern'));
        $subject='Products Import Finished, File: Date:'.$estTZ->format('m-d-Y h:i:s');
        mail("magemodules@diversdirect.com",$subject,$emailResult,"From: $from\n");
        //mail("jvillalobos@qxdev.com",$subject,$emailResult,"From: $from\n");
    }

    
}

