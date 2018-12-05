/*
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/form/element/abstract'
], function (_, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Magestore_Giftvoucher/form/element/image-preview',
            width: '90px'
        },

        initialize: function() {
          this._super();
          this.src = this.mediaUrl + this.initialValue;
          return this;
        }
    });
});
