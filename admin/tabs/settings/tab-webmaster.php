<?php
function cd_core_settings_webmaster() {
  global $cd_option_defaults;

  // Get the active widgets
  $webmaster_enable             = get_option('cd_webmaster_enable', $cd_option_defaults['webmaster_enable']);
  $webmaster_name               = get_option('cd_webmaster_name', $cd_option_defaults['webmaster_name']);
  $webmaster_custom_content     = get_option('cd_webmaster_custom_content', $cd_option_defaults['webmaster_custom_content']);
  $webmaster_custom_content_tab = get_option('cd_webmaster_custom_content_tab', $cd_option_defaults['webmaster_custom_content_tab']);

  // If empty, delete
  if (!$webmaster_name) {
    delete_option('cd_webmaster_name');
  }

  // Create secondary tab option
  $sanitized_tab = strtolower(get_option('cd_webmaster_custom_content_tab'));
  $sanitized_tab = str_replace(' ', '_', $sanitized_tab);
  $sanitized_tab = str_replace('-', '_', $sanitized_tab);
  update_option('cd_webmaster_custom_content_tab_clean', $sanitized_tab);
  ?>

  <table class="form-table">
    <tr valign="top">
      <th scope="row">
        <label for="cd_webmaster_enable">Show Webmaster Page</label>
      </th>
      <td>
        <input type="hidden" name="cd_webmaster_enable" value="0"/>
        <input type="checkbox" id="cd_webmaster_enable" name="cd_webmaster_enable"
               value="1" <?php checked($webmaster_enable, '1'); ?> />
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="cd_webmaster_name">Webmaster Name</label>
      </th>
      <td>
        <input type="text" id="cd_webmaster_name" name="cd_webmaster_name"
               value="<?php echo $webmaster_name; ?>"/>
      </td>
    </tr>
  </table>

  <h3>Custom Content</h3>
  <table class="form-table">
    <tr valign="top">
      <th scope="row">
        <label for="cd_webmaster_custom_content_tab">Tab</label>
      </th>
      <td>
        <input type="text" id="cd_webmaster_custom_content_tab" name="cd_webmaster_custom_content_tab"
               value="<?php echo $webmaster_custom_content_tab; ?>"/>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="cd_webmaster_custom_content">Content</label>
      </th>
      <td>
        <?php wp_editor($webmaster_custom_content, 'cd_webmaster_custom_content'); ?>
      </td>
    </tr>
  </table>
<?php
}

add_action('cd_settings_webmaster_tab', 'cd_core_settings_webmaster');