<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\DataProvider\Giftvoucher\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Field;

/**
 * Class Template
 * @package Magestore\Giftvoucher\Ui\DataProvider\Giftvoucher\Modifier
 */
class Template implements ModifierInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var \Magestore\Giftvoucher\Model\Source\Giftvoucher\TemplatesOptionsFactory
     */
    protected $templatesOptionsFactory;
    
    /**
     * @var \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\Collection
     */
    private $templates;

    /**
     * Template constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     * @param UrlInterface $urlBuilder
     * @param \Magestore\Giftvoucher\Model\Source\Giftvoucher\TemplatesOptionsFactory $templatesOptionsFactory
     * @param \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\Collection $templates
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        UrlInterface $urlBuilder,
        \Magestore\Giftvoucher\Model\Source\Giftvoucher\TemplatesOptionsFactory $templatesOptionsFactory,
        \Magestore\Giftvoucher\Model\ResourceModel\GiftTemplate\Collection $templates
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->urlBuilder = $urlBuilder;
        $this->templatesOptionsFactory = $templatesOptionsFactory;
        $this->templates = $templates;
    }
    
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $fields = [];
        /** @var \Magestore\Giftvoucher\Model\Giftvoucher $giftvoucher */
        $giftvoucher = $this->coreRegistry->registry('giftvoucher_data');
        
        if ($giftvoucher->getGiftcardCustomImage()) {
            // Show Custom Image
            $fields['giftcard_template_image'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'dataType' => 'text',
                            'source' => 'giftcard_code',
                            'formElement' => 'input',
                            'component' => 'Magestore_Giftvoucher/js/form/element/image-preview',
                            'label' => __('Customer\'s Image'),
                            'dataScope' => 'giftcard_template_image',
                            'mediaUrl' => $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
                                . 'giftvoucher/template/images/',
                            'sortOrder' => 420,
                        ],
                    ],
                ],
            ];
        } else {
            $fields['giftcard_template_id'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'dataType' => 'text',
                            'source' => 'giftcard_code',
                            'formElement' => 'select',
                            'label' => __('Template'),
                            'dataScope' => 'giftcard_template_id',
                            'sortOrder' => 420,
                        ],
                        'options' => $this->templatesOptionsFactory->create()->toOptionArray(),
                    ],
                ],
            ];
            $fields['giftcard_template_image'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'dataType' => 'text',
                            'source' => 'giftcard_code',
                            'formElement' => 'input',
                            'component' => 'Magestore_Giftvoucher/js/form/element/image-select',
                            'label' => __('Template image'),
                            'dataScope' => 'giftcard_template_image',
                            'mediaUrl' => $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])
                                . 'giftvoucher/template/images/',
                            'templateImages' => $this->getTemplateImages(),
                            'imports' => [
                                'templateId' => '${ $.parentName }.giftcard_template_id:value',
                            ],
                            'sortOrder' => 430,
                        ],
                    ],
                ],
            ];
        }
        
        $meta = array_merge_recursive($meta, [
            'general' => [
                'children' => $fields,
            ],
        ]);
        return $meta;
    }
    
    /**
     * Template Images
     *
     * @return multitype:multitype:
     */
    protected function getTemplateImages()
    {
        $images = [];
        foreach ($this->templates as $template) {
            $images[$template->getId()] = explode(',', $template->getImages());
        }
        return $images;
    }
    
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }
}
