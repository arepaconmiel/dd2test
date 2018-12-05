/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    "jquery",
    "prototype",
    "mage/translate",
    "jquery/ui"
], function ($j) {
    window.giftCard = {
        giftcard_prev : 0,
        giftcard_next : 4,

        initialize: function () {
            day_to_send_error = 'We cannot send a Gift Card on a date in the past. Please choose the sending date again.';
            Validation.add('validate-date-giftcard', day_to_send_error, function (v) {
                if (Validation.get('validate-date').test(v)) {
                    var test = new Date(v);
                    var today = getTodayDate();
                    if (test < today)
                        return false;
                }
                return true;
            });
        },

        hideTemplateImages: function () {
            if ($j('#select-gift').get(0).selected == true) {
                $j('#gift-image-carosel').hide();
            } else {
                $j('#gift-image-carosel').show();
            }
        },

        loadGiftCard: function (templates) {
            if (($j('#select-gift').length > 0) && ($j('#select-gift').val())) {
                this.changeTemplate($('select-gift'), templates);
            }
        },

        sendFriend: function (el) {
            if (!el)
                return;
            var receiver = $('giftvoucher-receiver');
            if (el.checked) {
                if (receiver) {
                    receiver.show();
                    $j('.giftvoucher-receiver').show();
                    if ($j('#recipient_name').length > 0)
                        $j('#recipient_name').addClass('required-entry');
                    if ($j('#recipient_email').length > 0) {
                        $j('#recipient_email').addClass('required-entry');
                        $j('#recipient_email').addClass('validate-email');
                        $j('#recipient_email').addClass('validate-same-email');
                    }
                    if ($j('#day_to_send').length > 0) {
                        $j('#day_to_send').addClass('required-entry');
                        $j('#day_to_send').addClass('validate-date');
                        $j('#day_to_send').addClass('validate-date-giftcard');
                    }
                }
            } else {
                if (receiver) {
                    if ($j('#recipient_email').length > 0) {
                        $j('#recipient_email').removeClass('required-entry');
                        $j('#recipient_email').removeClass('validate-email');
                        $j('#recipient_email').removeClass('validate-same-email');
                    }
                    receiver.hide();
                    if ($j('#recipient_name').length > 0)
                        $j('#recipient_name').removeClass('required-entry');

                    if ($j('#day_to_send').length > 0) {
                        $j('#day_to_send').removeClass('required-entry');
                        $j('#day_to_send').removeClass('validate-date');
                        $j('#day_to_send').removeClass('validate-date-giftcard');
                    }
                }
            }
        },


        changeTemplate: function (el) {
            this.template_id = this.getTemplateById(el.value, templates);
            if (typeof image_for_old !== 'undefined')
                $(image_for_old).hide();
            if (typeof image_form_data === 'undefined') {
                image_for_old = 'div-bound-' + templates[this.template_id].giftcard_template_id + '-0';
                if ($(image_for_old))
                    $(image_for_old).show();
                this.giftcard_prev = 0;
                this.giftcard_next = 4;
            } else
                image_for_old = 'div-bound-' + templates[this.template_id].giftcard_template_id + '-' + (image_form_data - image_form_data % 4);
            if ($(image_for_old))
                $(image_for_old).show();
            if (templates[this.template_id].images)
                count_next_fix = templates[this.template_id].images.split(',').length;
            else
                count_next_fix = 0;

            if (this.giftcard_next >= count_next_fix)
                $j('#giftcard-template-next').hide();
            else
                $j('#giftcard-template-next').show();
            if (this.giftcard_prev <= 0)
                $j('#giftcard-template-prev').hide();
            else
                $j('#giftcard-template-prev').show();

            if (typeof image_form_data !== 'undefined') {
                this.changeSelectImages(image_form_data);
                delete image_form_data;
            } else {
                this.changeSelectImages(0);
            }
        },
        getTemplateById: function (id, templates) {
            for (i = 0; i < templates.length; i++) {
                if (templates[i].giftcard_template_id === id)
                    return i;
            }
            return 0;
        },
        changeSelectImages: function (image_id) {
            if (typeof this.image_old != 'undefined') {
                $j('#div-' + this.image_old).removeClass('gift-active');
                $j('#div-' + this.image_old).find('.egcSwatch-arrow').hide();
            }
            if ($j('#image-for-' + templates[this.template_id].giftcard_template_id + '-' + image_id).length > 0) {
                this.image_old = 'image-for-' + templates[this.template_id].giftcard_template_id + '-' + image_id;
                $j('#div-' + this.image_old).addClass('gift-active');

                $j('#div-image-for-' + templates[this.template_id].giftcard_template_id + '-' + image_id).find('.egcSwatch-arrow').show();
                image = $(this.image_old).src;

                images_tmp = templates[this.template_id].images;
                if (images_tmp != null) {
                    images_tmp = images_tmp.split(',');
                    $j('#giftcard-template-images').val(images_tmp[image_id]);
                }
            }
        },
        giftcardPrevImage: function () {
            if (this.giftcard_prev === 0)
                return;
            if (typeof image_for_old !== 'undefined')
                $(image_for_old).hide();
            this.giftcard_prev = this.giftcard_prev - 4;
            this.giftcard_next = this.giftcard_next - 4;
            image_for_old = 'div-bound-' + templates[this.template_id].giftcard_template_id + '-' + this.giftcard_prev;
            $(image_for_old).show();
            if (this.giftcard_prev === 0)
                $j('#giftcard-template-prev').hide();
            if (this.giftcard_next < templates[this.template_id].images.split(',').length)
                $j('#giftcard-template-next').show();
        },
        giftcardNextImage: function () {
            if (this.giftcard_next >= templates[this.template_id].images.split(',').length)
                return;
            if (typeof image_for_old !== 'undefined')
                $(image_for_old).hide();
            this.giftcard_next = this.giftcard_next + 4;
            this.giftcard_prev = this.giftcard_prev + 4;
            image_for_old = 'div-bound-' + templates[this.template_id].giftcard_template_id + '-' + this.giftcard_prev;
            $(image_for_old).show();
            if (this.giftcard_next >= templates[this.template_id].images.split(',').length)
                $j('#giftcard-template-next').hide();
            if (this.giftcard_prev > 0)
                $j('#giftcard-template-prev').show();
        },
        changeRemaining: function (el, remaining_max) {
            if (el.value.length > remaining_max) {
                el.value = el.value.substring(0, remaining_max);
            }
            $j('#giftvoucher_char_remaining').html(remaining_max - el.value.length);
        },

        shipToFriend: function (el, check) {
            if (el !== null && el.checked) {
                if ($j('#recipient_email').length > 0)
                    $j('#recipient_email').removeClass('required-entry');
                if ($j('#recipient_ship_desc').length > 0)
                    $j('#recipient_ship_desc').show();
            } else {

                if ($j('#recipient_ship_desc').length > 0)
                    $j('#recipient_ship_desc').hide();
            }
        },
        validateInputRange: function (el, from, to, priceFormat, gift_price_type, gift_price) {
            var result = [];
            price = priceFormat.match('1.000.00')[0];
            result['decimalSymbol'] = price.charAt(5);
            result['groupSymbol'] = price.charAt(1);

            var gift_amount_min = from;
            var gift_amount_max = to;

            validateValue = el.value.replace(/\s/g, '');
            if (validateValue.search(result.groupSymbol) !== -1)
                validateValue = validateValue.replace(result.groupSymbol, '');
            el.value = validateValue.replace(result.decimalSymbol, '.');
            $j('#amount_range').val(el.value);

            if (el.value < gift_amount_min)
                el.value = gift_amount_min;
            if (el.value > gift_amount_max)
                el.value = gift_amount_max;
            if (gift_price_type === '3') {
                var newPrice = el.value * gift_price / 100;
                $j("#product_composite_configure_input_qty").attr('price', newPrice);
            } else {
                $j("#product_composite_configure_input_qty").attr('price', el.value);
            }


        },

        setAmountRange: function (amount) {
            if (!$j('#amount_range').val())
                $j('#amount_range').val(amount);
            $j("#product_composite_configure_input_qty").attr('price', amount);
        },

        setAmountDropDown: function (el, gift_price_type, gift_price) {
            var input_hidden;
            if (gift_price_type === '3') {
                var newPrice = el.value * gift_price / 100;
                input_hidden = '<input id="abc" type="hidden" value="1" price="' + newPrice + '" qtyid="product_composite_configure_input_qty">';
            } else if (gift_price_type === '2') {

                // convert string to array of value and price
                var priceArray = gift_price.split(",");
                if (priceArray[el.selectedIndex]) {
                    input_hidden = '<input id="abc" type="hidden" value="1" price="' + priceArray[el.selectedIndex] + '" qtyid="product_composite_configure_input_qty">';
                }
            } else {
                    input_hidden = '<input id="abc" type="hidden" value="1" price="' + el.value + '" qtyid="product_composite_configure_input_qty">';
            }
            $j("#abc").remove();
            $j("#catalog_product_composite_configure_fields_giftvoucher").append(input_hidden);
        }
    };

    return window.giftCard;


});