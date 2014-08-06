<?php

/**
 * Outputs Webmaster page.
 */
function cd_webmaster_page() {
	global $cd_content_blocks;

	// Normally the feed is disabled. Remove the feed content block
	// unless this has been set to true in Settings
	$enable_feed = get_option( 'cd_webmaster_feed', false );
	if ( ! $enable_feed ) {
		unset( $cd_content_blocks['webmaster']['feed'] );
	}

	?>
	<div class="wrap cd-webmaster">
		<?php
		cd_the_page_title( 'webmaster' );
		cd_create_tab_page();
		?>
	</div><!--.wrap-->
<?php
}