<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\GiftTemplate\Edit;

/**
 * Class Gallery
 * @package Magestore\Giftvoucher\Block\Adminhtml\GiftTemplate\Edit
 */
class Gallery extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery
{
    /**
     * Gallery field name suffix
     *
     * @var string
     */
    protected $fieldNameSuffix = 'gifttemplate';

    /**
     * Gallery html id
     *
     * @var string
     */
    protected $htmlId = 'media_gallery';

    /**
     * Gallery name
     *
     * @var string
     */
    protected $name = 'media_gallery';

    /**
     * Html id for data scope
     *
     * @var string
     */
    protected $image = 'image';

    /**
     * @var string
     */
    protected $formName = 'giftcard_template_form';
    

    /**
     * Prepares content block
     *
     * @return string
     */
    public function getContentHtml()
    {
        $content = $this->getLayout()->createBlock(
            'Magestore\Giftvoucher\Block\Adminhtml\GiftTemplate\Edit\Gallery\Content',
            $this->getHtmlId() . '.' . 'content',
            []
        );
        $content->setId($this->getHtmlId() . '_content')->setElement($this);
        $content->setFormName($this->formName);
        $galleryJs = $content->getJsObjectName();
        $content->getUploader()->getConfig()->setMegiaGallery($galleryJs);
        return $content->toHtml();
    }
    
    /**
     * Get product images
     *
     * @return array|null
     */
    public function getImages()
    {
        return explode(',', $this->registry->registry('gift_template')->getImages());
    }
    
    /**
     *
     */
    public function getModelData()
    {
        return $this->registry->registry('gift_template');
    }
}
