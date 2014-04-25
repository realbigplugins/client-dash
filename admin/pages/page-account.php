<?php
/*
* Output for account page
*/

function cd_account_page(){
  ?>
  <div class="wrap cd-account">
    <?php
    cd_the_page_title();
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