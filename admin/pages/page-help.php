<?php
/*
* Output for help page
*/

function cd_help_page(){
  ?>
  <div class="wrap cd-help">
    <?php
    cd_the_page_title();
    cd_create_tab_page(array(
      'tabs' => array(
        'Info' => 'info',
        // 'FAQ' => 'faq',
        //'Forum' => 'forum',
        //'Tickets' => 'tickets'
        // 'Tutorials' => 'tutorials'
      )
    ));
    ?>
  </div><!--.wrap-->
  <?php
}

?>