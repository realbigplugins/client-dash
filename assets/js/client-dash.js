// On ready functions
jQuery(function () {
    cd_disable_drag();

    jQuery('.cd-toggle-switch').click(function (e) {
        e.stopPropagation();
        cd_toggle_switch(jQuery(this));
    });

    jQuery('.cd-tip-close').click(function (e) {
        e.stopPropagation();
        cd_close_tip(jQuery(this));
    });

    cd_tips_stop_propogation();
});

// On load functions
jQuery(window).load(function () {
    setTimeout(cd_show_tips, 1000);
});

/**
 * Hides/shows element with given ID.
 *
 * @since Client Dash 1.1
 *
 * @param id The ID of the element to hide/show.
 */
function cd_updown(id) {
    var e = document.getElementById(id);
    if (e.style.display == 'block')
        e.style.display = 'none';
    else
        e.style.display = 'block';
}

/**
 * Toggles the pages on the Settings -> Roles page.
 *
 * @since Client Dash 1.4
 *
 * @param e_self
 */
function cd_toggle_roles_page(e_self) {
    var e_target = jQuery(e_self).siblings('.cd-roles-grid-tab'),
        e_toggle = jQuery(e_self).find('.cd-up-down'),
        e_child_target = e_target.find('.cd-roles-grid-block'),
        e_child_toggle = e_target.find('.cd-up-down');

    // Toggle arrow up and down
    e_toggle.toggleClass('open');

    // Open/collapse target
    e_target.toggleClass('hidden');

    // Collapse children
    e_child_toggle.removeClass('open');
    e_child_target.addClass('hidden');
}

/**
 * Toggles the tabs on the Settings -> Roles page.
 *
 * @since Client Dash 1.4
 *
 * @param e_self
 */
function cd_toggle_roles_tab(e_self) {
    var e_target = jQuery(e_self).siblings('.cd-roles-grid-block'),
        e_toggle = jQuery(e_self).find('.cd-up-down');

    // Toggle
    e_toggle.toggleClass('open');

    // Toggle target
    e_target.toggleClass('hidden');
}

/**
 * Updates selected dashicons.
 *
 * Used on Settings -> Icons.
 *
 * @since Client Dash 1.3
 *
 * @param id
 */
function cd_dashicons_selected(id) {
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
}

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
function cd_dashicons_change(dashicon, e) {
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
}

/**
 * Disable the ability to drag dashboard widgets.
 *
 * @since Client Dash 1.4
 */
function cd_disable_drag() {
    var dash_widgets = jQuery('#dashboard-widgets');

    // Only use on dashboard
    if (!dash_widgets.length) return;

    // Disable being able to drag widgets
    dash_widgets.find('.meta-box-sortables').sortable({disabled: true});
}

/**
 * Toggles the on/off switches on the Roles page.
 *
 * @since Client Dash 1.5
 *
 * @param e The supplied object.
 */
function cd_toggle_switch(e) {
    e.toggleClass('on').toggleClass('off');

    var toggle;

    if (e.attr('data-inverse')) {
        if (e.hasClass('on')) {
            e.find('input').prop('disabled', true);
        } else {
            e.find('input').prop('disabled', false);
        }
    } else {
        if (e.hasClass('on')) {
            e.find('input').prop('disabled', false);
        } else {
            e.find('input').prop('disabled', true);
        }
    }
}
/**
 * Cycles through all tips and shows them on page load.
 *
 * @since Client Dash 1.5
 */
function cd_show_tips() {
    jQuery('.cd-tip').each(function () {
        jQuery(this).removeClass('cd-tip-hidden');
    });
}

/**
 * Closes the help tip.
 *
 * @since Client Dash 1.5
 *
 * @param e The supplied object.
 */
function cd_close_tip(e) {
    e.closest('.cd-tip').addClass('cd-tip-hidden');
}

/**
 * Make sure clicking on items inside tops don't propogate up the DOM tree.
 *
 * @since Client Dash 1.5
 */
function cd_tips_stop_propogation() {
    jQuery('.cd-tip *').click(function (e) {
        e.stopPropagation();
    });
}
