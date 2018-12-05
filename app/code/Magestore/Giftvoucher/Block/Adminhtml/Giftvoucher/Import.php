<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Giftvoucher;

/**
 * Adminhtml Giftvoucher Import Block
 */
class Import extends \Magento\Backend\Block\Widget\Form\Container
{
    public function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'Magestore_Giftvoucher';
        $this->_controller = 'adminhtml_giftvoucher';
        $this->_mode = 'import';
        $this->buttonList->update('save', 'label', __('Import'));
        $this->buttonList->remove('delete');
        $this->buttonList->remove('reset');
        
        $this->buttonList->add(
            'print',
            array(
                'label' => __('Import and Print'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'save',
                            'target' => '#edit_form',
                            'eventData' => ['action' => ['args' => ['print' => 'print']]],
                        ],
                    ],
                ]
            ),
            100
        );
    }
}
