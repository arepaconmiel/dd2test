<?php
     
namespace Qxd\Rewardpoints\Plugin;
 
class Autosaverewardpoints
{   

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\Product $product,
        \Qxd\Rewardpoints\Helper\Data $helper
    ) {
        $this->_request = $request;
        $this->_product = $product;
        $this->_helper = $helper;
    }

    /*
    * This plugin assign the reward points to a product based on its price 
    * when is saved or updated in the admin panel 
    */

    public function afterExecute(
        \Magento\Catalog\Controller\Adminhtml\Product\Save $subject, 
        $result
    ) 
    {   

        $productId = $this->_request->getParam('id');
        $product = $this->_product->load($productId);

        $type = $product->getTypeId();
        $price = $this->_helper->returnRewardPointsForProducts($product);

        if($type == "simple" || $type == "configurable" || $type == "giftvoucher") { 
            $product->setRewardPoints($price);
        }
        if($type == "bundle") {
            if($product->getPriceType() == 1){ 
                $product->setRewardPoints($price);
            }
            else{
                $bundle_price = $this->_helper->getPriceBundle($product);
                $product->setRewardPoints($bundle_price);
            }
        } 

        $product->save();

        return $result;
    }
}