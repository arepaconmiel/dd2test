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
        'mage/translate',
        'Magestore_Giftvoucher/js/model/product/giftcard',
        'Magestore_Giftvoucher/js/helper/giftvoucher'
    ],
    function (Component, ko, $, $t, giftCard, giftvoucherHelper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: ko.computed(function () {
                    return giftCard.templatePreviewFile();
                })
            },

            barcodeUrl: ko.observable(window.giftvoucherConfig.barCodeUrl),
            logo_url: ko.observable(window.giftvoucherConfig.logo_url),
            notes: giftCard.notes,

            initialize: function () {
                this._super();
            },

            closePopup: function () {
                $('#popup-giftcard').hide();
                $('#bg-fade').hide();
            },


            recipientName: ko.pureComputed(function () {
                return giftCard.recipientName();
            }),

            senderName: ko.pureComputed(function () {
                return giftCard.senderName();
            }),
            giftImageUrl: ko.pureComputed(function () {
                if (giftCard.isTrue(giftCard.isSelectCustomImage())) {
                    return giftCard.customImageUrl();
                } else {
                    return giftCard.selectedImage();
                }

            }),

            expiredDate: ko.pureComputed(function () {
                return giftCard.expiredDate();
            }),

            giftMessage: ko.pureComputed(function () {
                if (giftCard.customMessage()) {
                    return giftCard.customMessage();
                } else {
                    return $t('Hope you enjoy this gift card!');
                }

            }),

            showPopup: function () {
                $('#popup-giftcard').show();
                $('#bg-fade').show();
            },

            giftValue: ko.pureComputed(function () {
                return giftvoucherHelper.getFormattedPrice(giftCard.giftCardPrice());
            }),

            giftCode: ko.pureComputed(function () {
                var hiddenChar = giftCard.hiddenCharacter();
                return 'GIFT-' + hiddenChar + hiddenChar + hiddenChar + hiddenChar + '-'
                    + hiddenChar + hiddenChar + hiddenChar + hiddenChar;
            }),

            styleColor: ko.pureComputed(function () {
                var selectedTemplate = giftCard.selectedTemplate();
                var color = selectedTemplate.style_color;
                color = color.replace('#', '');
                return '#' + color;
            }),

            textColor: ko.pureComputed(function () {
                var selectedTemplate = giftCard.selectedTemplate();
                var color = selectedTemplate.text_color;
                color = color.replace('#', '');
                return '#' + color;
            })
        });
    }
);
