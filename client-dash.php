<?php
/**
 * Plugin Name: Client Dash
 * Description: Creating a more intuitive admin interface for clients.
 * Version: 2.0.0-beta.1
 * Author: Real Big Plugins
 * Author URI: https://realbigplugins.com
 *
 * @package ClientDash
 */

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'ClientDash' ) ) {

	define( 'CLIENTDASH_VERSION', '2.0.0-beta.1' );
	define( 'CLIENTDASH_DIR', plugin_dir_path( __FILE__ ) );
	define( 'CLIENTDASH_URI', plugins_url( '', __FILE__ ) );

	/**
	 * Class ClientDash
	 *
	 * The main plugin class.
	 *
	 * @since {{VERSION}}
	 */
	final class ClientDash {

		/**
		 * Database functions.
		 *
		 * @since {{VERSION}}
		 *
		 * @var ClientDash_DB
		 */
		public $db;

		/**
		 * api functions.
		 *
		 * @since {{VERSION}}
		 *
		 * @var ClientDash_API
		 */
		public $api;

		/**
		 * Handles the plugin upgrades.
		 *
		 * @since {{VERSION}}
		 *
		 * @var ClientDash_Upgrade
		 */
		public $upgrade;

		/**
		 * Handles the plugin pages.
		 *
		 * @since {{VERSION}}
		 *
		 * @var ClientDash_PluginPages
		 */
		public $pluginpages;

		/**
		 * Loads the Client Dash Customizer.
		 *
		 * @since {{VERSION}}
		 *
		 * @var ClientDash_Customize
		 */
		public $customize;

		/**
		 * Modifies the admin from customizations.
		 *
		 * @since {{VERSION}}
		 *
		 * @var ClientDash_Modify
		 */
		public $modify;

		/**
		 * Handles core CD pages.
		 *
		 * @since {{VERSION}}
		 *
		 * @var ClientDash_Core_Pages
		 */
		public $core_pages;

		protected function __wakeup() {
		}

		protected function __clone() {
		}

		/**
		 * Call this method to get singleton
		 *
		 * @since {{VERSION}}
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
		 * @since {{VERSION}}
		 */
		function __construct() {

			$this->require_necessities();

			add_action( 'init', array( $this, 'register_assets' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		}

		/**
		 * Requires and loads required files.
		 *
		 * @since {{VERSION}}
		 * @access private
		 */
		private function require_necessities() {

			require_once CLIENTDASH_DIR . 'core/clientdash-functions.php';
			require_once CLIENTDASH_DIR . 'core/class-clientdash-upgrade.php';
			require_once CLIENTDASH_DIR . 'core/class-clientdash-db.php';
			require_once CLIENTDASH_DIR . 'core/api/class-clientdash-api.php';
			require_once CLIENTDASH_DIR . 'core/customize/class-clientdash-customize.php';
			require_once CLIENTDASH_DIR . 'core/core-pages/class-clientdash-core-pages.php';

			$this->db         = new ClientDash_DB();
			$this->api        = new ClientDash_API();
			$this->customize  = new ClientDash_Customize();
			$this->core_pages = new ClientDash_Core_Pages();

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
		 * @since {{VERSION}}
		 * @access private
		 */
		function register_assets() {

			// --- INTERNAL ---

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

			wp_register_style(
				'clientdash-fontawesome',
				'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
				array(),
				'4.7.0'
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
				CLIENTDASH_URI . '/assets/dist/js/clientdash-inpreview.min.js',
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

			// --- VENDOR ---

			// Select 2
			wp_register_style(
				'clientdash-select2',
				CLIENTDASH_URI . '/assets/vendor/select2/select2.min.css',
				array(),
				'4.0.3'
			);

			wp_register_script(
				'clientdash-select2',
				CLIENTDASH_URI . '/assets/vendor/select2/select2.full.min.js',
				array( 'jquery' ),
				'4.0.3'
			);
		}

		/**
		 * Enqueues plugin assets.
		 *
		 * @since {{VERSION}}
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
	 * @since {{VERSION}}
	 *
	 * @return ClientDash
	 */
	function ClientDash() {

		return ClientDash::instance();
	}
}