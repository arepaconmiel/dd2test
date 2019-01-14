<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block\Search;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Framework\DB\Select;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Amasty\Xsearch\Controller\RegistryConstants;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @method \Amasty\Xsearch\Block\Search\Product setNumResults(int $size)
 * @method int getNumResults()
 */
class Product extends ListProduct
{
    const BLOCK_TYPE = 'product';
    const XML_PATH_TEMPLATE_PRODUCT_LIMIT = 'product/limit';
    const XML_PATH_TEMPLATE_TITLE = 'product/title';
    const XML_PATH_TEMPLATE_NAME_LENGTH = 'product/name_length';
    const XML_PATH_TEMPLATE_DESC_LENGTH = 'product/desc_length';
    const XML_PATH_TEMPLATE_REVIEWS = 'product/reviews';
    const XML_PATH_TEMPLATE_ADD_TO_CART = 'product/add_to_cart';

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    private $string;
    
    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    /**
     * @var \Amasty\Xsearch\Helper\Data
     */
    private $xSearchHelper;

    /**
     * @var RedirectInterface
     */
    private $redirector;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var array|null
     */
    private $products;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Amasty\Xsearch\Helper\Data $xSearchHelper,
        RedirectInterface $redirector,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
        $this->string = $string;
        $this->formKey = $formKey;
        $this->xSearchHelper = $xSearchHelper;
        $this->redirector = $redirector;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_template = 'search/product.phtml';
        parent::_construct();
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return $this
     */
    public function prepareSortableFieldsByCategory($category)
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return self::BLOCK_TYPE;
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function setIndexedIds(array $ids)
    {
        return $this->setData('indexed_ids', $ids);
    }

    /**
     * @inheritdoc
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            if (!$this->getIndexedIds()) {
                $this->_productCollection = $this->initializeProductCollection();
            } else {
                $this->_productCollection = $this->collectionFactory
                    ->create()
                    ->addAttributeToSelect(['price', 'thumbnail', 'thumbnail_label', 'description', 'name'])
                    ->addIdFilter($this->getIndexedIds())
                    ->setStore($this->_storeManager->getStore()->getId());
            }
        }

        return $this->_productCollection;
    }
    
    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function initializeProductCollection()
    {
        //Parent part without blocks and sorting initializing.
        $layer = $this->getLayer();
        $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
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
        if ($origCategory) {
            $layer->setCurrentCategory($origCategory);
        }

        //Custom part.
        $collection->clear();
        $collection->setPageSize($this->getLimit());
        $collection->getSelect()
            ->reset(Select::ORDER)
            ->order('search_result.'. TemporaryStorage::FIELD_SCORE . ' ' . Select::SQL_DESC);
        //End of custom part.

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $this->_productCollection]
        );
        return $collection;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        $results = [];
        foreach ($this->getLoadedProductCollection() as $product) {
            $data['img'] = $this->getImage($product, 'amasty_xsearch_page_list')->toHtml();
            $data['url'] = $product->getProductUrl();
            $data['name'] = $this->getName($product);
            $data['description'] = $this->getDescription($product);
            $data['price'] = 'really';
            $data['is_salable'] = $product->isSaleable();
            $data['cart_post_params'] = $this->getAddToCartPostParams($product);
            $data['compare_post_params'] = $this->_compareProduct->getPostDataParams($product);
            $data['wishlist_post_params'] = $this->getAddToWishlistParams($product);
            $data['reviews'] = $this->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
            $results[$product->getId()] = $data;
        }

        $this->setNumResults($this->getLoadedProductCollection()->getSize());
        return $results;
    }

    /**
     * @return array
     */
    public function getProducts()
    {
        if ($this->products === null) {
            $this->products = $this->getResults();
        }

        return $this->products;
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $this->getProducts();
        if ($this->getQuery() && $this->getNumResults() !== null) {
            $this->getQuery()->saveNumResults($this->getNumResults());
        }

        //Prevent collection to be unconditionaly loaded (don't need if elasticsearch is enabled).
        return $this;
    }

    /**
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        if ($this->getData('limit') === null) {
            $limit = (int)$this->xSearchHelper->getModuleConfig(self::XML_PATH_TEMPLATE_PRODUCT_LIMIT);
            $this->setData('limit', max(1, $limit));
        }

        return $this->getData('limit');
    }

    /**
     * @return \Magento\Search\Model\Query
     */
    public function getQuery()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_AMASTY_XSEARCH_QUERY);
    }

    /**
     * @return string
     */
    public function getResultUrl()
    {
        $result = null;
        if ($this->getQuery()) {
            $result = $this->xSearchHelper->getResultUrl($this->getQuery()->getQueryText());
        }

        return $result;
    }

    /**
     * @param $text
     * @return string
     */
    public function highlight($text)
    {
        if ($this->getQuery()) {
            $text = $this->xSearchHelper->highlight($text, $this->getQuery()->getQueryText());
        }

        return $text;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->xSearchHelper->getModuleConfig(self::XML_PATH_TEMPLATE_TITLE);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    private function getName(\Magento\Catalog\Model\Product $product)
    {
        $nameLength = $this->getNameLength();
        $productNameStripped = $this->stripTags($product->getName(), null, true);
        $text =
            $nameLength && $this->string->strlen($productNameStripped) > $nameLength ?
            $this->string->substr($productNameStripped, 0, $this->getNameLength()) . '...'
            : $productNameStripped;
        return $this->highlight($text);
    }

    /**
     * @return int
     */
    private function getNameLength()
    {
        return (int)$this->xSearchHelper->getModuleConfig(self::XML_PATH_TEMPLATE_NAME_LENGTH);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getDescription(\Magento\Catalog\Model\Product $product)
    {
        $descLength = $this->getDescLength();
        $productDescStripped = $this->stripTags($product->getShortDescription(), null, true);

        $text =
            $this->string->strlen($productDescStripped) > $descLength ?
            $this->string->substr($productDescStripped, 0, $descLength) . '...'
            : $productDescStripped;

        return $this->highlight($text);
    }

    /**
     * @inheritdoc
     */
    protected function getPriceRender()
    {
        return $this->_layout->createBlock(
            'Magento\Framework\Pricing\Render',
            '',
            ['data' => ['price_render_handle' => 'catalog_product_prices']]
        );
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $result = parent::getAddToCartPostParams($product);
        $result['data']['return_url'] =  $this->redirector->getRefererUrl();
        return $result;
    }

    /**
     * @return string
     */
    public function getUlrEncodedParam()
    {
        return Action::PARAM_NAME_URL_ENCODED;
    }

    /**
     * @param array $product
     * @return bool
     */
    public function isShowDescription(array $product)
    {
        return $this->string->strlen($product['description']) > 0
            && $this->getDescLength() > 0;
    }

    /**
     * @return int
     */
    private function getDescLength()
    {
        return (int)$this->xSearchHelper->getModuleConfig(self::XML_PATH_TEMPLATE_DESC_LENGTH);
    }

    /**
     * @return bool
     */
    public function getReviews()
    {
        return (bool)$this->xSearchHelper->getModuleConfig(self::XML_PATH_TEMPLATE_REVIEWS) == '1' ? 1 : 0;
    }

    /**
     * @return bool
     */
    public function getAddToCart()
    {
        return (bool)$this->xSearchHelper->getModuleConfig(self::XML_PATH_TEMPLATE_ADD_TO_CART) == '1'? 1 : 0;
    }
}
