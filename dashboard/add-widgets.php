<?php

/**
 * Adds Client Dash dashboard widgets.
 */
function cd_add_dashboard_widgets() {
	global $cd_option_defaults;

	if ( current_user_can( 'publish_posts' ) ) {
		$webmaster_enable = get_option( 'cd_webmaster_enable', false );
		$webmaster_name   = get_option( 'cd_webmaster_name', $cd_option_defaults['webmaster_name'] );

		if( !get_option( 'cd_hide_page_account' ) )
			add_meta_box( 'cd-reports', 'Account', 'cd_account_widget_content', 'dashboard', 'normal', 'core' );

		if( !get_option( 'cd_hide_page_reports' ) )
			add_meta_box( 'cd-account', 'Reports', 'cd_reports_widget_content', 'dashboard', 'normal', 'core' );

		if( !get_option( 'cd_hide_page_help' ) )
			add_meta_box( 'cd-help', 'Help', 'cd_help_widget_content', 'dashboard', 'normal', 'core' );

		if ( $webmaster_enable ) {
			add_meta_box( 'cd-webmaster', $webmaster_name, 'cd_webmaster_widget_content', 'dashboard', 'normal', 'core' );
		}
	}
}

add_action( 'wp_dashboard_setup', 'cd_add_dashboard_widgets' );