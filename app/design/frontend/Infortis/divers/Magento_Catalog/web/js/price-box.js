/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'underscore',
    'mage/template',
    'mage/translate',
    'jquery/ui'
], function ($, utils, _, mageTemplate, $t) {
    'use strict';

    var globalOptions = {
        productId: null,
        priceConfig: null,
        prices: {},
        priceTemplate: '<span class="price"><%- data.formatted %></span>',
        rangeTemplate: '<span class="price"><%- first_price %> - <%- second_price %></span>'
    };

    $.widget('mage.priceBox', {
        options: globalOptions,

        /**
         * Widget initialisation.
         * Every time when option changed prices also can be changed. So
         * changed options.prices -> changed cached prices -> recalculation -> redraw price box
         */
        _init: function initPriceBox() {
            var box = this.element;

            box.trigger('updatePrice');
            this.cache.displayPrices = utils.deepClone(this.options.prices);
        },

        /**
         * Widget creating.
         */
        _create: function createPriceBox() {
            var box = this.element;

            this.cache = {};
            this._setDefaultsFromPriceConfig();
            this._setDefaultsFromDataSet();

            box.on('reloadPrice', this.reloadPrice.bind(this));
            box.on('updatePrice', this.onUpdatePrice.bind(this));

            var priceFormat = (this.options.priceConfig && this.options.priceConfig.priceFormat) || {},
                priceTemplate = mageTemplate(this.options.priceTemplate);

            var rangeTemplate = mageTemplate(this.options.rangeTemplate);

            if(typeof this.options.priceConfig.qxd_price != 'undefined') 
            {
                var $custom_price = this.options.priceConfig.qxd_price.data

                if($custom_price){
                    //regular
                    var regular_range_template = '';
                    if($custom_price['hasSameRegularPrice']){

                        var aux_price = {}
                        aux_price.formatted = utils.formatPrice($custom_price['rangeRegular']['lower'], priceFormat);
                        regular_range_template = priceTemplate({
                                data: aux_price,
                        });
                    }else{

                        var first_price = utils.formatPrice($custom_price['rangeRegular']['lower'], priceFormat);
                        var second_price = utils.formatPrice($custom_price['rangeRegular']['higher'], priceFormat);

                        regular_range_template = rangeTemplate({
                            first_price: first_price,
                            second_price: second_price 
                        });
                    }
                        
                    //special
                    if($custom_price['hasSpecialPrice']){
                        var special_range_template = '';
                        if($custom_price['hasSameSpecialPrice']){

                            var aux_price = {}
                            aux_price.formatted = utils.formatPrice($custom_price['rangeSpecial']['lower'], priceFormat);
                            special_range_template = priceTemplate({
                                    data: aux_price,
                            });
                        }else{

                            var first_price = utils.formatPrice($custom_price['rangeSpecial']['lower'], priceFormat);
                            var second_price = utils.formatPrice($custom_price['rangeSpecial']['higher'], priceFormat);

                            special_range_template = rangeTemplate({
                                first_price: first_price,
                                second_price: second_price
                            });
                        }  
                    }else{
                        $('[data-price-type="finalPrice-special-range"]', this.element).hide();
                        // ocultar label de regular
                    }

                    if(regular_range_template!=''){
                        $('[data-price-type="finalPrice-regular-range"]', this.element).html(regular_range_template);
                    }
                    if(special_range_template!=''){
                        $('[data-price-type="finalPrice-special-range"]', this.element).html(special_range_template);
                    }
                    //$('[data-price-type="oldPrice-regular-range"]', this.element).hide();
                    //$('[data-price-type="oldPrice-special-range"]', this.element).hide();

                    $('[data-price-type="finalPrice"]', this.element).hide();

                }
            }
        },

        /**
         * Call on event updatePrice. Proxy to updatePrice method.
         * @param {Event} event
         * @param {Object} prices
         */
        onUpdatePrice: function onUpdatePrice(event, prices) {
            return this.updatePrice(prices);
        },

        /**
         * Updates price via new (or additional values).
         * It expects object like this:
         * -----
         *   "option-hash":
         *      "price-code":
         *         "amount": 999.99999,
         *         ...
         * -----
         * Empty option-hash object or empty price-code object treats as zero amount.
         * @param {Object} newPrices
         */
        updatePrice: function updatePrice(newPrices) {
            var prices = this.cache.displayPrices,
                additionalPrice = {},
                pricesCode = [],
                priceValue, origin, finalPrice;

            this.cache.additionalPriceObject = this.cache.additionalPriceObject || {};

            if (newPrices) {
                $.extend(this.cache.additionalPriceObject, newPrices);
            }

            if (!_.isEmpty(additionalPrice)) {
                pricesCode = _.keys(additionalPrice);
            } else if (!_.isEmpty(prices)) {
                pricesCode = _.keys(prices);
            }

            _.each(this.cache.additionalPriceObject, function (additional) {
                if (additional && !_.isEmpty(additional)) {
                    pricesCode = _.keys(additional);
                }
                _.each(pricesCode, function (priceCode) {
                    priceValue = additional[priceCode] || {};
                    priceValue.amount = +priceValue.amount || 0;
                    priceValue.adjustments = priceValue.adjustments || {};

                    additionalPrice[priceCode] = additionalPrice[priceCode] || {
                            'amount': 0,
                            'adjustments': {}
                        };
                    additionalPrice[priceCode].amount =  0 + (additionalPrice[priceCode].amount || 0) +
                        priceValue.amount;
                    _.each(priceValue.adjustments, function (adValue, adCode) {
                        additionalPrice[priceCode].adjustments[adCode] = 0 +
                            (additionalPrice[priceCode].adjustments[adCode] || 0) + adValue;
                    });
                });
            });

            if (_.isEmpty(additionalPrice)) {
                this.cache.displayPrices = utils.deepClone(this.options.prices);
            } else {
                _.each(additionalPrice, function (option, priceCode) {
                    origin = this.options.prices[priceCode] || {};
                    finalPrice = prices[priceCode] || {};
                    option.amount = option.amount || 0;
                    origin.amount = origin.amount || 0;
                    origin.adjustments = origin.adjustments || {};
                    finalPrice.adjustments = finalPrice.adjustments || {};

                    finalPrice.amount = 0 + origin.amount + option.amount;
                    _.each(option.adjustments, function (pa, paCode) {
                        finalPrice.adjustments[paCode] = 0 + (origin.adjustments[paCode] || 0) + pa;
                    });
                }, this);
            }

            /* qxd */

            this.cache['current_qxd_data'] = null;
            //case swatches
            if(
                newPrices && 
                typeof newPrices.prices != 'undefined' &&
                typeof newPrices.prices.currentOption != 'undefined'
            ){
                this.cache['current_qxd_data'] = newPrices.prices.currentOption;
            }else{
                if(newPrices){
                    for (var obj in newPrices) {
                        if (typeof newPrices[obj].currentOption != 'undefined') {
                           this.cache['current_qxd_data'] = newPrices[obj].currentOption; 
                        }
                    }
                }
            }


            this.element.trigger('reloadPrice');
        },

        /*eslint-disable no-extra-parens*/
        /**
         * Render price unit block.
         */
        reloadPrice: function reDrawPrices() {
            var priceFormat = (this.options.priceConfig && this.options.priceConfig.priceFormat) || {},
                priceTemplate = mageTemplate(this.options.priceTemplate);

            var rangeTemplate = mageTemplate(this.options.rangeTemplate);

            _.each(this.cache.displayPrices, function (price, priceCode) {
                price.final = _.reduce(price.adjustments, function (memo, amount) {
                    return memo + amount;
                }, price.amount);

                price.formatted = utils.formatPrice(price.final, priceFormat);

                $('[data-price-type="' + priceCode + '"]', this.element).html(priceTemplate({
                    data: price
                }));
            }, this);

            var currentOpt = this.cache['current_qxd_data'];
            if(currentOpt){

                //an option has selected
                //select the label for the price
                var opt = currentOpt.currentOption;

                if(opt.oldPrice.amount != opt.finalPrice.amount){
                    //option with spaceila price
                    $('[custom-label="oldPrice"]', this.element).html($t("Regular Price:"));
                    $('[custom-label="finalPrice"]', this.element).html($t("Special Price:"));

                }else{
                    $('[custom-label="oldPrice"]', this.element).html($t("Special Price:"));
                    $('[custom-label="finalPrice"]', this.element).html($t("Regular Price:"));
                }
            }
        },

        /*eslint-enable no-extra-parens*/
        /**
         * Overwrites initial (default) prices object.
         * @param {Object} prices
         */
        setDefault: function setDefaultPrices(prices) {
            this.cache.displayPrices = utils.deepClone(prices);
            this.options.prices = utils.deepClone(prices);
        },

        /**
         * Custom behavior on getting options:
         * now widget able to deep merge of accepted configuration.
         * @param  {Object} options
         * @return {mage.priceBox}
         */
        _setOptions: function setOptions(options) {
            $.extend(true, this.options, options);

            if ('disabled' in options) {
                this._setOption('disabled', options.disabled);
            }

            return this;
        },

        /**
         * setDefaultsFromDataSet
         */
        _setDefaultsFromDataSet: function _setDefaultsFromDataSet() {
            var box = this.element,
                priceHolders = $('[data-price-type]', box),
                prices = this.options.prices;

            this.options.productId = box.data('productId');

            if (_.isEmpty(prices)) {
                priceHolders.each(function (index, element) {
                    var type = $(element).data('priceType'),
                        amount = parseFloat($(element).data('priceAmount'));

                    if (type && !_.isNaN(amount)) {
                        prices[type] = {
                            amount: amount
                        };
                    }
                });
            }
        },

        /**
         * setDefaultsFromPriceConfig
         */
        _setDefaultsFromPriceConfig: function _setDefaultsFromPriceConfig() {
            var config = this.options.priceConfig;

            if (config && config.prices) {
                this.options.prices = config.prices;
            }
        }
    });

    return $.mage.priceBox;
});
