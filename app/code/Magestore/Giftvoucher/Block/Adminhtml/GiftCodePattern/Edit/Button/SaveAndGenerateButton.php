<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\GiftCodePattern\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\Registry;

/**
 * Class SaveAndGenerateButton
 */
class SaveAndGenerateButton implements ButtonProviderInterface
{
    /**
     * @var Registry
     */
    protected $coreRegistry;
    
    /**
     * @param Registry $coreRegistry
     */
    public function __construct(Registry $coreRegistry)
    {
        $this->coreRegistry = $coreRegistry;
    }
    
    /**
     * @return array
     */
    public function getButtonData()
    {
        /** @var \Magestore\Giftvoucher\Model\GiftCodePattern $model */
        $model = $this->coreRegistry->registry('giftcodepattern_data');
        if ($model->getIsGenerated()) {
            return [];
        }
        return [
            'label' => __('Save and Generate'),
            'class' => 'save',
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'giftcard_pattern_form.giftcard_pattern_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'additional_action' => 'generate',
                                    ]
                                ]
                            ]
                        ]
                    ],
                ],
            ],
            'sort_order' => 85,
        ];
    }
}
