<?php
/*
* Output for Real Big page
*/

function cd_webmaster_page(){
  ?>
  <div class="wrap">
    <h2>Client Dash - Webmaster</h2>
    <?php
    cd_create_tab_page(array(
      'tabs' => array(
        'News' => 'news',
        'Promotions' => 'promotions'
      )
    ));
    ?>
  </div><!--.wrap-->
  <?php
}
?>