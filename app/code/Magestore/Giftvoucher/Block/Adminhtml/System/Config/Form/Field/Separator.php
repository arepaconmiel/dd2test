<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;

/**
 * Adminhtml Giftvoucher Config Field Separator Block
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class Separator extends Field
{

    /**
     * render separator config row
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $fieldConfig = $element->getFieldConfig();
        $htmlId = $element->getHtmlId();
        $html = '<tr id="row_' . $htmlId . '">'
            . '<td class="label" colspan="3">';

        $marginTop = isset($fieldConfig['margin_top']) ? (string) $fieldConfig['margin_top'] : '0px';
        $customStyle = isset($fieldConfig['style']) ? (string) $fieldConfig['style'] : '';

        $html .= '<div style="margin-top: ' . $marginTop
            . '; font-weight: bold; border-bottom: 1px solid #dfdfdf; text-align:left !important'
            . $customStyle . '">';
        $html .= $element->getLabel();
        $html .= '</div></td></tr>';
        
        return $html;
    }
}
