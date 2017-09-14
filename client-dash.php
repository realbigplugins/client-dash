<?php
/**
 * Client Dash
 *
 * @package     LearnDash_Gradebook
 * @author      Real Big Plugins
 * @license     GPL2
 *
 * Plugin Name: Client Dash
 * Description: Creating a more intuitive admin interface for clients.
 * Version: 2.0.0
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

	define( 'CLIENTDASH_VERSION', '2.0.0' );
	define( 'CLIENTDASH_DIR', plugin_dir_path( __FILE__ ) );
	define( 'CLIENTDASH_URI', plugins_url( '', __FILE__ ) );

	/**
	 * Class ClientDash
	 *
	 * The main plugin class.
	 *
	 * @since 2.0.0
	 */
	final class ClientDash {

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

			add_action( 'init', array( $this, 'register_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
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

			$this->db           = new ClientDash_DB();
			$this->api          = new ClientDash_API();
			$this->customize    = new ClientDash_Customize();
			$this->helper_pages = new ClientDash_Helper_Pages();

			if ( is_admin() ) {

				require_once CLIENTDASH_DIR . 'core/plugin-pages/class-clientdash-pluginpages.php';
				require_once CLIENTDASH_DIR . 'core/class-clientdash-modify.php';

				$this->upgrade     = new ClientDash_Upgrade();
				$this->pluginpages = new ClientDash_PluginPages();
				$this->modify      = new ClientDash_Modify();
			}
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
					'reset_settings_confirm' => __(
						'This will reset ALL Client Dash settings permanently. This can NOT be undone. Are you sure ' .
						'you want to proceed?',
						'client-dash'
					),
					'change'                 => __( 'Change', 'client-dash-pro' ),
					'close'                  => __( 'Close', 'client-dash-pro' ),
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

			global $wp_styles;

			wp_enqueue_style( 'clientdash-select2' );
			wp_enqueue_script( 'clientdash-select2' );

			wp_enqueue_style( 'clientdash-admin' );
			wp_enqueue_script( 'clientdash-admin' );
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