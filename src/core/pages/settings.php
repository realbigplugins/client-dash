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

		// Sidebar sections
		add_action( 'cd_sidebar', array( __CLASS__, 'sidebar_pro_prompt' ) );
		add_action( 'cd_sidebar', array( __CLASS__, 'sidebar_review_support' ), 15 );
		add_action( 'cd_sidebar', array( __CLASS__, 'sidebar_rbp_signup' ), 20 );
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
			__( 'Client Dash Settings', 'client-dash' ),
			__( 'Client Dash', 'client-dash' ),
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
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'client-dash' ) );
		}

		// Get the current tab, if set
		if ( isset( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		} else {
			$tab = 'display';
		}
		?>
		<div class="wrap cd-settings">

			<section class="cd-settings-wrap">
				<?php
				// Default to wrapping everything in the form, but allow to be disabled
				if ( apply_filters( 'cd_settings_form_wrap', true ) ) {

					echo '<form method="post" action="options.php">';

					// Prepare cd_settings
					settings_fields( 'cd_options_' . $tab );
				}

				?>
				<h2 class="cd-title"><span class="dashicons dashicons-admin-settings cd-icon"></span><span
						class="cd-title-text"><?php _e( 'Client Dash Settings', 'client-dash' ); ?></span></h2>
				<?php
				$this->create_tab_page();

				// Can modify submit button with this filter
				// EG: add_filter( 'cd_submit', '__return_false' );
				$submit = '<input type="submit" name="submit" id="submit" class="button button-primary" value="' . __( 'Save Changes', 'client-dash' ) . '">';
				echo apply_filters( 'cd_submit', $submit );
				?>
				<?php
				// Default to wrapping everything in the form, but allow to be disabled
				if ( apply_filters( 'cd_settings_form_wrap', true ) ) {
					echo '</form>';
				}
				?>
			</section>

			<sidebar class="cd-settings-sidebar">

				<?php
				/**
				 * Fires inside sidebar.
				 *
				 * @since 1.6.13
				 *
				 * @hooked ClientDash_Page_Settings::sidebar_rbp_pro-prompt() 10
				 * @hooked ClientDash_Page_Settings::sidebar_rbp_signup() 15
				 */
				do_action( 'cd_sidebar' );
				?>

			</sidebar>
		</div>
		<?php
	}

	/**
	 * Outputs the sidebar pro prompt section.
	 *
	 * @since 1.6.13
	 * @access private
	 */
	static function sidebar_pro_prompt() {
		?>
		<section class="cd-settings-sidebar-section cd-settings-sidebar-pro-prompt">
			<a href="https://realbigplugins.com/plugins/client-dash-pro/?utm_source=Client%20Dash&utm_medium=Plugin%20settings%20sidebar%20link&utm_campaign=Client%20Dash%20Plugin" target="_blank">
				<img src="<?php echo CLIENTDASH_URI; ?>assets/images/cd-pro-logo.png"
				     alt="<?php _e( 'Client Dash Pro', 'clientdash' ); ?>"/>
			</a>

			<h3><?php _e( 'Go Pro!', 'clientdash' ); ?></h3>

			<p>
				<?php _e( 'Extend Client Dash with numerous powerful WordPress dashboard customization ' .
				          'features', 'clientdash' ) ?>
			</p>

			<p class="cd-settings-sidebar-centered">
				<a href=https://realbigplugins.com/plugins/client-dash-pro/?utm_source=Client%20Dash&utm_medium=Plugin%20settings%20sidebar%20link&utm_campaign=Client%20Dash%20Plugin"
				   class="button" target="_blank">
					<?php _e( 'Check it out!', 'clientdash' ); ?>
				</a>
			</p>
		</section>
		<?php
	}

	/**
	 * Outputs the sidebar wordpress.org review/support links.
	 *
	 * @since 1.6.13
	 * @access private
	 */
	static function sidebar_review_support() {

		$rating_confirm = 'onclick="return confirm(\'' .
		                  __( "Is there something we can do better?\\n\\nIf you\\'re having an issue with the " .
		                      "plugin, please consider asking us in the support forums instead.\\n\\nIf you " .
		                      "still want to leave a low rating, please consider changing it in the future " .
		                      "if we fix your issue. Thanks!" ) .
		                  '\');"';
		?>
		<section class="cd-settings-sidebar-section cd-settings-sidebar-review-support">
			<p>
				<?php
				printf(
					__( 'Like this plugin? Consider leaving us a rating.' ),
					'<a href="https://wordpress.org/support/view/plugin-reviews/client-dash?rate=5#postform">',
					'</a>'
				);
				?>
			</p>

			<p class="cd-settings-sidebar-ratings">
				<?php for ( $i = 5; $i >= 1; $i -- ) : ?><a
					href="https://wordpress.org/support/plugin/client-dash/reviews/?rate=<?php echo $i; ?>#new-post"
					class="dashicons dashicons-star-empty" target="_blank"
					<?php echo $i < 4 ? $rating_confirm : ''; ?> >
					</a><?php endfor; ?>
			</p>

			<p>
				<?php
				printf(
					__( 'Need help? Visit our %ssupport forums%s.', 'clientdash' ),
					'<a href="https://wordpress.org/support/plugin/client-dash" target="_blank">',
					'</a>'
				);
				?>
			</p>
		</section>
		<?php
	}

	/**
	 * Outputs the sidebar Real Big Plugins signup section.
	 *
	 * @since 1.6.13
	 * @access private
	 */
	static function sidebar_rbp_signup() {
		?>
		<section class="cd-settings-sidebar-section cd-settings-sidebar-rbp-signup">
			<p>
				<?php
				printf(
					__( 'We make other cool plugins and share updates and special offers to anyone who ' .
					    '%ssubscribes here%s.' ),
					'<a href="http://realbigplugins.com/subscribe/?utm_source=Client%20Dash&utm_medium=Plugin' .
					'%20settings%20sidebar%20link&utm_campaign=Client%20Dash%20Plugin" target="_blank">',
					'</a>'
				);
				?>
			</p>
		</section>
		<?php
	}
}