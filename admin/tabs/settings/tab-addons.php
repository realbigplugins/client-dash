<?php

/**
 * Outputs Addons tab under Settings page.
 */
function cd_core_settings_addons_tab() {
  $addons = array(
    'test' => array(
      'name' => 'bust',
      'link' => 'smash'
      ),
    'another' => array(
      'name' => 'smack',
      'link' => 'smush'
      )
    );
  ?>

  <h3>Available Client Dash Addons</h3>
  <div>
    <?php
    foreach ($addons as $key => $value) {
      echo '<p>'.$key;
        foreach($value as $val){
        echo $val;
        }
      echo '</p>';
    }
    ?>
  </div>
<?php
}

add_action('cd_settings_addons_tab', 'cd_core_settings_addons_tab');