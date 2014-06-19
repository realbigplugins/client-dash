function cd_updown(id) {
  var e = document.getElementById(id);
  if (e.style.display == 'block')
    e.style.display = 'none';
  else
    e.style.display = 'block';
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