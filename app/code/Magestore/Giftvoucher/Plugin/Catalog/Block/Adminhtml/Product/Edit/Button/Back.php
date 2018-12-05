<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Plugin\Catalog\Block\Adminhtml\Product\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;

/**
 * Class Back
 * @package Magestore\Giftvoucher\Plugin\Catalog\Block\Adminhtml\Product\Edit\Button
 */
class Back
{
    /**
     * BackButton constructor
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->context = $context;
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Back $button
     * @param $result
     * @return array
     */
    public function afterGetButtonData(\Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Back $button, $result)
    {
        $type = $this->context->getRequestParam('type');
        if ($type != 'giftvoucher') {
            return $result;
        }
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $button->getUrl('giftvoucheradmin/giftproduct/')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
