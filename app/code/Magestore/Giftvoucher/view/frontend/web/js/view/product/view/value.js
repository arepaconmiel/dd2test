/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'uiComponent',
        'ko',
        'Magestore_Giftvoucher/js/model/product/giftcard',
        'Magento_Catalog/js/price-utils',
        'Magento_Ui/js/modal/alert',
        'mage/translate'
    ],
    function (Component, ko, giftCard, priceUtils, alert, $t) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Giftvoucher/product/view/value'
            },

            initialize: function () {
                this._super();
                if (this.priceType() === 'dropdown' && !giftCard.giftCardPrice()) {
                    var giftAmount = this.giftAmount().options;
                    this.setDropdownPrice(giftAmount[0]);
                }
                if (this.priceType() === 'static' && !giftCard.giftCardPrice()) {
                    this.setPrice(this.giftAmount().value);
                }

                if (this.priceType() === 'range' && !giftCard.giftCardPrice()) {
                    this.setPrice(this.giftAmount().from);
                }
            },
            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, window.giftvoucherConfig.priceFormat);
            },
            priceType: ko.observable(window.giftvoucherConfig.giftAmount.type),
            giftAmount: ko.observable(window.giftvoucherConfig.giftAmount),
            choosePrice: ko.pureComputed(function () {
                return giftCard.choosePrice();
            }),
            setDropdownPrice: function (data) {
                giftCard.choosePrice(data);
                giftCard.giftCardPrice(data);
            },
            setRangePrice: function (data,event) {
                if ((event.target.value >= this.giftAmount().from) && (event.target.value <= this.giftAmount().to)) {
                    giftCard.giftCardPrice(event.target.value);
                } else {
                    event.target.value = this.giftAmount().from;
                    alert({
                        content: $t('Please choose the value correctly!')
                    });
                }
            },

            setPrice: function (data) {
                giftCard.giftCardPrice(data);
            },

            giftCardPrice: ko.pureComputed(function () {
                return giftCard.giftCardPrice();
            })
        });
    }
);
