<?php
/**
 * Client Dash
 *
 * @package     ClientDash
 * @author      Real Big Plugins
 * @license     GPL2
 *
 * Plugin Name: Client Dash
 * Description: Creating a more intuitive admin interface for clients.
 * Version: 2.0.3
 * Author: Real Big Plugins
 * Author URI: https://realbigplugins.com
 * Text Domain: client-dash
 * Domain Path: /languages
 * License:     GPL2
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package ClientDash
 */

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'ClientDash' ) ) {

	define( 'CLIENTDASH_VERSION', '2.0.3' );
	define( 'CLIENTDASH_DIR', plugin_dir_path( __FILE__ ) );
	define( 'CLIENTDASH_URI', plugins_url( '', __FILE__ ) );

	/**
	 * Class ClientDash
	 *
	 * The main plugin class.
	 *
	 * @since 2.0.0
	 */
	class ClientDash {

		/**
		 * Database functions.
		 *
		 * @since 2.0.0
		 *
		 * @var ClientDash_DB
		 */
		public $db;

		/**
		 * api functions.
		 *
		 * @since 2.0.0
		 *
		 * @var ClientDash_API
		 */
		public $api;

		/**
		 * RBM Field Helpers instance.
		 *
		 * @since 2.0.0
		 *
		 * @var RBM_FieldHelpers
		 */
		public $field_helpers;

		/**
		 * RBP Support instance.
		 *
		 * @since 2.0.0
		 *
		 * @var RBP_Support
		 */
		public $support;

		/**
		 * Handles the plugin upgrades.
		 *
		 * @since 2.0.0
		 *
		 * @var ClientDash_Upgrade
		 */
		public $upgrade;

		/**
		 * Handles the plugin pages.
		 *
		 * @since 2.0.0
		 *
		 * @var ClientDash_PluginPages
		 */
		public $pluginpages;

		/**
		 * Handles the plugin settings.
		 *
		 * @since 2.0.0
		 *
		 * @var ClientDash_Settings
		 */
		public $settings;

		/**
		 * Loads the Client Dash Customizer.
		 *
		 * @since 2.0.0
		 *
		 * @var ClientDash_Customize
		 */
		public $customize;

		/**
		 * Modifies the admin from customizations.
		 *
		 * @since 2.0.0
		 *
		 * @var ClientDash_Modify
		 */
		public $modify;

		/**
		 * Handles Helper Pages.
		 *
		 * @since 2.0.0
		 *
		 * @var ClientDash_Helper_Pages
		 */
		public $helper_pages;

		protected function __wakeup() {
		}

		protected function __clone() {
		}

		/**
		 * Call this method to get singleton
		 *
		 * @since 2.0.0
		 *
		 * @return ClientDash()
		 */
		public static function instance() {

			static $instance = null;

			if ( $instance === null ) {

				$instance = new ClientDash();
			}

			return $instance;
		}

		/**
		 * ClientDash constructor.
		 *
		 * @since 2.0.0
		 */
		function __construct() {

			$this->require_necessities();
			$this->legacy_apis();

			$this->setup_fieldhelpers();

			add_action( 'init', array( $this, 'register_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		}

		/**
		 * Requires and loads required files.
		 *
		 * @since 2.0.0
		 * @access private
		 */
		private function require_necessities() {

			require_once CLIENTDASH_DIR . 'core/clientdash-functions.php';
			require_once CLIENTDASH_DIR . 'core/class-clientdash-upgrade.php';
			require_once CLIENTDASH_DIR . 'core/class-clientdash-db.php';
			require_once CLIENTDASH_DIR . 'core/api/class-clientdash-api.php';
			require_once CLIENTDASH_DIR . 'core/customize/class-clientdash-customize.php';
			require_once CLIENTDASH_DIR . 'core/helper-pages/class-clientdash-helper-pages.php';

			$this->upgrade = new ClientDash_Upgrade();

			// Don't load Client Dash unless fully upgraded
			if ( $this->upgrade->needs_update() ) {

				return;
			}

			$this->db           = new ClientDash_DB();
			$this->api          = new ClientDash_API();
			$this->customize    = new ClientDash_Customize();
			$this->helper_pages = new ClientDash_Helper_Pages();

			if ( is_admin() ) {

				require_once CLIENTDASH_DIR . 'core/plugin-pages/class-clientdash-pluginpages.php';
				require_once CLIENTDASH_DIR . 'core/plugin-pages/class-clientdash-settings.php';
				require_once CLIENTDASH_DIR . 'core/class-clientdash-modify.php';

				$this->pluginpages = new ClientDash_PluginPages();
				$this->settings    = new ClientDash_Settings();
				$this->modify      = new ClientDash_Modify();
			}
		}

		/**
		 * Includes and sets up everything required to maintain rough support for legacy extension APIs.
		 *
		 * @since 2.0.0
		 * @access private
		 */
		private function legacy_apis() {

			global $ClientDash_Core_Page_Settings_Tab_Widgets;

			require_once CLIENTDASH_DIR . 'core/legacy-extension-apis/clientdash-menus-api.php';
			require_once CLIENTDASH_DIR . 'core/legacy-extension-apis/clientdash-widgets-api.php';
			require_once CLIENTDASH_DIR . 'core/legacy-extension-apis/clientdash-settings-api.php';
			require_once CLIENTDASH_DIR . 'core/legacy-extension-apis/class-clientdash-core-page-settings-tab-widgets.php';

			$ClientDash_Core_Page_Settings_Tab_Widgets = new ClientDash_Core_Page_Settings_Tab_Widgets();
		}

		/**
		 * Initializes Field Helpers.
		 *
		 * @since 2.0.0
		 * @access private
		 */
		private function setup_fieldhelpers() {

			require_once CLIENTDASH_DIR . 'core/clientdash-fieldhelper-functions.php';
			require_once CLIENTDASH_DIR . 'core/library/rbm-field-helpers/rbm-field-helpers.php';

			$this->field_helpers = new RBM_FieldHelpers( array(
				'ID'   => 'cd',
				'l10n' => array(
					'field_table'    => array(
						'delete_row'    => __( 'Delete Row', 'client-dash' ),
						'delete_column' => __( 'Delete Column', 'client-dash' ),
					),
					'field_select'   => array(
						'no_options'       => __( 'No select options.', 'client-dash' ),
						'error_loading'    => __( 'The results could not be loaded', 'client-dash' ),
						/* translators: %d is number of characters over input limit */
						'input_too_long'   => __( 'Please delete %d character(s)', 'client-dash' ),
						/* translators: %d is number of characters under input limit */
						'input_too_short'  => __( 'Please enter %d or more characters', 'client-dash' ),
						'loading_more'     => __( 'Loading more results...', 'client-dash' ),
						/* translators: %d is maximum number items selectable */
						'maximum_selected' => __( 'You can only select %d item(s)', 'client-dash' ),
						'no_results'       => __( 'No results found', 'client-dash' ),
						'searching'        => __( 'Searching...', 'client-dash' ),
					),
					'field_repeater' => array(
						'collapsable_title' => __( 'New Row', 'client-dash' ),
						'confirm_delete'    => __( 'Are you sure you want to delete this element?', 'client-dash' ),
						'delete_item'       => __( 'Delete', 'client-dash' ),
						'add_item'          => __( 'Add', 'client-dash' ),
					),
					'field_media'    => array(
						'button_text'        => __( 'Upload / Choose Media', 'client-dash' ),
						'button_remove_text' => __( 'Remove Media', 'client-dash' ),
						'window_title'       => __( 'Choose Media', 'client-dash' ),
					),
					'field_checkbox' => array(
						'no_options_text' => __( 'No options available.', 'client-dash' ),
					),
				),
			) );
		}

		/**
		 * Registers plugin assets.
		 *
		 * @since 2.0.0
		 * @access private
		 */
		function register_assets() {

			wp_register_style(
				'clientdash-admin',
				CLIENTDASH_URI . '/assets/dist/css/clientdash-admin.min.css',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CLIENTDASH_VERSION
			);

			wp_register_script(
				'clientdash-admin',
				CLIENTDASH_URI . '/assets/dist/js/clientdash-admin.min.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CLIENTDASH_VERSION,
				true
			);

			// Customize assets
			wp_register_style(
				'clientdash-customize',
				CLIENTDASH_URI . '/assets/dist/css/clientdash-customize.min.css',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CLIENTDASH_VERSION
			);

			wp_register_style(
				'clientdash-customize-inpreview',
				CLIENTDASH_URI . '/assets/dist/css/clientdash-customize-inpreview.min.css',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CLIENTDASH_VERSION
			);

			wp_register_script(
				'clientdash-customize',
				CLIENTDASH_URI . '/assets/dist/js/clientdash-customize.min.js',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CLIENTDASH_VERSION,
				true
			);

			wp_register_script(
				'clientdash-customize-inpreview',
				CLIENTDASH_URI . '/assets/dist/js/clientdash-customize-inpreview.min.js',
				array(),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : CLIENTDASH_VERSION
			);

			wp_localize_script( 'clientdash-admin', 'ClientDash_Data', array(
				'nonce' => wp_create_nonce( 'clientdash_nonce' ),
				'l10n'  => array(
					'reset_settings_confirm' => __( 'This will reset ALL Client Dash settings permanently. This can NOT be undone. Are you sure you want to proceed?', 'client-dash' ),
					'change'                 => __( 'Change', 'client-dash' ),
					'close'                  => __( 'Close', 'client-dash' ),
					'saving'                 => __( 'Saving...', 'client-dash' ),
				),
			) );

			wp_localize_script( 'clientdash-customize-inpreview', 'ClientDashCustomizeInPreview_Data', array(
				'domain' => get_bloginfo( 'url' ),
				'l10n'   => array(
					'preview_only' => __( 'Preview Only', 'clientdash' ),
				),
			) );
		}

		/**
		 * Enqueues plugin assets.
		 *
		 * @since 2.0.0
		 * @access private
		 */
		function enqueue_assets() {

			wp_enqueue_style( 'clientdash-select2' );
			wp_enqueue_script( 'clientdash-select2' );

			wp_enqueue_style( 'clientdash-admin' );
			wp_enqueue_script( 'clientdash-admin' );
		}

		/**
		 * Adds more action links to the plugin row.
		 *
		 * @since 2.0.0
		 * @access private
		 *
		 * @param array $links
		 *
		 * @return array
		 */
		function action_links( $links ) {

			$links[] = '<a href="' . admin_url( 'admin.php?page=clientdash_settings' ) . '">' .
			           __( 'Settings', 'client-dash' ) . '</a>';

			$links[] = '<a href="http://realbigplugins.com/?utm_source=Client%20Dash&utm_medium=Plugins%20list%20link&utm' .
			           '_campaign=Client%20Dash%20Plugin" target="_blank">' . __( 'More Real Big Plugins', 'client-dash' ) .
			           '</a>';

			$links[] = '<a href="http://realbigplugins.com/subscribe/?utm_source=Client%20Dash&utm_medium=Plugins%20list%' .
			           '20link&utm_campaign=Client%20Dash%20Plugin" target="_blank">' . __( 'Subscribe', 'client-dash' ) .
			           '</a>';

			return $links;
		}

		/**
		 * Adds a content section.
		 *
		 * @deprecated
		 */
		public function add_content_section( $section ) {
		}

		/**
		 * Strips out spaces and dashes and replaces them with underscores. Also
		 * translates to lowercase.
		 *
		 * @deprecated
		 *
		 * @param string $name The name to be translated.
		 *
		 * @return string Translated ID.
		 */
		public static function translate_name_to_id( $name ) {

			return strtolower( str_replace( array( ' ', '-' ), '_', $name ) );
		}

		/**
		 * Checks to see if we're on a specific page and tab.
		 *
		 * @deprecated
		 *
		 * @param string $page The page to check.
		 * @param        bool /string $tab If supplied, will also check that the given tab is active.
		 *
		 * @return bool True of on the page (and tab), false otherwise.
		 */
		public static function is_cd_page( $page, $tab = false ) {

			return false;
		}
	}

	// Load the bootstrapper
	require_once CLIENTDASH_DIR . 'client-dash-bootstrapper.php';
	new ClientDash_BootStrapper();

	// Installation
	require_once CLIENTDASH_DIR . 'core/class-clientdash-install.php';
	register_activation_hook( __FILE__, array( 'ClientDash_Install', 'install' ) );

	/**
	 * Gets/loads the main plugin class.
	 *
	 * @since 2.0.0
	 *
	 * @return ClientDash
	 */
	function ClientDash() {

		return ClientDash::instance();
	}
}