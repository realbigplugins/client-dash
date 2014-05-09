<?php
function cd_ditch_dashboard_widgets() {
  global $cd_remove_widgets;

  // Don't remove selected widgets
  $dont_remove = get_option('cd_remove_which_widgets');
  foreach ($dont_remove as $widget):
    unset($cd_remove_widgets[$widget]);
  endforeach;

  // Allow removing/adding of widgets to ditch
  $cd_remove_widgets = apply_filters('cd_remove_widgets', $cd_remove_widgets);

  if (current_user_can('publish_posts')):
    foreach ($cd_remove_widgets as $widget => $values):
  	  remove_meta_box($widget, 'dashboard', $values['position']);
    endforeach;
  endif;
}
add_action('wp_dashboard_setup', 'cd_ditch_dashboard_widgets');
?>