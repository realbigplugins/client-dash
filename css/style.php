<?php
wp_register_style("client-dash", plugins_url("/style.css", __FILE__), FALSE); 

function cd_styles(){
  //Now we actually register the stylesheet
  wp_enqueue_style("client-dash"); 
}
add_action('admin_enqueue_scripts', 'cd_styles');
?>