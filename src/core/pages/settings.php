<?php

/**
 * Class ClientDash_Page_Settings
 *
 * Creates the toolbar sub-menu item and the page for Settings.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Pages
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
		register_setting( 'cd_options_display', 'cd_display_settings_updated' );

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
	 * Outputs prompt for users to rate the plugin and subscribe
	 *
	 * @since Client Dash 1.7
	 */
	public function subscribe() {
		echo '<div>Like this plugin? Consider <a href="https://wordpress.org/support/view/plugin-reviews/client-dash?rate=5#postform">leaving us a rating</a>. Also, we make other cool plugins and share updates and special offers to anyone who <a href="http://realbigplugins.com/subscribe/?utm_source=Client%20Dash&utm_medium=Plugin%20settings%20footer%20link&utm_campaign=Client%20Dash%20Plugin">subscribes here</a>.</div>';
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

			<?php
			// Default to wrapping everything in the form, but allow to be disabled
			if ( apply_filters( 'cd_settings_form_wrap', true ) ) {

				echo '<form method="post" action="options.php">';

				// Prepare cd_settings
				settings_fields( 'cd_options_' . $tab );
			}

			?>
			<h2 class="cd-title"><span class="dashicons dashicons-admin-settings cd-icon"></span><span class="cd-title-text">Client Dash Settings</span></h2>
			<?php
				$this->create_tab_page();

				// Can modify submit button with this filter
				// EG: add_filter( 'cd_submit', '__return_false' );
				$submit = '<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">';
				echo apply_filters( 'cd_submit', $submit );
				?>
			<?php
			// Default to wrapping everything in the form, but allow to be disabled
			if ( apply_filters( 'cd_settings_form_wrap', true ) ) {
				echo '</form>';
			}
			$this->subscribe();
			?>
		</div>
	<?php
	}
}