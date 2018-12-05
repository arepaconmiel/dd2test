<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\GiftTemplate\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magestore\Giftvoucher\Block\Adminhtml\Form\Button\GenericButton;

/**
 * Class PreviewButton
 * @package Magestore\Giftvoucher\Block\Adminhtml\GiftTemplate\Edit\Button
 */
class PreviewButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getEntityId()) {
            $data = [
                'label' => __('Preview'),
                'class' => 'preview',
                'on_click' => "window.open('" . $this->getPreviewUrl() . "', 'newWindow', 'width=680,height=860,left=600,resizable=yes,scrollbars=yes')",
                'sort_order' => 80,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview', ['id' => $this->getEntityId()]);
    }
}
