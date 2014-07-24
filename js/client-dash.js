// On ready functions
jQuery(function() {
    cd_disable_drag();
});

/**
 * Hides/shows element with given ID.
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
 * Hides/shows nearest div element.
 */
function cd_toggle_roles_page(e_self) {
    var e_target = jQuery(e_self).siblings('.cd-roles-grid-tab'),
        e_toggle = jQuery(e_self).find('.cd-roles-grid-toggle'),
        e_child_target = e_target.find('.cd-roles-grid-block'),
        e_child_toggle = e_target.find('.cd-roles-grid-toggle');

    // Toggle arrow up and down
    e_toggle.toggleClass('open');

    // Open/collapse target
    e_target.toggleClass('hidden');

    // Collapse children
    e_child_toggle.removeClass('open');
    e_child_target.addClass('hidden');
}

function cd_toggle_roles_tab(e_self) {
    var e_target = jQuery(e_self).siblings('.cd-roles-grid-block'),
        e_toggle = jQuery(e_self).find('.cd-roles-grid-toggle');

    // Toggle
    e_toggle.toggleClass('open');

    // Toggle target
    e_target.toggleClass('hidden');
}

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
    if ( ! dash_widgets.length ) return;

    // Disable being able to drag widgets
    dash_widgets.find('.ui-sortable').sortable('destroy');
}