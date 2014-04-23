<?php
/*
* Output for help page
*/

function cd_help_page(){
  ?>
  <div class="wrap">
    <h2>Client Dash - Help</h2>
    <?php
    cd_create_tab_page(array(
      'tabs' => array(
        'Info' => 'info',
        'FAQ' => 'faq',
        'Forum' => 'forum',
        'Tickets' => 'tickets'
        // 'Tutorials' => 'tutorials'
      )
    ));
    ?>
  </div><!--.wrap-->
  <?php
}

?>