/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'ko',
        'jquery'
    ], function (ko, $) {
        return {
            senderName: ko.observable(window.giftvoucherConfig.additionalInfo.customer_name),
            customMessage: ko.observable(window.giftvoucherConfig.additionalInfo.message),
            selectedTemplate: ko.observable(window.giftvoucherConfig.additionalInfo.giftcard_template_id),
            selectedImage: ko.observable(window.giftvoucherConfig.additionalInfo.giftcard_template_image),
            selectedTemplateImage: ko.observable(window.giftvoucherConfig.additionalInfo.giftcard_template_id),
            recipientName: ko.observable(window.giftvoucherConfig.additionalInfo.recipient_name),
            recipientEmail: ko.observable(window.giftvoucherConfig.additionalInfo.recipient_email),
            selectedTimezone: ko.observable(window.giftvoucherConfig.additionalInfo.timezone_to_send),

            expiredDate: ko.observable(window.giftvoucherConfig.expireDay),
            imageSelectedValue: ko.observable(''),
            templatePreviewFile: ko.observable(''),
            notes: ko.observable(window.giftvoucherConfig.notes),

            dayToSend: ko.observable(window.giftvoucherConfig.additionalInfo.day_to_send),

            choosePrice: ko.observable(window.giftvoucherConfig.additionalInfo.amount),
            giftCardPrice: ko.observable(window.giftvoucherConfig.additionalInfo.amount),
            hiddenCharacter: ko.observable(window.giftvoucherConfig.settings.general.hiddenchar),
            isSelectCustomImage: ko.observable(window.giftvoucherConfig.additionalInfo.giftcard_use_custom_image),
            isShowCustomImage: ko.observable(window.giftvoucherConfig.additionalInfo.giftcard_use_custom_image),

            customImageUrl: ko.observable(window.giftvoucherConfig.customImageBaseUrl + '/' + window.giftvoucherConfig.additionalInfo.giftcard_template_image),

            sendToFriend: ko.observable(window.giftvoucherConfig.defaultCheckedSender),
            isGetNotificationEmail: ko.observable(window.giftvoucherConfig.defaultNotifySuccess),

            isTrue: function(value) {
                if (typeof(value) == 'string') {
                    value = value.toLowerCase();
                }
                switch (value) {
                    case true:
                    case "true":
                    case 1:
                    case "1":
                    case "on":
                    case "yes":
                        return true;
                    default:
                        return false;
                }
            },
            chooseFirstImageOfTemplate: function (selectedTemplate) {
                var images = selectedTemplate.images;
                if (images) {
                    var imageArray = images.split(',');
                    var imageArrayUrl = [];
                    $.each(imageArray, function (index, value) {
                        imageArrayUrl.push(window.giftvoucherConfig.imageBaseUrl + '/' + value);
                    });
                    this.selectedImage(imageArrayUrl[0]);
                    this.selectedTemplateImage(selectedTemplate.giftcard_template_id + '-' + imageArrayUrl[0]);
                    this.isSelectCustomImage(0);
                    this.isShowCustomImage(false);
                    this.customImageUrl('');
                } else {
                    this.selectedImage('');
                    this.selectedTemplateImage('');
                    this.isSelectCustomImage(0);
                    this.isShowCustomImage(false);
                    this.customImageUrl('');
                }
            },

            selectImage: function (id, data) {
                this.selectedImage(data);
                this.selectedTemplateImage(id);
                this.isSelectCustomImage(0);
            }
        }
    }
);