<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block;

use Amasty\Xsearch\Helper\Data as Helper;
use Magento\Framework\View\Element\Template\Context;

class Head extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_LAYOUT_ENABLED = 'layout/enabled';
    const XML_PATH_LAYOUT_BORDER = 'layout/border';
    const XML_PATH_LAYOUT_HOVER = 'layout/hover';
    const XML_PATH_LAYOUT_HIGHLIGHT = 'layout/highlight';
    const XML_PATH_LAYOUT_BACKGROUND = 'layout/background';
    const XML_PATH_LAYOUT_TEXT = 'layout/text';
    const XML_PATH_LAYOUT_HOVER_TEXT = 'layout/hover_text';
    
    /**
     * @var Helper
     */
    private $helper;

    /**
     * Head constructor.
     * @param Context $context
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }

    /**
     * @return string
     */
    public function getLayoutEnabled()
    {
        return $this->helper->getModuleConfig(self::XML_PATH_LAYOUT_ENABLED);
    }

    /**
     * @return string
     */
    public function getLayoutBorder()
    {
        return $this->helper->getModuleConfig(self::XML_PATH_LAYOUT_BORDER);
    }

    /**
     * @return string
     */
    public function getLayoutHover()
    {
        return $this->helper->getModuleConfig(self::XML_PATH_LAYOUT_HOVER);
    }

    /**
     * @return string
     */
    public function getLayoutHighlight()
    {
        return $this->helper->getModuleConfig(self::XML_PATH_LAYOUT_HIGHLIGHT);
    }

    /**
     * @return string
     */
    public function getLayoutBackground()
    {
        return $this->helper->getModuleConfig(self::XML_PATH_LAYOUT_BACKGROUND);
    }

    /**
     * @return string
     */
    public function getLayoutText()
    {
        return $this->helper->getModuleConfig(self::XML_PATH_LAYOUT_TEXT);
    }

    /**
     * @return string
     */
    public function getLayoutHoverText()
    {
        return $this->helper->getModuleConfig(self::XML_PATH_LAYOUT_HOVER_TEXT);
    }
}
