<?php
/*
* Options page
*/

// Register plugin settings
function cd_register_settings() {
  register_setting('cd_options', 'cd_remove_which_widgets');
}

add_action('admin_init', 'cd_register_settings');

// Create page
function cd_settings_page() {
  $active_widgets = get_option('cd_active_widgets');

  // Make sure user has rights
  if (!current_user_can('activate_plugins')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }

  // Get options
  $cd_remove_which_widgets = get_option('cd_remove_which_widgets');

  ?>
  <div class="wrap">
    <h2>Client Dash Settings</h2>

    <form method="post" action="options.php">
      <?php
      // Prepare settings
      settings_fields('cd_options');
      do_settings_sections('cd_options');
      ?>

      <table class="form-table">
        <tr valign="top">
          <th scope="row">
            <label for="cd_remove_which_widgets">Widgets to not Remove</label>
          </th>
          <td>
            <?php
            foreach ($active_widgets as $widget => $values) {
              echo '<input type="checkbox" name="cd_remove_which_widgets[' . $widget . ']" id="cd_remove_which_widgets' . $widget . '" value="' . $widget . '" ' . (isset($cd_remove_which_widgets[$widget]) ? 'checked' : '') . '/><label for="cd_remove_which_widgets' . $widget . '">' . $values['title'] . '</label><br/>';
            }
            ?>
          </td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
<?php
}