<?php
// Guide: http://codex.wordpress.org/Function_Reference/wp_add_dashboard_widget
// Widget contents
function cd_account_widget_content(){
  ?>
  <a href="?page=account" class="cd cd-account">
  	<span data-code="f337" class="wp-menu-image cd-icon cd-title-icon"></span>
  </a>
  <?php
}

// Make it a dashboard widget
function add_cd_account_widget() {
	if (current_user_can( 'publish_posts' )) {
  wp_add_dashboard_widget('cd-account', 'Account', 'cd_account_widget_content');
}
}
add_action('wp_dashboard_setup', 'add_cd_account_widget' );
?>