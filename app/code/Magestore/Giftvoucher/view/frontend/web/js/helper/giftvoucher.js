/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(
    [
        'jquery',
        'ko',
        'Magento_Catalog/js/price-utils'
    ],
    function ($, ko, priceUtils) {
        'use strict';
        return {
            showPopup: function () {
                $('#popup-giftcard').show();
                $('#bg-fade').show();
            },

            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, window.giftvoucherConfig.priceFormat);
            },
        }
    }
);
