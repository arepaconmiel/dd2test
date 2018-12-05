<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Ui\DataProvider\GiftCodePattern\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Class GiftCode
 * @package Magestore\Giftvoucher\Ui\DataProvider\GiftCodePattern\Modifier
 */
class GiftCode implements ModifierInterface
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
     * GiftCode constructor.
     * @param \Magento\Framework\Registry $coreRegistry
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        UrlInterface $urlBuilder
    ) {
            $this->coreRegistry = $coreRegistry;
            $this->urlBuilder = $urlBuilder;
    }
    
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->coreRegistry->registry('giftcodepattern_data')->getIsGenerated()) {
            return $meta;
        }
        $meta['giftcode_history'] = [
            'children' => [
                'review_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => 'giftcard_pattern_code_listing',
                                'externalProvider' => 'giftcard_pattern_code_listing.giftcard_pattern_code_listing_data_source',
                                'ns' => 'giftcard_pattern_code_listing',
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'templateId' => '${ $.provider }:data.current_template_id'
                                ],
                                'exports' => [
                                    'templateId' => '${ $.externalProvider }:params.current_template_id'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Generated Gift Codes'),
                        'collapsible' => true,
                        'opened' => false,
                        'componentType' => Fieldset::NAME,
                    ],
                ],
            ],
        ];
        return $meta;
    }
    
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $id = $this->coreRegistry->registry('giftcodepattern_data')->getId();
        $data[$id]['current_template_id'] = $id;
        return $data;
    }
}
