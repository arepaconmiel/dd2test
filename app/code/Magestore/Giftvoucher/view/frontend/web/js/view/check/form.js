/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Giftvoucher/js/model/check/form'
    ],
    function ($, ko, Component, CheckForm) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Giftvoucher/check/form'
            },
            hasCodeData: CheckForm.hasCodeData,
            checkingCode: CheckForm.checkingCode,
            canCheck: CheckForm.canCheck,
            code: CheckForm.code,
            balance: CheckForm.balance,
            description: CheckForm.description,
            status: CheckForm.status,
            expiredAt: CheckForm.expiredAt,
            initialize: function () {
                this._super();
                var self = this;
                self.hasDescription = ko.pureComputed(function(){
                    return (CheckForm.description())?true:false;
                });
                self.hasExpiredAt = ko.pureComputed(function(){
                    return (CheckForm.expiredAt())?true:false;
                });
            },
            submit: function(){
                CheckForm.check();
            },
            scanAfter: function(data, event){
                if (event.keyCode == 13) {
                    var elId = event.target.id;
                    $('#'+elId).change();
                    CheckForm.check();
                    return false;
                }
            }
        });
    }
);
