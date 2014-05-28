<?php
/*
* Add Client Dash dashboard widgets
*/

function cd_add_dashboard_widgets() {
	if ( current_user_can( 'publish_posts' ) ):
		add_meta_box( 'cd-help', 'Help', 'cd_help_widget_content', 'dashboard', 'normal', 'core' );
		add_meta_box( 'cd-account', 'Reports', 'cd_reports_widget_content', 'dashboard', 'normal', 'core' );
		add_meta_box( 'cd-reports', 'Account', 'cd_account_widget_content', 'dashboard', 'normal', 'core' );
	endif;
}

add_action( 'wp_dashboard_setup', 'cd_add_dashboard_widgets' );