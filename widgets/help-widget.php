<?php
// Guide: http://codex.wordpress.org/Function_Reference/wp_add_dashboard_widget
// Widget contents
function cd_help_widget_content(){
  ?>
  <a href="?page=help" class="cd cd-help">
  	<span data-code="f223" class="wp-menu-image cd-icon cd-title-icon"></span>
  </a>
  <?php
}

// Make it a dashboard widget
function add_cd_help_widget() {
	if (current_user_can( 'publish_posts' )) {
	add_meta_box('cd-help', 'Help', 'cd_help_widget_content', 'dashboard', 'side', 'high');
}
}
add_action('wp_dashboard_setup', 'add_cd_help_widget' );
?>