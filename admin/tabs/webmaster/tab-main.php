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
		echo '<div class="settings-error error"><p>This tab has no content. Please set content under Client Dash settings.</p></div>';
	}
}

add_action( 'cd_webmaster_main_tab', 'cd_core_webmaster_main_tab' );