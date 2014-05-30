<?php
function cd_ditch_wp() {
  if (current_user_can('publish_posts')) {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');
  }
}

add_action('wp_before_admin_bar_render', 'cd_ditch_wp', 0);