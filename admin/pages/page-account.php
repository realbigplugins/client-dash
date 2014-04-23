<?php
/*
* Output for account page
*/

function cd_account_page(){
  ?>
  <div class="wrap">
    <h2>Client Dash - Account</h2>
    <?php
    cd_create_tab_page(array(
      'tabs' => array(
        'About' => 'about',
        'Sites' => 'sites'
      )
    ));
    ?>
  </div><!--.wrap-->
  <?php
}
?>