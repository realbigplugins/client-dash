<?php
/*
* Output for reports page
*/

function cd_reports_page(){
  ?>
  <div class="wrap cd-reports">
    <?php
    cd_the_page_title();
    cd_create_tab_page(array(
      'tabs' => array(
        'Site Overview' => 'site',
        //'Analytics' => 'analytics',
        //'SEO' => 'seo',
        //'Ecommerce' => 'ecommerce'
      )
    ));
    ?>
  </div><!--.wrap-->
  <?php
}
?>