/**
 * Overall base functionality for Client Dash.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since ClientDash 1.6
 */
var cdMain;
(function ($) {
    cdMain = {
        /**
         * The initialization for ClientDash functionality.
         *
         * @since ClientDash 1.4
         */
        init: function () {
            this.disable_drag();

            jQuery('.cd-toggle-switch').click(function (e) {
                e.stopPropagation();
                cdMain.toggle_switch(jQuery(this));
            });

            jQuery('.cd-tip-close').click(function (e) {
                e.stopPropagation();
                cdMain.close_tip(jQuery(this));
            });

            this.tips_stop_propogation();
        },
        /**
         * Hides/shows element with given ID.
         *
         * @since Client Dash 1.1
         *
         * @param id The ID of the element to hide/show.
         */
        updown: function (id) {
            var e = document.getElementById(id);
            if (e.style.display == 'block')
                e.style.display = 'none';
            else
                e.style.display = 'block';
        },
        /**
         * Toggles the pages on the Settings -> Roles page.
         *
         * @since Client Dash 1.4
         *
         * @param e_self
         */
        toggle_roles_page: function (e_self) {
            var e_target = jQuery(e_self).siblings('.cd-display-grid-tab'),
                e_toggle = jQuery(e_self).find('.cd-up-down');

            // Toggle arrow up and down
            e_toggle.toggleClass('open');

            // Open/collapse target
            e_target.toggleClass('hidden');
        },
        /**
         * Updates selected dashicons.
         *
         * Used on Settings -> Icons.
         *
         * @since Client Dash 1.3
         *
         * @param id
         */
        dashicons_selected: function (id) {
            // Set up some variables
            var selectedEl = jQuery('#cd-dashicons-selections'),
                gridEl = jQuery('#cd-dashicons-grid'),
                dashicon = selectedEl.find('.cd-' + id + ' .dashicons').attr('data-dashicon');

            // Remove all active classes
            selectedEl.find('.dashicons').removeClass('active');
            gridEl.find('.cd-dashicons-grid-item').removeClass('active');

            // Add active class to correct items
            selectedEl.find('.cd-' + id + ' .dashicons').addClass('active');
            gridEl.find('.dashicons.' + dashicon).closest('.cd-dashicons-grid-item').addClass('active');
        },
        /**
         * Changes the dashicon icon when clicking.
         *
         * Used on Settings -> Icons.
         *
         * @since Client Dash 1.3
         *
         * @param dashicon
         * @param e
         */
        dashicons_change: function (dashicon, e) {
            // Set up some variables
            var selectedEl = jQuery('#cd-dashicons-selections'),
                active_widget = selectedEl.find('.dashicons.active').attr('data-widget');

            // Change the value of the hidden input field for updating the option
            jQuery('#cd_dashicon_' + active_widget).val(dashicon);

            // Update the attributes
            selectedEl.find('.dashicons.active').attr({
                'data-dashicon': dashicon,
                'class': 'dashicons ' + dashicon + ' active'
            });

            // Remove all active classes
            jQuery('#cd-dashicons-grid').find('.cd-dashicons-grid-item').removeClass('active');

            // Add the active class to the new active option
            jQuery(e).closest('.cd-dashicons-grid-item').addClass('active');
        },
        /**
         * Disable the ability to drag dashboard widgets.
         *
         * @since Client Dash 1.4
         */
        disable_drag: function () {
            var dash_widgets = jQuery('#dashboard-widgets');

            // Only use on dashboard
            if (!dash_widgets.length) return;

            // Disable being able to drag widgets
            dash_widgets.find('.meta-box-sortables').sortable({disabled: true});
        },
        /**
         * Toggles the on/off switches on the Roles page.
         *
         * @since Client Dash 1.5
         *
         * @param e The supplied object.
         */
        toggle_switch: function (e) {

            // Find the hidden input
            var e_input = e.find('input');

            // Toggle the on and off classes
            e.toggleClass('on').toggleClass('off');

            // Toggle the disabled attr
            if (e_input.prop('disabled')) {
                e_input.prop('disabled', false);
            } else {
                e_input.prop('disabled', true);
            }
        },
        /**
         * Cycles through all tips and shows them on page load.
         *
         * @since Client Dash 1.5
         */
        show_tips: function () {
            jQuery('.cd-tip').each(function () {
                jQuery(this).removeClass('cd-tip-hidden');
            });
        },
        /**
         * Closes the help tip.
         *
         * @since Client Dash 1.5
         *
         * @param e The supplied object.
         */
        close_tip: function (e) {
            e.closest('.cd-tip').addClass('cd-tip-hidden');
        },
        /**
         * Make sure clicking on items inside tips don't propogate up the DOM tree.
         *
         * @since Client Dash 1.5
         */
        tips_stop_propogation: function () {
            jQuery('.cd-tip *').click(function (e) {
                e.stopPropagation();
            });
        }
    };

    // Launch on ready
    $(function () {
        cdMain.init();
    });
    // Launch on page load
    $(window).load(function () {
        setTimeout(cdMain.show_tips, 1000);
    });
})(jQuery);