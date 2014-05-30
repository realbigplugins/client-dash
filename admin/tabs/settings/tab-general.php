<?php

/**
 * Outputs General tab under Settings page.
 */
function cd_core_settings_general_tab() {
  // Get the active widgets
  $active_widgets = get_option('cd_active_widgets');

  // Get options
  $cd_remove_which_widgets = get_option('cd_remove_which_widgets');
  ?>

  <h3>Widget Settings</h3>
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
<?php
}

add_action('cd_settings_general_tab', 'cd_core_settings_general_tab');