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
            getExistingCodeTitle: function(giftCard){
                return giftCard.gift_code +' (' + giftCard.balance +')';
            },
            submit: function(){
                RedeemForm.apply();
            },
            initEvents: function(){
                var activeCheckbox = $('#giftvoucher_active');
                var formContainer = $('#giftvoucher_form_container');
                var submitButton = $('#giftvoucher_submit_button');
                var usingCodes = $('.giftvoucher_using_codes .using_code_container');
                var existingCodeSelect = $('#giftvoucher_existed_code');
                var newCodeInput = $('#giftvoucher_new_code');
                var submitOnEnterElement = $('.giftvoucher_submit_on_enter');
                var self = this;
                submitOnEnterElement.keydown(function(event) {
                    if (event.keyCode == 13) {
                        $(this).change();
                        RedeemForm.apply();
                        return false;
                    }
                });
                if(activeCheckbox.is(":checked")){
                    formContainer.removeClass('hide');
                }
                submitButton.click(function(){
                    self.submit();
                });
                activeCheckbox.change(function(){
                    var checked = $(this).is(":checked");
                    RedeemForm.useGiftcard(checked);
                    if(checked) {
                        formContainer.removeClass('hide');
                    }else{
                        formContainer.addClass('hide');
                        RedeemForm.resetData();
                        if(RedeemForm.usingCodes().length > 0){
                            RedeemForm.removeAllCodes();
                        }
                    }
                });
                newCodeInput.change(function(){
                    RedeemForm.selectedNewCode($(this).val());
                });
                if(existingCodeSelect.length > 0){
                    existingCodeSelect.change(function(){
                        RedeemForm.selectedExistingCode($(this).val());
                    });
                }
                if(usingCodes.length > 0){
                    usingCodes.each(function(){
                        var usingCode = $(this);
                        var usingCodeInput = usingCode.find('.using_code');
                        var editCodeButton = usingCode.find('.edit_button');
                        var removeCodeButton = usingCode.find('.remove_button');
                        removeCodeButton.click(function(){
                            var gift_code = $(this).data('gift_code');
                            RedeemForm.removeUsingCode(gift_code);
                        });
                        usingCodeInput.change(function(){
                            var gift_code = $(this).data('gift_code');
                            var discount = $(this).val();
                            RedeemForm.changeUsingCodeDiscount(gift_code, discount);
                        });
                    });
                }
            }
        });
    }
);
