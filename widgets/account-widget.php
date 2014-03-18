<?php
// Guide: http://codex.wordpress.org/Function_Reference/wp_add_dashboard_widget
// Widget contents
function cd_account_widget_content() { ?>
<a href="?page=account" class="cd cd-account">
	<span data-code="f337" class="wp-menu-image"></span>
</a>
<?php }
// Make it a dashboard widget
function add_cd_account_widget() {
wp_add_dashboard_widget('cd-account', 'Account', 'cd_account_widget_content');
}
// Register the widget
add_action('wp_dashboard_setup', 'add_cd_account_widget' );
?>