<?php

/**
 * Register all settings for Client Dash.
 */
function cd_register_settings() {
	// General Tab
	register_setting( 'cd_options_general', 'cd_remove_which_widgets' );
	register_setting( 'cd_options_general', 'cd_hide_page_account' );
	register_setting( 'cd_options_general', 'cd_hide_page_reports' );
	register_setting( 'cd_options_general', 'cd_hide_page_help' );

	// Icons Tab
	register_setting( 'cd_options_icons', 'cd_dashicon_account' );
	register_setting( 'cd_options_icons', 'cd_dashicon_reports' );
	register_setting( 'cd_options_icons', 'cd_dashicon_help' );
	register_setting( 'cd_options_icons', 'cd_dashicon_webmaster' );

	// Webmaster Tab
	register_setting( 'cd_options_webmaster', 'cd_webmaster_name', 'sanitize_text_field' );
	register_setting( 'cd_options_webmaster', 'cd_webmaster_enable' );
	register_setting( 'cd_options_webmaster', 'cd_webmaster_main_tab_name' );
	register_setting( 'cd_options_webmaster', 'cd_webmaster_main_tab_content' );
	register_setting( 'cd_options_webmaster', 'cd_webmaster_feed' );
	register_setting( 'cd_options_webmaster', 'cd_webmaster_feed_url', 'esc_url' );
	register_setting( 'cd_options_webmaster', 'cd_webmaster_feed_count' );

	// Roles Tab
	register_setting( 'cd_options_roles', 'cd_content_blocks_roles' );

	do_action( 'cd_register_settings' );
}

add_action( 'admin_init', 'cd_register_settings' );

/**
 * Outputs Settings page (under Settings).
 */
function cd_settings_page() {
	// Make sure user has rights
	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="wrap cd-settings">

		<form method="post" action="options.php">
			<?php
			// Get the current tab, if set
			if ( isset( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = 'general';
			}

			// Prepare cd_settings
			settings_fields( 'cd_options_' . $tab );

			cd_the_page_title( 'settings' );
			cd_create_tab_page();

			// Can turn off submit button with this filter
			// EG: add_filter( 'cd_submit', '__return_false' );
			if ( apply_filters( 'cd_submit', true ) ) {
				submit_button();
			}
			?>
		</form>
	</div>
<?php
}