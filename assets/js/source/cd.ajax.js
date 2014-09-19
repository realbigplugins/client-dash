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
        },
        /**
         * Sends off all AJAX requests to populate the nav menu. Also updates the progress bar.
         *
         * @since Client Dash 1.6
         */
        populate_nav_menu: function () {

            // Get the total number of menu items (excluding children)
            // And start the completed items at 0
            var total_items = cdData.navMenusAJAX.menu_items.length,
                completed_items = 0;

            // Cycle through all menu items data and send them off
            for (var i = 0; i < total_items; i++) {

                // Prepare our data to send
                var data = {
                    action: 'cd_populate_nav_menu',
                    menu_item: cdData.navMenusAJAX.menu_items[i].menu_item,
                    menu_item_position: cdData.navMenusAJAX.menu_items[i].menu_item_position,
                    menu_ID: cdData.navMenusAJAX.menu_ID,
                    role: cdData.navMenusAJAX.role
                };

                // Now send it off
                $.post(
                    ajaxurl, // already defined
                    data,
                    function () {

                        // Update the loader percentage
                        completed_items++;
                        var progress = Math.round(((100 / total_items) * completed_items));
                        $('.cd-progress-bar-inner').css('right', 100 - progress + '%');
                        $('.cd-progress-bar-percent').html(progress + '%');

                        // If we've cycled though ALL of the menu items, then we're done
                        if (completed_items == total_items) {
                            window.location.href = cdData.navMenusAJAX.url;
                        }
                    }
                )
            }
        }
    };

    $(function () {

        // If the navMenusAJAX property is present, then it's time to create some nav menus!
        if (cdData.hasOwnProperty('navMenusAJAX')) {
            cdAJAX.populate_nav_menu();
        }
    });
})(jQuery);