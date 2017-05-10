/**
 * Admin Page functionality.
 *
 * @since {{VERSION}}
 */

var ClientDash_AdminPage;

(function ($) {
    'use strict';

    var api = ClientDash_AdminPage = {

        /**
         * Initializes the api.
         *
         * @since {{VERSION}}
         */
        init: function () {

            if (!$('body').hasClass('client-dash_page_clientdash_admin_page')) {

                return;
            }

            $('#clientdash-admin-page-form').submit(api.handleFormSubmit);
        },

        /**
         * Fires on submitting the form for saving the admin page.
         *
         * @since {{VERSION}}
         */
        handleFormSubmit: function () {

            var $submit_button = $('#submit');

            $submit_button.prop('disabled', true );
        }
    }

    $(api.init);
})(jQuery);