/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Giftvoucher/js/model/redeem/form'
    ],
    function ($, ko, Component, RedeemForm) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Giftvoucher/redeem/form'
            },
            existingCodes: RedeemForm.existingCodes,
            usingCodes: RedeemForm.usingCodes,
            selectedExistingCode: RedeemForm.selectedExistingCode,
            selectedNewCode: RedeemForm.selectedNewCode,
            hasExistingCodes: RedeemForm.hasExistingCodes,
            hasUsingCodes: RedeemForm.hasUsingCodes,
            useGiftcard: RedeemForm.useGiftcard,
            isBuyingGiftcard: RedeemForm.isBuyingGiftcard,
            isGuest: RedeemForm.isGuest,
            manageCodesUrl: RedeemForm.manageCodesUrl,
            checkCodesUrl: RedeemForm.checkCodesUrl,
            initialize: function () {
                this._super();
                var self = this;
                self.useGiftcard.subscribe(function(value){
                    if(value == false){
                        RedeemForm.removeAllCodes();
                    }
                });
                return self;
            },
            getExistingCodeTitle: function(giftCard){
                return giftCard.gift_code +' (' + giftCard.balance +')';
            },
            submit: function(){
                RedeemForm.apply();
            },
            removeUsingCode: function(giftCard){
                RedeemForm.removeUsingCode(giftCard.code);
            },
            scanAfter: function(data, event){
                if (event.keyCode == 13) {
                    event.preventDefault();
                    var elId = event.target.id;
                    $('#'+elId).change();
                    RedeemForm.apply();
                    return false;
                } else {
                    return true;
                }
            }
        });
    }
);
