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
		cd_error( 'This tab has no content. Please set content under Client Dash settings.' );
	}
}

cd_content_block( 'Core Webmaster Main', 'webmaster', 'main', 'cd_core_webmaster_main_tab' );