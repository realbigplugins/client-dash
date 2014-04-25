<?php
/*
* Output for Real Big page
*/

function cd_webmaster_page(){
  ?>
  <div class="wrap cd-webmaster">
    <?php
    cd_the_page_title();
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