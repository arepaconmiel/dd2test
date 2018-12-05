/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'Magestore_Giftvoucher/js/model/product/giftcard',
        'mage/validation'
    ],
    function ($, Component, ko, giftCard) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Giftvoucher/product/view'
            },

            initialize: function () {
                this._super();
            },
            afterRender: function () {
                $('document').ready(function () {
                    $('#product-addtocart-button').click(function (e) {


                        var parent = $('.ajax-file-upload');
                        var html = [];
                        $('.ajax-file-upload form').each(function () {
                            var detach = $(this).detach();
                            html.push(detach);
                        });


                        $('#product_addtocart_form').validation({
                            ignore: "#giftcard-template-upload-images"
                        });
                        var valid = $('#product_addtocart_form').validation('isValid');
                        if (!valid) {
                            e.preventDefault();
                        }

                        $.each(html, function (index, value) {
                            parent.append(value);
                        });

                    })

                });


            }

        });
    }
);
