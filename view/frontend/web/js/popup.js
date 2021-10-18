/**
 *
 * @description Geo IP popup
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
define([
    'jquery',
    'Magento_Ui/js/modal/alert'
], function ($, alert) {
    'use strict';

    /**
     *
     * @note Init geo IP popup widget
     *
     */
    $.widget('mage.geoIpPopup', {
        /**
         *
         * @type {Object}
         *
         */
        options: {
            checkIpUrl: '',
            currentUrl: ''
        },

        /**
         *
         * Create
         *
         * @returns {void}
         *
         * @private
         *
         */
        _create: function () {
            /**
             *
             * @note Check IP
             *
             */
            this.checkIp();
        },

        /**
         *
         * Check IP
         *
         * @returns {void}
         *
         * @public
         *
         */
        checkIp: function () {
            /**
             *
             * @note Init self
             *
             */
            var self = this;

            /**
             *
             * @note Send ajax
             *
             */
            $.ajax({
                url       : self.options.checkIpUrl,
                type      : 'GET',
                dataType  : 'json',
                data      : {current_url: self.options.currentUrl},
                showLoader: false
            }).done(function (data) {
                /**
                 *
                 * @note Check new store URL
                 *
                 */
                if (data['url']) {
                    /**
                     *
                     * @note Show modal
                     *
                     */
                    self.showModal(data['url']);
                }
            })
        },

        /**
         *
         * Show modal
         *
         * @param {String} url
         *
         * @returns {void}
         *
         * @public
         *
         */
        showModal: function (url) {
            /**
             *
             * @note Init modal
             *
             */
            alert({
                title  : $.mage.__('Welcome'),
                content: $.mage.__('We have a better version of the page for you. Would you like to try it?'),
                buttons: [
                    {
                        text : $.mage.__('Accept'),
                        class: 'action primary accept',
                        click: function () {
                            location.href = url;
                        }
                    }
                ]
            });
        }
    });

    /**
     *
     * @note Return widget
     *
     */
    return $.mage.geoIpPopup;
});
