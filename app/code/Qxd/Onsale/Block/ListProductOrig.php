<?php
namespace Qxd\Onsale\Block;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Config\Element;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Url\Helper\Data;
 
class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{   

    // overwrite all the fucntion which use the function _getProductCollection
    // to use _customGetProductCollection instead

    public function _customGetProductCollection()
    {   
        //if ($this->_productCollection === null) {
            $this->_productCollection = $this->customInitializeProductCollection();
        //}

        return $this->_productCollection;
    }

    public function customInitializeProductCollection(){

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/category_collection.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $params = $this->getRequest()->getParams();

        //here create the custom collection
        $layer = $this->getLayer();
        /* @var $layer Layer */
        if ($this->getShowRootCategory()) {
            $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
        }

        // if this is a product view page
        if ($this->_coreRegistry->registry('product')) {
            // get collection of categories this product is associated with
            $categories = $this->_coreRegistry->registry('product')
                ->getCategoryCollection()->setPage(1, 1)
                ->load();
            // if the product is associated with any category
            if ($categories->count()) {
                // show products from this category
                $this->setCategoryId(current($categories->getIterator())->getId());
            }
        }

        $origCategory = null;
        if ($this->getCategoryId()) {
            try {
                $category = $this->categoryRepository->get($this->getCategoryId());
            } catch (NoSuchEntityException $e) {
                $category = null;
            }

            if ($category) {
                $origCategory = $layer->getCurrentCategory();
                $layer->setCurrentCategory($category);
            }
        }
        $collection = $layer->getProductCollection();

        
        //$logger->info('enter lol');
        $param = $this->getRequest()->getParam('ddsale');


//      qxd start
        if(isset($param) && $param==='1'){
           $collection
                ->addFinalPrice()
                ->getSelect()
                ->where('price_index.final_price < price_index.price');
        }
//      qxd end

        
        $logger->info(print_r($collection->getSelect()->__toString(),true));

        $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        if ($origCategory) {
            $layer->setCurrentCategory($origCategory);
        }
        
        $this->customAddToolbarBlock($collection);

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );

        return $collection;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return AbstractCollection
     */
    public function getLoadedProductCollection()
    {   
        return $this->_customGetProductCollection();
    }

    /**
     * @param array|string|integer| Element $code
     * @return $this
     */
    public function addAttribute($code)
    {
        $this->_customGetProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];

        $category = $this->getLayer()->getCurrentCategory();
        if ($category) {
            $identities[] = Product::CACHE_PRODUCT_CATEGORY_TAG . '_' . $category->getId();
        }

        //Check if category page shows only static block (No products)
        if ($category->getData('display_mode') == Category::DM_PAGE) {
            return $identities;
        }

        foreach ($this->_customGetProductCollection() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }

        return $identities;
    }

    /**
     * Add toolbar block from product listing layout
     *
     * @param Collection $collection
     */
    public function customAddToolbarBlock(Collection $collection)
    {
        $toolbarLayout = $this->customGetToolbarFromLayout();

        if ($toolbarLayout) {
            $this->customConfigureToolbar($toolbarLayout, $collection);
        }
    }

    /**
     * Get toolbar block from layout
     *
     * @return bool|Toolbar
     */
    public function customGetToolbarFromLayout()
    {
        $blockName = $this->getToolbarBlockName();

        $toolbarLayout = false;

        if ($blockName) {
            $toolbarLayout = $this->getLayout()->getBlock($blockName);
        }

        return $toolbarLayout;
    }

    /**
     * Configures the Toolbar block with options from this block and configured product collection.
     *
     * The purpose of this method is the one-way sharing of different sorting related data
     * between this block, which is responsible for product list rendering,
     * and the Toolbar block, whose responsibility is a rendering of these options.
     *
     * @param ProductList\Toolbar $toolbar
     * @param Collection $collection
     * @return void
     */
    public function customConfigureToolbar(Toolbar $toolbar, Collection $collection)
    {
        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
    }
}




