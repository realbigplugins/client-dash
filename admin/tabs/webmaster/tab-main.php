<?php

/**
 * Outputs Main tab under Webmaster page.
 */
function cd_core_webmaster_main_tab() {
	$content = get_option( 'cd_webmaster_custom_content', 'ISSUE: No content' );
	$content = wpautop( $content );

	if ( $content ) {
		echo $content;
	} else {
		// Show different message for Admin
		if ( current_user_can( 'manage_options' ) )
			cd_error( 'This tab has no content. Please set content under Client Dash <a href="/wp-admin/options-general.php?page=cd_settings&tab=webmaster">settings</a>.' );
		else
			cd_error( 'This tab has no content. If you believe this to be an error, please contact your system administrator.' );
	}
}

cd_content_block( 'Core Webmaster Main', 'webmaster', 'main', 'cd_core_webmaster_main_tab' );