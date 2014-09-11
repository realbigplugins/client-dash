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
        populate_nav_menu: function () {
            console.log(cdData.navMenusAJAX);
            for (i = 0; i < cdData.navMenusAJAX.menu_items.length; i++) {

                var current_item = cdData.navMenusAJAX.menu_items[i],
                    completed_items = 0;

                var data = {
                    action: 'cd_populate_nav_menu',
                    total: cdData.navMenusAJAX.total,
                    menu_item: current_item.menu_item,
                    menu_item_position: current_item.menu_item_position,
                    menu_ID: cdData.navMenusAJAX.menu_ID,
                    role: cdData.navMenusAJAX.role
                };

                console.log('ready');

                var test = 'test';
                $.post(
                    ajaxurl, // already defined
                    data,
                    function (response) {

                        // Update the loader percentage
                        var progress = Math.round(((100 / cdData.navMenusAJAX.menu_items.length) * completed_items)) + '%';
                        completed_items++;
                        $('#cd-creating-nav-menu-progress').html(progress);

                        if (response.complete) {

                            // Okay, now save it and THEN reload
                            var data = {
                                action: 'cd_save_nav_menu',
                                menu_ID: response.menu_ID
                            };

                            $.post(
                                ajaxurl, // already defined
                                data,
                                function () {
                                    window.location.reload();
                                }
                            )
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