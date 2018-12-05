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
            elementTmpl: 'Magestore_Giftvoucher/form/element/image-select',
            tracks: {
              templateId: true
            },
            listens: {
              templateId: 'templateIdChanged'
            },
            width: '90px'
        },

        /**
         * Select image on initialization
         *
         * @returns {*|void|Element}
         */
        initialize: function() {
          this._super();

          // process click image event
          var self = this;
          self.clickImage = function(image) {
            self.value(image);
            return true;
          };

          return this;
        },

        /**
         * Select default image on update
         *
         * @returns void
         */
        templateIdChanged: function () {
          if (this.templateId && this.templateImages && this.templateImages[this.templateId]) {
            var templates = this.templateImages[this.templateId];
            if (-1 !== _.indexOf(templates, this.initialValue)) {
              this.value(this.initialValue);
            } else if (_.first(templates)) {
              this.value(_.first(templates));
            }
          }
        }
    });
});
