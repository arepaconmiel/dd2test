/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/abstract',
    'Magestore_Giftvoucher/js/jscolor/jscolor.min'
], function (Abstract) {
    'use strict';
 
    return Abstract.extend({
        defaults: {
            elementTmpl: 'Magestore_Giftvoucher/form/element/color-input'
        },
        
        afterRender: function() {
            window.jscolor.installByClassName('jscolor');
        }
    });
});
