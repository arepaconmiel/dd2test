/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';
    return Abstract.extend({
        defaults: {
            valuesForEnable: [],
            valuesForOption: [],
            valuesForValidateGreatThan: 0,
            disabled: true,
            imports: {
                toggleDisable:
                    'product_attribute_add_form.product_attribute_add_form.base_fieldset.frontend_input:value',
                toggleOption:
                    'product_attribute_add_form.product_attribute_add_form.base_fieldset.frontend_input:value',
                handleChanges:
                    'product_attribute_add_form.product_attribute_add_form.base_fieldset.frontend_input:value',
                setValueForValidateGreatThan:
                    'product_attribute_add_form.product_attribute_add_form.base_fieldset.frontend_input:value'
            },
            valueUpdate: 'input'
        },
        /**
         * Toggle disabled state.
         *
         * @param {Number} selected
         */
        toggleDisable: function (selected) {
            this.disabled(!(selected in this.valuesForEnable));
            if(selected in this.valuesForEnable) {
                this.show();
            }else{
                this.hide();
            }
        },
        /**
         * Toggle option state.
         *
         * @param {Number} selected
         */
        toggleOption: function (selected) {
            this.disabled(!(selected in this.valuesForOption));
            if(selected in this.valuesForOption) {
                this.show();
            }else{
                this.hide();
            }
        },

        /**
         * Change validator
         */
        setValueForValidateGreatThan: function (value) {
            this.valuesForValidateGreatThan = value;
        },

        /**
         * Change validator
         */
        handleChanges: function (value) {
            var isDigits = value !== 1;
            this.validation['greater-than-equals-to'] = isDigits ? this.valuesForValidateGreatThan : 99999999.9999;
            this.validate();
        }
    });
});