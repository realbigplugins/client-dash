<?php
/*
* Include files
*/

function client_dash_register_files(){
  wp_register_script('client-dash', plugins_url('../js/client-dash.js', __FILE__ ), array('jquery'), null, true);
  wp_register_style('client-dash', plugins_url('../css/client-dash.css', __FILE__), array(), null);
}
add_action('admin_init', 'client_dash_register_files');

function client_dash_enqueue_files(){
	if (current_user_can( 'publish_posts' )) {
  wp_enqueue_script('client-dash');
  wp_enqueue_style('client-dash');
}
}
add_action('admin_enqueue_scripts', 'client_dash_enqueue_files');
?>