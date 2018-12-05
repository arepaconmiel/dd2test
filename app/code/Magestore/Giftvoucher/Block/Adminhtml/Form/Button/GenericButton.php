<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Form\Button;

use Magento\Backend\Block\Widget\Context;

/**
 * Class Product
 * @package Magestore\Giftvoucher\Block\Adminhtml
 */
class GenericButton extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
    
        $this->context = $context;
    }

    /**getEntityId
     * Return Entity ID
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return $this->context->getRequest()->getParam('id');
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
