/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    'underscore'
], function($, _){
    'use strict';

    /**
     * Check wether the incoming string is not empty or if doesn't consist of spaces.
     *
     * @param {String} value - Value to check.
     * @returns {Boolean}
     */
    function isEmpty(value) {
        return (value.length === 0) || (value == null) || /^\s+$/.test(value);
    }

    $.widget('magestore.searchGiftCodes', {
        options: {
            submitBtn: 'button[type="submit"]'
        },

        _create: function() {
            this.searchForm = $(this.options.formSelector);
            this.submitBtn = this.searchForm.find(this.options.submitBtn)[0];

            _.bindAll(this, '_onPropertyChange', '_onSubmit');
            this.submitBtn.disabled = true;

            this.element.on('input propertychange', this._onPropertyChange);

            this.searchForm.on('submit', $.proxy(function() {
                this._onSubmit();
            }, this));
        },

        /**
         * Executes when the search box is submitted
         */
        _onSubmit: function() {
            if (isEmpty(this.element.val())) {
                e.preventDefault();
            }
        },

        /**
         * Executes when the value of the search input field changes
         */
        _onPropertyChange: function() {
            this.submitBtn.disabled = isEmpty(this.element.val());
        }
    });

    return $.magestore.searchGiftCodes;
});
