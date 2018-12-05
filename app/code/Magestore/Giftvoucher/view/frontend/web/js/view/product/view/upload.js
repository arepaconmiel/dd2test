/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'Magestore_Giftvoucher/js/model/product/giftcard',
        "jquery/ui",
        "magestore/uploadfile",
        "magestore/jqueryform"
    ],
    function ($, Component, ko, giftCard) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Giftvoucher/product/view/upload'
            },

            customImageUrl: ko.pureComputed(function() {
                return giftCard.customImageUrl();
            }),

            isShowCustomImage: ko.pureComputed(function() {
                return giftCard.isShowCustomImage();
            }),

            maximumFileSize: ko.observable(window.giftvoucherConfig.settings.interface.upload_max_size),

            initialize: function () {
                this._super();
            },

            afterRender: function () {
                var self = this;
                $("#eventsupload").uploadFile({
                    id: "giftcard_custom_uploadimage",
                    url: window.giftvoucherConfig.uploadUrl,
                    multiple: false,
                    allowedTypes: "png,gif,jpg,jpeg",
                    maxFileSize: self.maximumFileSize()* 1024,
                    showDone: false,
                    fileName: "templateimage",
                    onSubmit: function (files) {
                    },
                    onSuccess: function (files, data, xhr) {
                        var json_data = $.parseJSON(data);
                        var urlUploadImage = json_data['url'];
                        giftCard.selectedImage(json_data['file']);
                        giftCard.isSelectCustomImage(true);
                        giftCard.isShowCustomImage(true);
                        giftCard.customImageUrl(urlUploadImage);
                        $('.ajax-file-upload-statusbar').delay(1000).fadeOut(500);
                    },
                    afterUploadAll: function () {

                    },
                    onError: function (files, status, errMsg) {
                    }
                });
            },

            isSelectCustomImage: ko.pureComputed(function () {
                return giftCard.isSelectCustomImage();
            }),

            chooseCustomImage: function () {
                giftCard.selectedImage(this.customImageUrl());
                giftCard.isSelectCustomImage(true);
            }
        });
    }
);
