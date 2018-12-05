<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Giftvoucher\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magestore\Giftvoucher\Block\Adminhtml\Form\Button\GenericButton;

/**
 * Class Print
 */
class PrintButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getEntityId()) {
            $data = [
                'label' => __('Print'),
                'class' => 'print',
                'on_click' => "window.open('" . $this->getPrintUrl() . "', 'newWindow', 'width=1000,height=800,left=400,resizable=yes,scrollbars=yes')",
                'sort_order' => 80,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print', ['id' => $this->getEntityId()]);
    }
}
