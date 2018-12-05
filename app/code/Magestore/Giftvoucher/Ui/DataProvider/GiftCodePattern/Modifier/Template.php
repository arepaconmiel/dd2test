<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\DataProvider\GiftCodePattern\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Field;

/**
 * Class Template
 * @package Magestore\Giftvoucher\Ui\DataProvider\GiftCodePattern\Modifier
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
        /** @var \Magestore\Giftvoucher\Model\GiftCodePattern $model */
        $model = $this->coreRegistry->registry('giftcodepattern_data');
        $disabled = $model->getIsGenerated() ? true : false;
        
        $fields = [
            'giftcard_template_id' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'dataType' => 'text',
                            'source' => 'giftcard_code',
                            'formElement' => 'select',
                            'label' => __('Template'),
                            'dataScope' => 'giftcard_template_id',
                            'disabled' => $disabled,
                            'sortOrder' => 520,
                        ],
                        'options' => $this->templatesOptionsFactory->create()->toOptionArray(),
                    ],
                ],
            ],
            'giftcard_template_image' => [
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
                            'disabled' => $disabled,
                            'sortOrder' => 530,
                        ],
                    ],
                ],
            ],
        ];
        
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
