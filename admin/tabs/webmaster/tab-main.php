<?php

/**
 * Outputs Main tab under Webmaster page.
 */
function cd_core_webmaster_main_tab() {
  $content = get_option('cd_webmaster_custom_content', 'ISSUE: No content');
  $content = wpautop($content);

  echo $content;
}

add_action('cd_webmaster_main_tab', 'cd_core_webmaster_main_tab');