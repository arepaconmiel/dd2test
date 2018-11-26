<?php
namespace Qxd\Rewardpoints\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{   
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_resource = $resource;
        $this->_storeManager = $storeManager; 
    }

    /**
    * @param \Magento\Catalog\Model\Product $product
    * 
    * @return price
    */

    public function returnRewardPointsForProducts($product)
    {
        $result=$product->getPrice();
        if($this->specialPriceIsValid($product)){
           if($product->getTypeId()=='bundle' && $product->getPriceType() == 1) { 
                $result=($result*$product->getSpecialPrice())/100; 
            }
            else{ 
                $result=$product->getSpecialPrice(); 
            }
        }

        return $result;
    }

    public function specialPriceIsValid($product)
    {
        $result=false;

        $selection=$product->getData();
        if(!empty($selection['special_price']))
        {
            $current=date("Y-m-d h:i:s");

            if(isset($selection['special_from_date'])){
                $to=$selection['special_from_date'];
            }else{
                $to='';
            }
            if(isset($selection['special_to_date'])){
                $to=$selection['special_to_date'];
            }else{
                $to='';
            }
            

            if(!empty($selection['special_price']))
            {
                if(empty($from) && empty($to)) {  $result=true; }
                if(!empty($from) && !empty($to)) { if($current >= $from && $current <= $to){ $result=true; } }
                if(empty($from) && !empty($to)){ if($current <= $to){ $result=true; } }
                if(!empty($from) && empty($to)) { if($current >= $from){ $result=true; } }
            }
        }

        return $result;
    }

    public function getPriceBundle($product)
    {
        $result=0;

        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);
        $groupedByOption=array();

        foreach($selectionCollection as $data) { $groupedByOption[$data->getData('option_id')][]=$this->returnRewardPointsForProducts($data); }
        foreach($groupedByOption as $k=>$data){ $groupedByOption[$k]=max($data); }
        $result=array_sum($groupedByOption);

        return $result;
    }

    /*
    * This function was created because the observers were calling recursively
    * Is not neccesary anymore because weare using plugins instead observer on that cases.
    */

    public function updateRewardPoints($points, $product_id)
    {
        try{
            $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

            $result_query = $connection->fetchAll("SELECT value FROM catalog_product_entity_varchar AS T1, eav_attribute AS T2 WHERE T1.entity_id = ".$product_id." AND T1.attribute_id = T2.attribute_id AND T2.attribute_code = 'reward_points'");
            
            //create register
            if(empty ($result_query)){
                $store_id = $this->_storeManager->getStore()->getId();
                $connection->query("INSERT INTO catalog_product_entity_varchar (attribute_id, store_id, entity_id, value) VALUES (158, ".$store_id.", ".$product_id.", ".$points.")");
            }else{

                $connection->query("UPDATE
                                        catalog_product_entity_varchar AS T1,
                                        (
                                            SELECT * FROM eav_attribute WHERE attribute_code = 'reward_points'
                                        ) AS T2
                                    SET
                                        T1.value = '".$points."'
                                    WHERE
                                        T1.entity_id = ".$product_id." AND T1.attribute_id = T2.attribute_id");
            }

           
        }catch (Exception $e)
        {   
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_Reward.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    
    }
    
}
