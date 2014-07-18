<?php

/**
 * Adds all Client Dash submenu pages.
 */
function cd_add_subpages() {
	global $cd_option_defaults, $cd_content_blocks;

	// Unset content blocks if disabled for current role
	cd_unset_content_blocks();

	// This next section will check 3 things before adding the page:
	//   1) Is there content in it?
	//   2) Is there a filter disabling it?
	//   3) Is the "hide page" option turned on?

	// Account
	if ( ! empty( $cd_content_blocks['account'] )
	     && apply_filters( 'cd_show_account_page', true )
	     && ! get_option( 'cd_hide_page_account' )
	) {
		add_submenu_page( 'index.php', 'Account Information', 'Account', 'publish_posts', 'cd_account', 'cd_account_page' );
	}

	// Reports
	if ( ! empty( $cd_content_blocks['reports'] )
	     && apply_filters( 'cd_show_reports_page', true )
	     && ! get_option( 'cd_hide_page_reports' )
	) {
		add_submenu_page( 'index.php', 'Reports', 'Reports', 'publish_posts', 'cd_reports', 'cd_reports_page' );
	}

	// Help
	if ( ! empty( $cd_content_blocks['help'] )
	     && apply_filters( 'cd_show_help_page', true )
	     && ! get_option( 'cd_hide_page_help' )
	) {
		add_submenu_page( 'index.php', 'Helpful Information', 'Help', 'publish_posts', 'cd_help', 'cd_help_page' );
	}

	// Webmaster
	if ( apply_filters( 'cd_show_webmaster_page', true )
	     && ! get_option( 'cd_hide_page_webmaster' )
	     && get_option( 'cd_webmaster_enable', false ) == '1'
	     && ! empty( $cd_content_blocks['webmaster'] )
	) {
		add_submenu_page( 'index.php', get_option( 'cd_webmaster_name', $cd_option_defaults['webmaster_name'] ), get_option( 'cd_webmaster_name', $cd_option_defaults['webmaster_name'] ), 'publish_posts', 'cd_webmaster', 'cd_webmaster_page' );
	}

	// Options (not under dashboard)
	add_options_page( 'Client Dash Settings', 'Client Dash', 'activate_plugins', 'cd_settings', 'cd_settings_page' );
}

add_action( 'admin_menu', 'cd_add_subpages' );

/**
 * Gets rid of "my sites" if in multisite environment.
 */
function cd_ditch_my_sites_submenu() {
	if ( is_multisite() ) {
		$page = remove_submenu_page( 'index.php', 'my-sites.php' );
	}
}

add_action( 'admin_menu', 'cd_ditch_my_sites_submenu', 999 );