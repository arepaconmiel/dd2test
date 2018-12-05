/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Giftvoucher/js/model/request',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-list',
        'Magento_Checkout/js/action/get-payment-information',
    ],
    function ($, ko, Request, MessageList, PaymentService, PaymentMethodList, GetPaymentInformationAction ) {
        "use strict";
        var RedeemForm = {
            existingCodes: ko.observableArray(),
            usingCodes: ko.observableArray(),
            selectedExistingCode: ko.observable(),
            selectedNewCode: ko.observable(),
            useGiftcard: ko.observable(false),
            isBuyingGiftcard: ko.observable(false),
            isGuest: ko.observable(false),
            discountAmount: ko.observable(0),
            quoteId: ko.observable(),
            reloadFormUrl: ko.observable(),
            applyUrl: ko.observable(),
            removeUrl: ko.observable(),
            removeAllUrl: ko.observable(),
            manageCodesUrl: ko.observable(),
            checkCodesUrl: ko.observable(),
            initialize: function () {
                var self = this;
                self.hasExistingCodes = ko.pureComputed(function(){
                    return (self.existingCodes().length > 0)?true:false;
                });
                self.hasUsingCodes = ko.pureComputed(function(){
                    return (self.usingCodes().length > 0)?true:false;
                });
                self.hasUsingCodes.subscribe(function(value){
                    if(value == true){
                        self.useGiftcard(true);
                    }
                });
                return self;
            },
            initData: function(data){
                if(data){
                    var self = this;
                    data = (typeof data == 'string')?JSON.parse(data):data;
                    if(data.quote_id){
                        self.quoteId(data.quote_id);
                    }
                    if(data.gift_voucher_discount){
                        self.discountAmount(data.gift_voucher_discount);
                    }
                    if(data.existing_codes){
                        self.existingCodes(data.existing_codes);
                    }
                    if(data.using_codes){
                        self.usingCodes(data.using_codes);
                    }
                    if(data.reload_form_url){
                        self.reloadFormUrl(data.reload_form_url);
                    }
                    if(data.apply_url){
                        self.applyUrl(data.apply_url);
                    }
                    if(data.remove_url){
                        self.removeUrl(data.remove_url);
                    }
                    if(data.remove_all_url){
                        self.removeAllUrl(data.remove_all_url);
                    }
                    if(data.manage_codes_url){
                        self.manageCodesUrl(data.manage_codes_url);
                    }
                    if(data.check_codes_url){
                        self.checkCodesUrl(data.check_codes_url);
                    }
                    self.selectedExistingCode('');
                    self.selectedNewCode('');
                    self.isBuyingGiftcard(data.is_buying_giftcard);
                    self.isGuest(data.is_guest);

                    if(data.messages && $('#discount-giftcard-form.cart-page').length == 0){
                        var messages = data.messages;
                        if(messages.errors && messages.errors.length > 0){
                            $.each(messages.errors, function(index, message){
                                MessageList.addErrorMessage({'message': message});
                            });
                        }
                        if(messages.success && messages.success.length > 0){
                            $.each(messages.success, function(index, message){
                                MessageList.addSuccessMessage({'message': message});
                            });
                        }
                    }
                }
            },
            getFormData: function(){
                var self = this;
                var data = {
                    quote_id: self.quoteId(),
                    added_codes: [
                    ],
                    existed_code: self.selectedExistingCode(),
                    new_code: self.selectedNewCode()
                }
                if(self.usingCodes().length > 0){
                    $.each(self.usingCodes(), function(index, giftCode){
                        data.added_codes.push(giftCode);
                    });
                }
                return data;
            },
            resetData: function(){
                var self = this;
                self.selectedExistingCode('');
                self.selectedNewCode('');
            },
            refreshTotals: function(){
                var deferred = $.Deferred();
                var result = PaymentMethodList();
                GetPaymentInformationAction(deferred);
                if (result.length == 1 && result[0].method == 'free') {
                    // $.when(deferred).done(function () {
                        // Totals.isLoading(false);
                    // });
                } else {
                    $.when(deferred).done(function() {
                        PaymentService.setPaymentMethods(
                            PaymentMethodList()
                        );
                    });
                }
            },
            reload: function(){
                var self = this;
                if(self.reloadFormUrl()){
                    Request.send(self.reloadFormUrl(), 'post', {}).done(function(response){
                        self.initData(response);
                    });
                }
            },
            apply: function(){
                var self = this;
                if(self.applyUrl()){
                    var params = self.getFormData();
                    Request.send(self.applyUrl(), 'post', params).done(function(response){
                        self.initData(response);
                        self.refreshTotals();
                    });
                }
            },
            changeUsingCodeDiscount: function(code, discount){
                var self = this;
                if(code && self.usingCodes().length > 0){
                    $.each(self.usingCodes(), function(index, giftCode){
                        if(giftCode.code == code){
                            giftCode.discount = discount;
                        }
                    });
                }
            },
            removeUsingCode: function(code){
                var self = this;
                if(self.removeUrl() && code){
                    var params = {
                        quote_id: self.quoteId(),
                        gift_code: code
                    }
                    Request.send(self.removeUrl(), 'post', params).done(function(response){
                        self.initData(response);
                        self.refreshTotals();
                    });
                }
            },
            removeAllCodes: function(){
                var self = this;
                if(self.removeAllUrl()){
                    var params = {
                        quote_id: self.quoteId()
                    }
                    Request.send(self.removeAllUrl(), 'post', params).done(function(response){
                        self.initData(response);
                        self.refreshTotals();
                    });
                }
            }
        };
        return RedeemForm.initialize();
    }
);
