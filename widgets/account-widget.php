<?php
// Guide: http://codex.wordpress.org/Function_Reference/wp_add_dashboard_widget
// Widget contents
function cd_account_widget_content() {
	echo "Hello World, this is my first Dashboard Widget!";
}
// Make it a dashboard widget
function add_cd_account_widget() {
	add_meta_box('id', 'Account', 'cd_account_widget_content', 'dashboard', 'side', 'high');
}
// Register the widget
add_action('wp_dashboard_setup', 'add_cd_account_widget' );
?>