<?php

/**
 * Class ClientDash_Page_Settings
 *
 * Creates the toolbar sub-menu item and the page for Settings.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Page_Settings extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		// Register all the settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Add the menu item to the toolbar
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
	}

	/**
	 * Registers all of the Client Dash settings.
	 *
	 * @since Client Dash 1.2
	 */
	public function register_settings() {

		// Widgets Tab
		register_setting( 'cd_options_widgets', 'cd_remove_which_widgets' );

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

		// Display Tab
		register_setting( 'cd_options_display', 'cd_content_sections_roles' );
		register_setting( 'cd_options_display', 'cd_hide_page_account' );
		register_setting( 'cd_options_display', 'cd_hide_page_reports' );
		register_setting( 'cd_options_display', 'cd_hide_page_help' );
		register_setting( 'cd_options_display', 'cd_hide_page_webmaster' );

		// Widgets Tab
		register_setting( 'cd_options_widgets', 'cd_widgets' );

		do_action( 'cd_register_settings' );
	}

	/**
	 * Adds the sub-menu item to the toolbar.
	 *
	 * @since Client Dash 1.5
	 */
	public function add_submenu_page() {

		add_submenu_page(
			'options-general.php',
			'Client Dash Settings',
			'Client Dash',
			'manage_options',
			'cd_settings',
			array( $this, 'page_output' )
		);
	}

	/**
	 * The page content.
	 *
	 * @since Client Dash 1.5
	 */
	public function page_output() {

		// Make sure user has rights
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Get the current tab, if set
		if ( isset( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		} else {
			$tab = 'display';
		}
		?>
		<div class="wrap cd-settings">

			<form method="post" action="options.php">
				<?php
				// Prepare cd_settings
				settings_fields( 'cd_options_' . $tab );

				$this->the_page_title( 'settings' );
				$this->create_tab_page();

				// Can modify submit button with this filter
				// EG: add_filter( 'cd_submit', '__return_false' );
				$submit = '<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">';
				echo apply_filters( 'cd_submit', $submit );
				?>
			</form>
		</div>
	<?php
	}
}