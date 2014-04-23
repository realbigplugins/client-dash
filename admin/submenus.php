<?php
/*
* Creates submenu pages
*/

// Add subpages
function cd_add_subpages() {
  // Account
  $show_account_page = apply_filters('cd_show_account_page', true);

  if ($show_account_page):
    add_submenu_page('index.php', 'Account Information', 'Account', 'edit_others_posts', 'account', 'cd_account_page');
  endif;

  // Help
  $show_help_page = apply_filters('cd_show_help_page', true);

  if ($show_help_page):
    add_submenu_page('index.php', 'Helpful Information', 'Help', 'edit_others_posts', 'help', 'cd_help_page');
  endif;
  
  // Webmaster
  $show_webmaster_page = apply_filters('cd_show_webmaster_page', true);

  if ($show_webmaster_page):
    add_submenu_page('index.php', 'Webmaster', 'Webmaster', 'edit_others_posts', 'webmaster', 'cd_webmaster_page');
  endif;
  
  // Reports
  $show_reports_page = apply_filters('cd_show_reports_page', true);

  if ($show_reports_page):
    add_submenu_page('index.php', 'Reports', 'Reports', 'edit_others_posts', 'reports', 'cd_reports_page');
  endif;

  // Options (not under dashboard)
  add_submenu_page('options-general.php', 'Client Dash Settings', 'Client Dash', 'activate_plugins', 'client-dash', 'cd_settings_page');
}
add_action( 'admin_menu', 'cd_add_subpages' );
?>