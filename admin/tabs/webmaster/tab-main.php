<?php

/**
 * Outputs Main tab under Webmaster page.
 */
function cd_core_webmaster_main_tab() {
	$content = get_option( 'cd_webmaster_main_tab_content' );
	$content = wpautop( $content );

	if ( ! empty( $content ) ) {
		echo $content;
	} else {
		cd_error( 'Please set content under Client Dash <a href="/wp-admin/options-general.php?page=cd_settings&tab=webmaster">settings</a>.', 'manage_options' );
		cd_error( 'This tab has no content. If you believe this to be an error, please contact your system administrator.' );
	}
}

// Make sure the tab name isn't empty
$cd_webmaster_tab_name = get_option( 'cd_webmaster_main_tab_name' );
if ( empty( $cd_webmaster_tab_name ) ) {
	$cd_webmaster_tab_name = $cd_option_defaults['webmaster_main_tab_name'];
}

cd_content_block(
	'Core Webmaster Main',
	'webmaster',
	$cd_webmaster_tab_name,
	'cd_core_webmaster_main_tab'
);