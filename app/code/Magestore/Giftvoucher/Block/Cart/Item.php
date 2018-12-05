<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Cart;

use Magento\Catalog\Model\Config\Source\Product\Thumbnail as ThumbnailSource;
use Magento\Checkout\Block\Cart\Item\Renderer;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Message\InterpretationStrategyInterface;

/**
 * Giftvoucher Cart Item Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Item extends Renderer implements IdentityInterface
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Item constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param InterpretationStrategyInterface $messageInterpretationStrategy
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     * @internal param \Magento\Catalog\Helper\Image $imageHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Module\Manager $moduleManager,
        InterpretationStrategyInterface $messageInterpretationStrategy,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productConfig,
            $checkoutSession,
            $imageBuilder,
            $urlHelper,
            $messageManager,
            $priceCurrency,
            $moduleManager,
            $messageInterpretationStrategy,
            $data
        );
        $this->_objectManager = $objectManager;
        $this->setTemplate('Magestore_Giftvoucher::giftvoucher/cart/item.phtml');
    }

    /**
     * @return array
     */
    public function getProductOptions()
    {

        $options = parent::getProductOptions();


        $giftvoucherOptions = $this->_objectManager->create('Magestore\Giftvoucher\Helper\Data')
            ->getGiftVoucherOptions();
        $templates = $this->_objectManager->create('Magestore\Giftvoucher\Model\GiftTemplate')
            ->getCollection()
            ->addFieldToFilter('status', '1');
        $item = parent::getItem();
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProduct()->getId());
        ;
        $cartType = $product->getGiftCardType();

        $options = $item->getProductOptions();

        $buyRequest = $options['info_buyRequest'];
        //var_dump($product->getGiftCardType());die('xxx');
        foreach ($giftvoucherOptions as $code => $label) {
            if ($option = $this->getItem()->getOptionByCode($code)) {
                if ($code == 'giftcard_template_id') {
                    foreach ($templates as $template) {
                        if ($template->getId() == $option->getValue()) {
                            $valueTemplate = $template;
                        }
                    }
                    if ($cartType !=1) {
                        $options[] = array(
                            'label' => $label,
                            'value' => $this->escapeHtml($valueTemplate->getTemplateName() ?
                                $valueTemplate->getTemplateName() : $option->getValue()),
                        );
                    }
                } elseif ($code == 'amount') {
                    $options[] = array(
                        'label' => $label,
                        'value' => $this->priceCurrency->format(
                            $option->getValue(),
                            true,
                            PriceCurrencyInterface::DEFAULT_PRECISION,
                            $this->_storeManager->getStore()
                        )
                    );
                } else {
                    $options[] = array(
                        'label' => $label,
                        'value' => $this->escapeHtml($option->getValue()),
                    );
                }
            }
        }
        return $options;
    }

    /**
     * @return string
     */
    public function getProductThumbnail()
    {
        $item = $this->getItem();
        if ($item->getOptionByCode('giftcard_template_image')) {
            $filename = $item->getOptionByCode('giftcard_template_image')->getValue();
        } else {
            $filename = 'default.png';
        }
        if ($item->getOptionByCode('giftcard_use_custom_image')
            && $item->getOptionByCode('giftcard_use_custom_image')->getValue()) {
            $urlImage = '/tmp/giftvoucher/images/' . $filename;
        } else {
            $urlImage = '/giftvoucher/template/images/' . $filename;
        }

        $imageUrl = $this->_objectManager->create('Magestore\Giftvoucher\Helper\Data')
                ->getStoreManager()
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . $urlImage;

        return $imageUrl;
    }


    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        $result =  $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
        if ($this->_objectManager->create('Magestore\Giftvoucher\Helper\Data')->getStoreConfig('giftvoucher/interface_checkout/display_image_item')) {
            $result->setImageUrl($this->getProductThumbnail());
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getImageSrc()
    {
        $thumbnail = $this->getProductThumbnail();
        return $thumbnail;
    }

    /**
     * @return \Magento\Quote\Model\Quote\Item\AbstractItem
     */
    public function getItem()
    {
        $item = parent::getItem();
        
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProduct()->getId());

        $rowTotal = $item->getRowTotal();
        $qty = $item->getQty();
        $store = $item->getStore();
        $price = $this->priceCurrency->round($rowTotal) / $qty;

        $baseCurrencyCode = $this->_storeManager->getStore()->getBaseCurrencyCode();
        $quoteCurrencyCode = $item->getQuote()->getQuoteCurrencyCode();
        $baseCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')->load($baseCurrencyCode);

        if ($baseCurrencyCode != $quoteCurrencyCode) {
            $quoteCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')
                ->load($quoteCurrencyCode);
            if ($product->getGiftType() == \Magestore\Giftvoucher\Model\Source\GiftType::GIFT_TYPE_RANGE) {
                $price = $price * $price / $baseCurrency->convert($price, $quoteCurrency);
                $item->setPrice($price);
            }
        }

        $options = $item->getOptions();
        $result = array();
        foreach ($options as $option) {
            $result[$option->getCode()] = $option->getValue();
        }

        if (isset($result['base_gc_value']) && isset($result['base_gc_currency'])) {
            $currency = $store->getCurrentCurrencyCode();
            $currentCurrency = $this->_objectManager->create('Magento\Directory\Model\Currency')->load($currency);
            $amount = $baseCurrency->convert($result['base_gc_value'], $currentCurrency);
            foreach ($options as $option) {
                if ($option->getCode() == 'amount') {
                    $option->setValue($amount);
                }
            }
            $item->setOptions($options)->save();
        }

        return $item;
    }
}
