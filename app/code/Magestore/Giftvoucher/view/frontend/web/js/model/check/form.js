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
        var CheckForm = {
            hasCodeData: ko.observable(false),
            checkingCode: ko.observable(),
            canCheck: ko.observable(true),
            checkUrl: ko.observable(),
            code: ko.observable(),
            balance: ko.observable(),
            description: ko.observable(),
            status: ko.observable(),
            expiredAt: ko.observable(),
            initialize: function () {
                var self = this;
                return self;
            },
            initData: function(data){
                if(data){
                    var self = this;
                    data = (typeof data == 'string')?JSON.parse(data):data;
                    if(data.check_url){
                        self.checkUrl(data.check_url);
                    }
                    if(data.data){
                        var code = self.getCodeData(data.data, 'code');
                        var balance = self.getCodeData(data.data, 'balance');
                        var description = self.getCodeData(data.data, 'description');
                        var status = self.getCodeData(data.data, 'status');
                        var expiredAt = self.getCodeData(data.data, 'expired_at');
                        self.hasCodeData((code)?true:false);
                        self.code(code);
                        self.balance(balance);
                        self.description(description);
                        self.status(status);
                        self.expiredAt(expiredAt);
                    }
                    self.canCheck(data.can_check);
                }
            },
            getFormData: function(){
                var self = this;
                var data = {
                    code: self.checkingCode()
                }
                return data;
            },
            resetData: function(){
                var self = this;
            },
            check: function(){
                var self = this;
                if(self.checkUrl()){
                    var params = self.getFormData();
                    Request.send(self.checkUrl(), 'post', params).done(function(response){
                        self.initData(response);
                    });
                }
            },
            getCodeData: function(data, key){
                var self = this;
                var value = (data && key && (typeof data[key] != 'undefined'))?data[key]:data;
                return value;
            }
        };
        return CheckForm.initialize();
    }
);
