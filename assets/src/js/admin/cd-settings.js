/**
 * Settings Page functionality.
 *
 * @since 2.0.0
 */

var ClientDash_Settings;

(function ($) {
    'use strict';

    var l10n = typeof ClientDash_Data != 'undefined' ? ClientDash_Data.l10n : {};

    var api = ClientDash_Settings = {

        /**
         * Initializes the api.
         *
         * @since 2.0.0
         */
        init: function () {

            if (!$('body').hasClass('client-dash_page_clientdash_settings')) {

                return;
            }

            $('#cd_reset_all_settings').click(api.confirmReset);
        },

        /**
         * Confirms resetting CD settings.
         *
         * @since 2.0.0
         */
        confirmReset: function (e) {

            if ( !confirm(l10n['reset_settings_confirm'])) {

                e.preventDefault();
            }
        }
    }

    $(api.init);
})(jQuery);