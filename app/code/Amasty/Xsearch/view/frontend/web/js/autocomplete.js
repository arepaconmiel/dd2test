define([
    'quickSearch',
    'jquery',
    'underscore',
    'mage/template',
    'jquery/ui'
], function (quickSearch, $, _, mageTemplate) {
    'use strict';

    var autoComplete = {
        ajaxRequest: null,
        url: null,
        timer: null,
        delay: 500,
        currentUrlEncoded: null,
        minSizePopup: 730,
        mobileView: 768,

        init: function (url, layout, currentUrlEncoded) {
            this.url = url;
            this.layout = layout;
            this.currentUrlEncoded = currentUrlEncoded;
            this.extend();
        },

        extend: function () {
            var _caller = this;
            var methods = {
                options: {
                    amAutoComplete: _caller,
                    responseFieldElements: '.amsearch-item',
                    minChars: this.layout.minChars
                },

                _onPropertyChange: function () {
                    if (_caller.timer != null) {
                        clearTimeout(_caller.timer);
                    }
                    _caller.timer = setTimeout(function () {
                        _caller._onPropertyChange.call(this);
                    }.bind(this), _caller.delay);
                },

                _create: this._create,
                _onSubmit: this._onSubmit,
                _createLoader: this.createLoader,
                _amastyXsearchOnClick: this.onClick,
                _amastyXsearchShowLoader: this.showLoader,
                _amastyXsearchHideLoader: this.hideLoader,

                _amastyXsearchShowPopup: this.showPopup
            };

            $.extend(true, quickSearch.prototype, methods);
        },

        _create: function () {
            this.responseList = {
                indexList: null,
                selected: null
            };
            this.autoComplete = $(this.options.destinationSelector);
            this.searchForm = $(this.options.formSelector);
            this.submitBtn = this.searchForm.find(this.options.submitBtn)[0];
            this.searchLabel = $(this.options.searchLabel);
            this.redirectUrl = null;

            this._createLoader();

            _.bindAll(this, '_onKeyDown', '_onPropertyChange', '_onSubmit', '_amastyXsearchOnClick');

            this.submitBtn.disabled = true;

            this.element.attr('autocomplete', this.options.autocomplete);

            var timer;

            this.element.on('blur', $.proxy(function () {
                timer = setTimeout($.proxy(function () {
                    if (this.autoComplete.is(':hidden')) {
                        this.searchLabel.removeClass('active');
                    }
                    this.autoComplete.hide();
                    this._updateAriaHasPopup(false);
                }, this), 250);
            }, this));

            this.element.trigger('blur');

            this.element.on('focus', $.proxy(function () {
                if (timer != null) {
                    clearTimeout(timer);
                }

                this.searchLabel.addClass('active');
            }, this));
            this.element.on('keydown', this._onKeyDown);

            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");

            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
                $(this.element).keyup(this._onPropertyChange);
            } else {
                this.element.on('input propertychange', this._onPropertyChange);
            }

            this.element.on('input click', this._amastyXsearchOnClick);

            this.searchForm.on('submit', $.proxy(function (e) {
                this._onSubmit(e);
                this._updateAriaHasPopup(false);
            }, this));

            var amAutoComplete = this.options.amAutoComplete;
            $.get(
                amAutoComplete.url.slice(0, -1) + 'recent',
                {uenc: amAutoComplete.currentUrlEncoded},
                $.proxy(function (data) {
                    var $preload = $('#amasty-xsearch-preload');
                    if ($preload && $preload.length > 0 && data && data.html) {
                        $preload.html(data.html);
                    }
                }, this)
            );
        },

        onClick: function () {
            if (this.autoComplete && this.autoComplete.is(":visible")) {
                return;//fix for IE
            }

            var preload = $('#amasty-xsearch-preload');
            if (preload && preload.length > 0) {
                this._amastyXsearchShowPopup(preload.html());
            }

            var value = this.element.val().trim();

            var minChars = this.options.minChars ? this.options.minChars : this.options.minSearchLength;
            if (value.length >= parseInt(minChars, 10)
                && this.options.amAutoComplete.ajaxRequest
                && this.options.amAutoComplete.ajaxRequest.readyState !== 1
            ) {
                this._onPropertyChange();
            }
        },

        _onSubmit: function (e) {
            var value = this.element.val().trim();

            if (value.length === 0 || value == null || /^\s+$/.test(value)) {
                e.preventDefault();
            }

            if (this.redirectUrl) {
                e.preventDefault();
                window.location.assign(this.redirectUrl);
            }

            //disable search by selected items
        },

        showPopup: function (data) {
            var amAutoComplete = this.options.amAutoComplete;
            var searchField = this.element,
                source = this.options.template,
                template = mageTemplate(source),
                dropdown = $('<div class="amsearch-results"></div>'),
                searchResults = $('<div class="amsearch-leftside"></div>'),
                sideProportion = 0.25,
                value = this.element.val().trim(),
                productResults = '.amsearch-products',
                leftSide = '.amsearch-leftside',
                leftSideWidth,
                productsWidth;

            dropdown.append(searchResults);

            if ($.type(data) == 'string') {
                searchResults.append(data);
            } else {
                for (var i in data) {
                    if (data[i].type == 'product' &&
                        amAutoComplete.layout.width >= this.options.amAutoComplete.minSizePopup &&
                        $(window).width() >= this.options.amAutoComplete.mobileView) {
                        dropdown.append(data[i].html);
                    } else {
                        searchResults.append(data[i].html);
                    }
                }
            }

            this.responseList.indexList = this.autoComplete.html(dropdown)
                .addClass('amsearch-clone-position')
                .show()
                .find(this.options.responseFieldElements + ':visible');

            if (amAutoComplete.layout.width >= this.options.amAutoComplete.minSizePopup) {
                leftSideWidth = $(productResults).length ? amAutoComplete.layout.width * sideProportion : searchField.outerWidth();
                productsWidth = amAutoComplete.layout.width ? amAutoComplete.layout.width * (1 - sideProportion) : searchField.outerWidth();
                $(productResults).addClass('-columns');
            } else {
                leftSideWidth = $(productResults).length ? amAutoComplete.layout.width : searchField.outerWidth();
                productsWidth = leftSideWidth;
            }

            $(leftSide).css({
                width: leftSideWidth
            });

            $(productResults).css({
                width: productsWidth
            });

            if (!$(leftSide).children().length) {
                $(leftSide).hide();
            }

            if (this.responseList.indexList.length > 0) {
                this.autoComplete.show();
            } else {
                this.autoComplete.hide();
            }

            this._resetResponseList(false);
            this.element.removeAttr('aria-activedescendant');

            if (this.responseList.indexList.length) {
                this._updateAriaHasPopup(true);
            } else {
                this._updateAriaHasPopup(false);
            }

            this.responseList.indexList
                .on('click', function (e) {
                    var $target = $(e.target);
                    if ($target.hasClass('amasty-xsearch-block-header')) {
                        return false;
                    }

                    if (!$target.attr('data-click-url')) {
                        $target = $(e.target).closest('[data-click-url]');
                    }
                    if ($(e.target).closest('[item-actions=1]').length === 0) {
                        document.location.href = $target.attr('data-click-url');
                    } else {
                        this.element.focus();
                        this.element.trigger('focus');
                        this.element.blur();
                    }
                }.bind(this))
                .on('mouseenter mouseleave', function (e) {
                    if (this.responseList && this.responseList.indexList) {
                        this.responseList.indexList.removeClass(this.options.selectClass);
                    }

                    $(e.target).addClass(this.options.selectClass);
                    this.responseList.selected = $(e.target);
                    this.element.attr('aria-activedescendant', $(e.target).attr('id'));
                }.bind(this));
        },

        _onPropertyChange: function () {
            var amAutoComplete = this.options.amAutoComplete;
            var searchField = this.element,
                clonePosition = {
                    position: 'absolute',
                    // Removed to fix display issues
                    // left: searchField.offset().left,
                    // top: searchField.offset().top + searchField.outerHeight(),
                    width: amAutoComplete.layout.width ?
                            amAutoComplete.layout.width:
                            searchField.outerWidth()
                },
                source = this.options.template,
                template = mageTemplate(source),
                value = this.element.val().trim();

            // check if value is empty
            this.submitBtn.disabled = (value.length === 0) || (value == null) || /^\s+$/.test(value);

            var minChars = this.options.minChars ? this.options.minChars : this.options.minSearchLength;

            if (value.length >= parseInt(minChars, 10)) {
                this._amastyXsearchShowLoader();

                if (amAutoComplete.ajaxRequest) {
                    amAutoComplete.ajaxRequest.abort();
                }

                amAutoComplete.ajaxRequest = $.get(
                    amAutoComplete.url,
                    {q: value, uenc: amAutoComplete.currentUrlEncoded},
                    $.proxy(function (data) {
                        this._amastyXsearchShowPopup(data);
                        this._amastyXsearchHideLoader();
                        if (data.redirect_url) {
                            this.redirectUrl = data.redirect_url;
                        } else {
                            this.redirectUrl = null;
                        }
                    }, this)
                );
            } else {
                this._resetResponseList(true);
                this.autoComplete.hide();
                this._updateAriaHasPopup(false);
                this.element.removeAttr('aria-activedescendant');
            }
        },

        createLoader: function () {
            var loader = $('<div/>', {
                id: 'amasty-xsearch-loader',
                class: 'amasty-xsearch-loader amasty-xsearch-hide'
            }).appendTo(this.searchForm);
        },

        showLoader: function () {
            var $loader = $('#amasty-xsearch-loader');
            $loader.removeClass('amasty-xsearch-hide');

            $(this.submitBtn).addClass('amasty-xsearch-hide');
        },

        hideLoader: function () {
            var $loader = $('#amasty-xsearch-loader');
            $loader.addClass('amasty-xsearch-hide');
            $(this.submitBtn).removeClass('amasty-xsearch-hide');
        }
    };

    return autoComplete;
});
