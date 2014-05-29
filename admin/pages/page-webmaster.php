<?php
/*
* Output for Real Big page
*/

function cd_webmaster_page() {
  ?>
  <div class="wrap cd-webmaster">
    <?php
    cd_the_page_title();
    cd_create_tab_page();
    ?>
  </div><!--.wrap-->
<?php
}

// If custom content exists, add it
function cd_webmaster_custom_content() {
  global $cd_option_defaults;

  // Break if webmaster custom content not set
  if (!get_option('cd_webmaster_custom_content') || !get_option('cd_webmaster_custom_content_tab')) {
    return;
  }

  $content = get_option('cd_webmaster_custom_content', $cd_option_defaults['webmaster_custom_content']);

  echo wpautop($content);
}

add_action('cd_webmaster_' . get_option('cd_webmaster_custom_content_tab_clean') . '_tab', 'cd_webmaster_custom_content');

function cd_webmaster_custom_content_tab($tabs) {
  global $cd_option_defaults;

  // Break if webmaster custom content not set
  if (!get_option('cd_webmaster_custom_content') || !get_option('cd_webmaster_custom_content_tab')) {
    return $tabs;
  }

  $tabs['webmaster'][get_option('cd_webmaster_custom_content_tab')] = get_option('cd_webmaster_custom_content_tab_clean');

  return $tabs;
}

add_filter('cd_tabs', 'cd_webmaster_custom_content_tab');