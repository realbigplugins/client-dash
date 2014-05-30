<?php

/**
 * Removes My Sites from toolbar.
 *
 * @param $wp_admin_bar
 */
function cd_remove_my_sites($wp_admin_bar) {
  if (!current_user_can('manage_network')) {
    $wp_admin_bar->remove_node('my-sites');
  }
}

add_action('admin_bar_menu', 'cd_remove_my_sites', 999);