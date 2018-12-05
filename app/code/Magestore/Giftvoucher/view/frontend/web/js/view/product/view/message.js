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
        'Magestore_Giftvoucher/js/helper/giftvoucher',
        'jquery/ui',
        'mage/calendar'
    ],
    function ($, Component, ko, giftCard, giftVoucherHelper) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Magestore_Giftvoucher/product/view/message'
            },

            initialize: function () {
                this._super();
            },

            timezones: ko.observable(window.giftvoucherConfig.timezones),
            selectedTimezone: ko.computed(function () {
                return giftCard.selectedTimezone();
            }),
            sendToFriend: ko.computed(function () {
                return giftCard.sendToFriend();
            }),

            setSendToFriend: function (data,event) {
                giftCard.sendToFriend(event.target.checked);
            },

            setIsGetNotificationEmail: function (data,event) {
                giftCard.isGetNotificationEmail(event.target.checked);
            },

            sendToPostal: ko.observable(window.giftvoucherConfig.defaultCheckedPostal),
            postOfficeDate: ko.observable(window.giftvoucherConfig.postOfficeDate),
            isPhysicalType: ko.pureComputed(function () {
                 if (window.giftvoucherConfig.giftCardType === '1') {
                     return true;
                 } else {
                     return false;
                 }
            }),
            isVirtualType: ko.pureComputed(function () {
                if (window.giftvoucherConfig.giftCardType === '2') {
                    return true;
                } else {
                    return false;
                }
            }),
            isCombineType: ko.pureComputed(function () {
                if (window.giftvoucherConfig.giftCardType === '3') {
                    return true;
                } else {
                    return false;
                }
            }),
            senderName: ko.pureComputed(function () {
                return giftCard.senderName();
            }),
            recipientName: ko.pureComputed(function () {
                return giftCard.recipientName();
            }),
            recipientEmail: ko.pureComputed(function () {
                return giftCard.recipientEmail();
            }),
            customMessage: ko.pureComputed(function () {
                return giftCard.customMessage();
            }),

            dayToSend: ko.pureComputed(function () {
                return giftCard.dayToSend();
            }),

            isGetNotificationEmail: ko.pureComputed(function () {
                return giftCard.isGetNotificationEmail();
            }),

            initDate: function () {
                var currentDate = new Date();
                var year = currentDate.getFullYear();
                var month = currentDate.getMonth();
                var day = currentDate.getDate();
                var self = this;
                $("#day_to_send").calendar({
                    dateFormat: "mm/dd/yy",
                    minDate: new Date(),
                    showOn: "button",
                    showAnim: "",
                    changeMonth: true,
                    changeYear: true,
                    buttonImageOnly: null,
                    buttonImage: null,
                    showButtonPanel: true,
                    showWeek: true,
                    timeFormat: '',
                    showTime: false,
                    showHour: false,
                    showMinute: false
                });
            },

            setSenderName: function (data,event) {
                giftCard.senderName(event.target.value);
            },

            setCustomMessage: function (data,event) {
                var length = event.target.value.length;
                if (length <= window.giftvoucherConfig.messageMaxLength) {
                    giftCard.customMessage(event.target.value);
                } else {
                    giftCard.customMessage(event.target.value.substring(0, window.giftvoucherConfig.messageMaxLength-1))
                }

            },

            setRecipientName: function (data,event) {
                giftCard.recipientName(event.target.value);
            },

            setRecipientEmail: function (data, event) {
                giftCard.recipientEmail(event.target.value);
            },

            setDayToSend: function (data, event) {
                giftCard.dayToSend(event.target.value);
            },

            characterRemaining: ko.computed(function () {
                var customMessage = giftCard.customMessage();
                var customMessageLength = customMessage.length;
                return (window.giftvoucherConfig.messageMaxLength - customMessageLength);
            }),

            showPopup: function () {
                $("html, body").animate({ scrollTop: 0 }, "slow");
                giftVoucherHelper.showPopup();
            }
        });
    }
);
