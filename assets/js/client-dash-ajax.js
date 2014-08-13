/**
 * Calls an AJAX function to reset all role settings.
 *
 * @since Client Dash 1.5
 */
function cd_reset_roles() {
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
}

/**
 * Calls an AJAX function to reset all settings.
 *
 * @since Client Dash 1.5
 */
function cd_reset_all_settings() {
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
}