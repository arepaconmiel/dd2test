<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Block\Adminhtml\Giftvoucher\Import;

use \Magento\Backend\Block\Widget\Form\Generic as FormGeneric;

/**
 * Adminhtml Giftvoucher Import Form Block
 */
class Form extends FormGeneric
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(['data' => array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/processImport'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        )]);

        $fieldset = $form->addFieldset('profile_fieldset', array());

        $fieldset->addField('filecsv', 'file', array(
            'label' => __('Import File'),
            'title' => __('Import File'),
            'name' => 'filecsv',
            'required' => true,
        ));

        $fieldset->addField('sample', 'note', array(
            'label' => __('Download Sample CSV File'),
            'text' => '<a href="' .
            $this->getUrl('*/*/downloadSample') .
            '" title="' .
            __('Download Sample CSV File') .
            '">import_giftcode_sample.csv</a>'
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
