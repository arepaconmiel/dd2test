<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Qxd\Updateprice\Model\Amasty\Xsearch\Block\Search;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Framework\DB\Select;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Amasty\Xsearch\Controller\RegistryConstants;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorage;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product as MagentoProduct;;

/**
 * @method \Amasty\Xsearch\Block\Search\Product setNumResults(int $size)
 * @method int getNumResults()
 */
class Product extends \Amasty\Xsearch\Block\Search\Product
{       

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
            $string,
            $formKey,
            $xSearchHelper,
            $redirector,
            $collectionFactory,
            $data
        );
       $this->_template = 'Amasty_Xsearch::search/product.phtml';

    }


    public function getThemeData()
    {   
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $_scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $_storeManager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
        $_themeProvider = $objectManager->create('Magento\Framework\View\Design\Theme\ThemeProviderInterface');
        $themeId = $_scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $_storeManager->getStore()->getId()
        );

        /** @var $theme \Magento\Framework\View\Design\ThemeInterface */
        $theme = $_themeProvider->getThemeById($themeId);
        $theme_path = $theme['theme_path'];

        return $theme_path;
    }

    /**
     * @return array
     */
    public function getResults()
    {   
        $results = [];

        $theme_data = $this->getThemeData();


        $final_collection = $this->getLoadedProductCollection()
            ->addAttributeToSelect('price_range_config')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('special_price');
        foreach ($final_collection as $product) {
            $data['img'] = str_replace('frontend/_view','frontend/'.$theme_data,$this->getImage($product, 'amasty_xsearch_page_list')->toHtml());
            $data['url'] = $product->getProductUrl();
            //$data['name'] = $this->getName($product);
            $data['name'] = $product->getName();
            $data['description'] = $this->getDescription($product);
            $data['price'] = $this->getCustomProductPrice($product);
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
     * @param Product $product
     * @return string
     */
	public function getCustomProductPrice(MagentoProduct $product)
    {      

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/configurable.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
        //here get the range 
        $price_config = $product->getPriceRangeConfig();

        $html = "";     

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');

        /*if ($product->getTypeId() == 'bundle') {
            $logger->info(print_r('es bundle :' . $product->getSku(), true));
        }*/

        if($price_config && trim($price_config) != ''){
            // with range

            /*$logger->info(print_r($product->getSku(), true));
            $logger->info(print_r($price_config, true));*/
            $aux_data =[];

            $price_config = json_decode($price_config, true); 
            
            $range_regular = $price_config['rangeRegular'];
            if($range_regular['lower'] != $range_regular['higher']){
                $price_config['formatted_regular_price'] = $priceHelper->currency($range_regular['lower'], true, false) . '-' . $priceHelper->currency($range_regular['higher'], true, false);
            }else{
                $price_config['formatted_regular_price'] = $priceHelper->currency($range_regular['lower'], true, false);
            }

            if($price_config['hasSpecialPrice']){
                $range_special = $price_config['rangeSpecial'];
                if($range_special['lower'] != $range_special['higher']){
                    $price_config['formatted_special_price'] = $priceHelper->currency($range_special['lower'], true, false) . '-' . $priceHelper->currency($range_special['higher'], true, false);
                }else{
                    $price_config['formatted_special_price'] = $priceHelper->currency($range_special['lower'], true, false);
                }
            }

            $price_html = $this->getConfigurablePriceHtml($product, $price_config);
            $html = $this->wrapResult($price_html, $product);
            
        }else{

            if ($product->getTypeId() == 'bundle') {
                //as low as
                $priceInfo = $product->getPriceInfo()->getPrice('final_price');
                $minRaw = $priceInfo->getMinimalPrice()->getValue();// For min price
                $aux_old_price = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();

                $display =false;
                if($aux_old_price > $minRaw){
                    $display = true;
                }
                $price = $priceHelper->currency($minRaw, true, false);
                $old_price = $priceHelper->currency($aux_old_price, true, false);
                $price_html = $this->getBundlePriceHtml($price, $old_price, $display);



                $html = $this->wrapResult($price_html, $product);

            }else{
                $html = $this->getProductPrice($product);
            }

            
        }

        return $html;
    }

    protected function getBundlePriceHtml($price, $old_price, $displayOld){
        $html = '<span class="normal-price">
                    <span class="price-container price-final_price tax weee">';


        $html .= '<span class="price-label">'.__('As low as').'</span>'; 

        $html .= '<span class="price-wrapper ">
                    <span class="price selected">'.$price.'</span>
                  </span>
                </span>';
        $html .= '</span>';

        if($displayOld){
            $html .= '<span class="old-price">
                    <span class="price-container price-final_price tax weee">';
            $html .= '<span class="price-wrapper ">
                        <span class="price selected">'.$old_price.'</span>
                      </span>
                    </span>';
            $html .= '</span>';
        }
        
        return $html;
    }

    protected function getConfigurablePriceHtml($product, $data){
       $html = '<span class="normal-price">
                    <span class="price-container price-final_price tax weee">';

        if($data['hasSpecialPrice']){
            $html .= '<span class="price-label">'.__('Regular Price').'</span>'; 
        }
        //$data['formated_price']
        // este deberia ya haber sido validado para mostar ;rnago o solo un precio
        $html .= '<span class="price-wrapper ">
                    <span class="price selected">'.$data['formatted_regular_price'].'</span>
                  </span>
                </span>';
        $html .= '</span>';

        if($data['hasSpecialPrice']){
            $html .= '<span class="special-price">
                <span class="price-container price-final_price tax weee">';
            $html .= '<span class="price-label">'.__('Special Price').'</span>';
            $html .= '<span class="price-wrapper ">
                    <span class="price selected">'.$data['formatted_special_price'].'</span>
                        </span>
                    </span>';
            $html .= '</span>';
        }        

        
        return $html;
    }

    protected function wrapResult($html, $product)
    {
        return '<div class="price-box price-final_price" ' .
            'data-role="priceBox" ' .
            'data-product-id="' . $product->getId() . '" ' .
            'data-price-box="product-id-' . $product->getId() . '"' .
            '>' . $html . '</div>';
    }

}