/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */


define([
    'jquery',
    'underscore',
    'mage/template',
    'uiRegistry',
    'jquery/ui',
    'baseImage',
    'Magento_Catalog/js/product-gallery'
], function ($, _, mageTemplate, registry) {
    'use strict';
    
    $.widget('magestore.giftTemplateGallery', $.mage.productGallery, {
        options: {
            dialogTemplate: '[data-role=img-dialog-tmpl]',
            dialogContainerTmpl: '[data-role=img-dialog-container-tmpl]'
        },

        _create: function () {
            var template = this.element.find(this.options.dialogTemplate),
                containerTmpl = this.element.find(this.options.dialogContainerTmpl);

            this._super();
            this.modalPopupInit = false;

            if (template.length) {
                this.dialogTmpl = mageTemplate(template.html().trim());
            }

            if (containerTmpl.length) {
                this.dialogContainerTmpl = mageTemplate(containerTmpl.html().trim());
            } else {
                this.dialogContainerTmpl = mageTemplate('');
            }

            this._initDialog();
        },

        /**
         * Bind handler to elements
         * @protected
         */
        _bind: function () {
            var events = {};

            this._super();

            events['click [data-role=close-panel]'] = $.proxy(function () {
                this.element.find('[data-role=dialog]').trigger('close');
            }, this);

            this._on(events);
            this.element.on('sortstart', $.proxy(function () {
                this.element.find('[data-role=dialog]').trigger('close');
            }, this));
        },

        /**
         * Initializes dialog element.
         */
        _initDialog: function () {
            var $dialog = $(this.dialogContainerTmpl());

            $dialog.modal({
                'type': 'slide',
                title: $.mage.__('Gift Cart Preview'),
                buttons: [],
                opened: function () {
                    $dialog.trigger('open');
                },
                closed: function () {
                    $dialog.trigger('close');
                }
            });

            $dialog.on('open', this.onDialogOpen.bind(this));
            $dialog.on('close', function () {
                var $imageContainer = $dialog.data('imageContainer');
                $imageContainer.removeClass('active');
            });

            this.$dialog = $dialog;
        },

        _showDialog: function (imageData) {
            var $imageContainer = this.findElement(imageData),
                $template;
            
            imageData.textColor = $("input[name='text_color']").val();
            imageData.styleColor = $("input[name='style_color']").val();
            imageData.notes = $("textarea[name='notes']").val();

            $template = this.dialogTmpl({
                'data': imageData
            });
            
            this.$dialog
                .html($template)
                .data('imageData', imageData)
                .data('imageContainer', $imageContainer)
                .modal('openModal');
        },

        /**
         * Handles dialog open event.
         *
         * @param {EventObject} event
         */
        onDialogOpen: function (event) {
            var imageData = this.$dialog.data('imageData'),
                imageSizeKb = imageData.sizeLabel,
                image = document.createElement('img');
            image.src = imageData.url;
        }
    });

    return $.magestore.giftTemplateGallery;    
});