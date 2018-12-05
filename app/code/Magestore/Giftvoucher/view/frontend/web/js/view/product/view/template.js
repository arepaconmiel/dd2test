/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'uiComponent',
        'ko',
        'jquery',
        'Magestore_Giftvoucher/js/model/product/giftcard',
        'Magestore_Giftvoucher/js/helper/giftvoucher'
    ],
    function (Component, ko, $, giftCard, giftCardHelper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Giftvoucher/product/view/template'
            },

            offset : 0,

            templates: ko.observableArray(window.giftvoucherConfig.templates),


            imageSelectedValue: ko.pureComputed(function () {
                return giftCard.imageSelectedValue();
            }),

            selectedTimezone: ko.pureComputed(function () {
                return giftCard.selectedTimezone();
            }),


            images: ko.pureComputed(function () {
                var selectedTemplate = giftCard.selectedTemplate();
                var images = selectedTemplate.images;
                if (images) {
                    var imageArray = images.split(',');
                    var imageArrayUrl = [];
                    $.each(imageArray, function (index, value) {
                        imageArrayUrl.push(window.giftvoucherConfig.imageBaseUrl + '/' + value);
                    });
                    return imageArrayUrl;
                } else {
                    return [];
                }

            }),

            selectedTemplate: ko.pureComputed(function () {
                return giftCard.selectedTemplate();
            }),

            selectedImage: ko.pureComputed(function () {
                var selectedImage = giftCard.selectedImage();
                var domain = window.giftvoucherConfig.imageBaseUrl + '/';
                return selectedImage.replace(domain, '');
            }),

            selectedTemplateImage: ko.pureComputed(function () {
                return giftCard.selectedTemplateImage();
            }),

            imagesDivWidth: ko.pureComputed(function () {
                var selectedTemplate = giftCard.selectedTemplate();
                var images = selectedTemplate.images;
                if (images) {
                    var imageArray = images.split(',');
                    console.log(imageArray.length * 70 + 'px');
                    return imageArray.length * 70 + 'px';
                } else {
                    return '70px';
                }

            }),

            initialize: function () {
                this._super();
                var self = this;
                var templates = this.templates();
                if (templates && !(this.selectedTemplate())) {
                    var firstTemplate = templates[0];
                    this.selectTemplate(firstTemplate);
                } else {
                    $.each(templates, function (index, value) {
                       if (value.giftcard_template_id === self.selectedTemplate()) {
                           giftCard.selectedTemplate(value);
                           giftCard.templatePreviewFile(value.template_file);


                           giftCard.selectedTemplateImage(giftCard.selectedTemplate().giftcard_template_id + '-'
                               + window.giftvoucherConfig.imageBaseUrl
                               + '/' + giftCard.selectedImage());
                           giftCard.selectedImage(window.giftvoucherConfig.imageBaseUrl
                               + '/' + giftCard.selectedImage());

                       }
                    });

                }
                $('#product-giftcard-price').html('<span class="price">' + self.getProductPrice() + '</span>');
                giftCard.giftCardPrice.subscribe(function () {
                    $('#product-giftcard-price').html('<span class="price">' + self.getProductPrice() + '</span>');
                });
            },

            selectTemplate: function (data) {
                giftCard.selectedTemplate(data);
                giftCard.templatePreviewFile(data.template_file);
                giftCard.chooseFirstImageOfTemplate(data);
                if (data.notes) {
                    giftCard.notes(data.notes);
                } else {
                    giftCard.notes(window.giftvoucherConfig.notes);
                }
            },

            getProductPrice: ko.pureComputed(function () {
                var giftCardPrice = giftCard.giftCardPrice();
                if (window.giftvoucherConfig.giftAmount.type === 'dropdown') {
                    return giftCardHelper.getFormattedPrice(window.giftvoucherConfig.giftAmount.prices[giftCardPrice]);
                }
                else if (window.giftvoucherConfig.giftAmount.type === 'range') {
                    if (window.giftvoucherConfig.giftAmount.gift_price_type === 'percent') {
                        var percent = window.giftvoucherConfig.giftAmount.gift_price_options;
                        return giftCardHelper.getFormattedPrice(giftCardPrice * percent / 100);
                    } else if (window.giftvoucherConfig.giftAmount.gift_price_type === 'fixed') {
                        return giftCardHelper.getFormattedPrice(parseFloat(window.giftvoucherConfig.giftAmount.gift_price));
                    } else {
                        return giftCardHelper.getFormattedPrice(giftCardPrice);
                    }
                } else if (window.giftvoucherConfig.giftAmount.type === 'static') {
                    return giftCardHelper.getFormattedPrice(window.giftvoucherConfig.giftAmount.gift_price);
                }
            }),

            selectImage: function (id, data) {
                giftCard.selectImage(id, data);
            },

            isSelectCustomImage: ko.pureComputed(function () {
                return giftCard.isSelectCustomImage();
            })
        });
    }
);
