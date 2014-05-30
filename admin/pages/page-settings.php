<?php
/*
* Options page
*/

// Register plugin cd_settings
function cd_register_settings() {
  register_setting('cd_options', 'cd_remove_which_widgets');
  register_setting('cd_options', 'cd_webmaster_name', 'sanitize_text_field');
  register_setting('cd_options', 'cd_webmaster_enable');
  register_setting('cd_options', 'cd_webmaster_custom_content_tab');
  register_setting('cd_options', 'cd_webmaster_custom_content');
}

add_action('admin_init', 'cd_register_settings');

// Create page
function cd_settings_page() {
  // Make sure user has rights
  if (!current_user_can('activate_plugins')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }
  ?>
  <div class="wrap cd-settings">

    <form method="post" action="options.php">
      <?php
      // Prepare cd_settings
      settings_fields('cd_options');
      do_settings_sections('cd_options');
      cd_the_page_title();
      cd_create_tab_page();
      submit_button(); ?>
    </form>
  </div>
<?php
}