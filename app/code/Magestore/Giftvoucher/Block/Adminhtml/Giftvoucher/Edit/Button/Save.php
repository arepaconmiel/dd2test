<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Giftvoucher\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magestore\Giftvoucher\Block\Adminhtml\Form\Button\GenericButton;
use Magento\Ui\Component\Control\Container;

/**
 * Class Save
 */
class Save extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'giftcard_code_form.giftcard_code_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => '',
                                        'sendemail' => '',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => Container::SPLIT_BUTTON,
            'options' => $this->getOptions(),
        ];
    }
    
    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions()
    {
        $options[] = [
            'id_hard' => 'save_and_send_email',
            'label' => __('Save & Send Email'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'giftcard_code_form.giftcard_code_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'edit',
                                        'sendemail' => 'now',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $options[] = [
            'id_hard' => 'save_and_continue',
            'label' => __('Save & Continue Edit'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'giftcard_code_form.giftcard_code_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'edit',
                                        'sendemail' => '',
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
        return $options;
    }
}
