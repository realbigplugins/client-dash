/**
 * AJAX functionality within Client Dash.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since ClientDash 1.6
 */
var cdAJAX;
(function ($) {
    cdAJAX = {
        /**
         * Calls an AJAX function to reset all role settings.
         *
         * @since Client Dash 1.5
         */
        reset_roles: function () {
            // Setup
            jQuery('body').append('<div id="cd-ajax-cover"></div>');
            jQuery('#cd-ajax-cover').animate({
                'opacity': 1
            }, 200);

            // AJAX
            var data = {
                'action': 'cd_reset_roles'
            };

            jQuery.post(
                ajaxurl,
                data,
                function (response) {
                    // Notify user
                    alert(response);

                    // Refresh the page
                    location.reload();
                }
            )
        },
        /**
         * Calls an AJAX function to reset all settings.
         *
         * @since Client Dash 1.5
         */
        reset_all_settings: function () {
            // AJAX
            var data = {
                'action': 'cd_reset_all_settings'
            };

            jQuery.post(
                ajaxurl,
                data,
                function (response) {
                    // Notify user
                    alert(response);
                }
            )
        },
        /**
         * Calls an AJAX function to reset the admin menu.
         *
         * @since Client Dash 1.5
         */
        reset_admin_menu: function () {
            // AJAX
            var data = {
                'action': 'cd_reset_admin_menu',
                'cd-create-admin-menus': true
            };

            jQuery.post(
                ajaxurl,
                data,
                function (response) {
                    // Notify user
                    alert(response);
                }
            )
        }
    };
})(jQuery);