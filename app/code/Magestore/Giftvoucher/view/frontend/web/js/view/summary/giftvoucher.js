/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magestore_Giftvoucher/js/model/redeem/form',
        'ko'
    ],
    function (Component, RedeemModel, ko) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Giftvoucher/summary/giftvoucher'
            },
            initialize: function () {
                this._super();
                var self = this;
                self.isVisible = ko.pureComputed(function(){
                    return (self.getPureValue() > 0)?true:false;
                });
                return self;
            },
            getPureValue: function() {
                return RedeemModel.discountAmount();
            },
            getValue: function() {
                return this.getFormattedPrice(-this.getPureValue());
            },
        });
    }
);
