<?php
/**
 * Adds plugin page(s) to the wp admin.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages
 */

defined( 'ABSPATH' ) || die;

/**
 * Class ClientDash_PluginPages
 *
 * Adds plugin page(s) to the wp admin
 *
 * @since {{VERSION}}
 */
class ClientDash_PluginPages {

	/**
	 * The current page tab, if any.
	 *
	 * @since {{VERSION}}
	 *
	 * @var null|string
	 */
	public $current_tab = null;

	/**
	 * ClientDash_PluginPages constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'get_current_tab' ) );
		add_action( 'admin_menu', array( $this, 'add_pages' ) );

		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_pro_prompt' ), 10 );
		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_review_support' ), 15 );
		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_rbp_signup' ), 20 );

		add_action( 'clientdash_reset_all_settings', array( __CLASS__, 'reset_admin_page' ) );

		if ( isset( $_REQUEST['cd_reset_settings'] ) ) {

			add_action( 'admin_init', array( $this, 'reset_all_settings' ) );
		}
	}

	/**
	 * Handles resetting all settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function reset_all_settings() {

		cd_reset_all_settings();

		add_settings_error(
			'cd_reset_settings',
			'',
			__( 'All settings successfully reset.', 'client-dash' ),
			'updated clientdash-notice'
		);

		set_transient( 'settings_errors', get_settings_errors(), 30 );

		wp_redirect( admin_url( 'admin.php?page=clientdash_settings&settings-updated=1' ) );
		exit();
	}

	/**
	 * Resets the Admin Page.
	 *
	 * @since {{VERSION}}
	 */
	public static function reset_admin_page() {

		delete_option( 'cd_admin_page_title' );
		delete_option( 'cd_admin_page_content' );
	}

	/**
	 * Registers all of the Client Dash settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function register_settings() {

		register_setting( 'clientdash_admin_page', 'cd_admin_page_title' );
		register_setting( 'clientdash_admin_page', 'cd_admin_page_content' );
	}

	/**
	 * Gets the current page tab, if any.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function get_current_tab() {

		if ( isset( $_REQUEST['tab'] ) &&
		     isset( $_REQUEST['page'] ) &&
		     substr( $_REQUEST['page'], 0, 10 ) == 'clientdash'
		) {

			$this->current_tab = $_REQUEST['tab'];
		}
	}

	/**
	 * Adds the sub-menu item to the toolbar.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function add_pages() {

		global $submenu;

		add_menu_page(
			__( 'Client Dash', 'clientdash' ),
			__( 'Client Dash', 'clientdash' ),
			'manage_options',
			'clientdash',
			null,
			'dashicons-admin-generic',
			100
		);

		add_submenu_page(
			'clientdash',
			__( 'Admin Page', 'clientdash' ),
			__( 'Admin Page', 'clientdash' ),
			'manage_options',
			'clientdash_admin_page',
			array( __CLASS__, 'load_admin_page' )
		);

		add_submenu_page(
			'clientdash',
			__( 'Addons', 'clientdash' ),
			__( 'Addons', 'clientdash' ),
			'manage_options',
			'clientdash_addons',
			array( __CLASS__, 'load_addons' )
		);

		add_submenu_page(
			'clientdash',
			__( 'Settings', 'clientdash' ),
			__( 'Settings', 'clientdash' ),
			'manage_options',
			'clientdash_settings',
			array( __CLASS__, 'load_settings' )
		);

		$submenu['clientdash'][0] = array(
			__( 'Customize Admin', 'clientdash' ),
			'customize_admin',
			'/?clientdash_customize=1'
		);
	}

	/**
	 * Loads the Admin Page screen.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function load_admin_page() {

		$admin_page_title   = get_option( 'cd_admin_page_title' );
		$admin_page_content = get_option( 'cd_admin_page_content' );

		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_admin_page_actions' ), 5 );

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/admin-page.php';
	}

	/**
	 * Loads the Addons screen.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function load_addons() {

		$addons = get_transient( 'clientdash_addons' );

		if ( ! $addons ) {

			$response = wp_remote_get( 'https://realbigplugins.com/edd-api/v2/products?category=client-dash&number=-1' );

			if ( is_wp_error( $response ) ) {

				$addons = array();

			} else {

				$body = json_decode( wp_remote_retrieve_body( $response ) );

				$addons = isset( $body->products ) ? $body->products : array();

				set_transient( 'clientdash_addons', $addons, DAY_IN_SECONDS );
			}
		}

		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_addons_rbp_promote' ), 5 );

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/addons.php';
	}

	/**
	 * Loads the Settings screen.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function load_settings() {

		$reset_settings_link = admin_url( 'admin.php?page=clientdash_settings&cd_reset_settings' );

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/settings.php';
	}

	/**
	 * Outputs the sidebar pro prompt section.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function sidebar_pro_prompt() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/pro-prompt.php';
	}

	/**
	 * Outputs the sidebar wordpress.org review/support links.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function sidebar_review_support() {

		$rating_confirm = 'onclick="return confirm(\'' .
		                  __( "Is there something we can do better?\\n\\nIf you\\'re having an issue with the " .
		                      "plugin, please consider asking us in the support forums instead.\\n\\nIf you " .
		                      "still want to leave a low rating, please consider changing it in the future " .
		                      "if we fix your issue. Thanks!" ) .
		                  '\');"';

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/review-support.php';
	}

	/**
	 * Outputs the sidebar Real Big Plugins signup section.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function sidebar_rbp_signup() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/rbp-signup.php';
	}

	/**
	 * Outputs the sidebar admin page actions section.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function sidebar_admin_page_actions() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/admin-page-actions.php';
	}

	/**
	 * Outputs the sidebar addons Real Big Plugins promote section.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	static function sidebar_addons_rbp_promote() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/addons-rbp-promote.php';
	}
}