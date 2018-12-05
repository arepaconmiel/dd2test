/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Giftvoucher/js/model/request'
    ],
    function ($, ko, Request) {
        "use strict";
        var RedeemForm = {
            existingCodes: ko.observableArray(),
            usingCodes: ko.observableArray(),
            selectedExistingCode: ko.observable(),
            selectedNewCode: ko.observable(),
            useGiftcard: ko.observable(false),
            quoteId: ko.observable(),
            applyUrl: ko.observable(),
            removeUrl: ko.observable(),
            removeAllUrl: ko.observable(),
            initialize: function () {
                var self = this;
                self.hasExistingCodes = ko.pureComputed(function(){
                    return (self.existingCodes().length > 0)?true:false;
                });
                self.hasUsingCodes = ko.pureComputed(function(){
                    return (self.usingCodes().length > 0)?true:false;
                });
                return self;
            },
            initData: function(data){
                if(data){
                    var self = this;
                    data = JSON.parse(data);
                    if(data.quote_id){
                        self.quoteId(data.quote_id);
                    }
                    if(data.existing_codes){
                        self.existingCodes(data.existing_codes);
                    }
                    if(data.using_codes){
                        self.usingCodes(data.using_codes);
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
            apply: function(){
                var self = this;
                if(self.applyUrl()){
                    var params = self.getFormData();
                    Request.send(self.applyUrl(), 'post', params).done(function(response){
                        if (order) {
                            order.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true, {reset_shipping: 0});
                        }
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
                        if (order) {
                            order.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true, {reset_shipping: 0});
                        }
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
                        if (order) {
                            order.loadArea(['items', 'shipping_method', 'totals', 'billing_method'], true, {reset_shipping: 0});
                        }
                    });
                }
            }
        };
        return RedeemForm.initialize();
    }
);
