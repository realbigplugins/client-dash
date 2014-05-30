<?php

/**
 * Outputs Webmaster page.
 */
function cd_webmaster_page() {
  global $cd_option_defaults;

  // Modify accordingly for webmaster settings
  $webmaster_tab = get_option('cd_webmaster_custom_content_tab', $cd_option_defaults['webmaster_custom_content_tab']);
  if ($webmaster_tab)
    add_filter('cd_tabs', 'cd_change_webmaster_custom_tab');

  $enable_feed = get_option('cd_webmaster_feed', false);
  if (!$enable_feed)
    add_filter('cd_tabs', 'cd_remove_feed_tab');

  ?>
  <div class="wrap cd-webmaster">
    <?php
    cd_the_page_title();
    cd_create_tab_page();
    ?>
  </div><!--.wrap-->
<?php
}

/**
 * Changes the name of the custom webmaster tab.
 *
 * @param array $tabs All existing tabs
 *
 * @return array
 */
function  cd_change_webmaster_custom_tab($tabs) {
  // Get new tab name and set it
  $webmaster_tab = get_option('cd_webmaster_custom_content_tab', $cd_option_defaults['webmaster_custom_content_tab']);

  $tabs['webmaster'][$webmaster_tab] = $tabs['webmaster']['Main'];
  unset($tabs['webmaster']['Main']);

  // Reset feeds tab so it appears after
  unset($tabs['webmaster']['Feed']);
  $tabs['webmaster']['Feed'] = 'feed';

  return $tabs;
}

/**
 * Removes "Feed" tab from webmaster page.
 *
 * @param array $tabs All existing tabs
 *
 * @return array
 */
function cd_remove_feed_tab($tabs) {
  unset($tabs['webmaster']['Feed']);

  return $tabs;
}