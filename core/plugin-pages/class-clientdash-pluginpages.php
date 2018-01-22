<?php
/**
 * Adds plugin page(s) to the wp admin.
 *
 * @since 2.0.0
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
 * @since 2.0.0
 */
class ClientDash_PluginPages {

	/**
	 * The current page tab, if any.
	 *
	 * @since 2.0.0
	 *
	 * @var null|string
	 */
	public $current_tab = null;

	/**
	 * ClientDash_PluginPages constructor.
	 *
	 * @since 2.0.0
	 */
	function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'get_current_tab' ) );
		add_action( 'admin_menu', array( $this, 'add_pages' ), 9 );

		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_pro_prompt' ), 10 );
		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_review_support' ), 15 );
		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_rbp_signup' ), 20 );

		add_action( 'clientdash_reset_all_settings', array( __CLASS__, 'reset_admin_page' ) );

		if ( isset( $_REQUEST['cd_reset_settings'] ) ) {

			add_action( 'admin_init', array( $this, 'reset_all_settings' ) );
		}

		if ( isset( $_REQUEST['cd_enable_customize_tutorial'] ) ) {

			add_action( 'admin_init', array( $this, 'enable_customize_tutorial' ) );
		}

		if ( isset( $_REQUEST['cd_flush_addons'] ) ) {

			add_action( 'admin_init', array( $this, 'flush_addons_cache' ) );
		}
	}

	/**
	 * Handles resetting all settings.
	 *
	 * @since 2.0.0
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
	 * Enables the customize tutorial.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function enable_customize_tutorial() {

		delete_user_meta( get_current_user_id(), 'clientdash_hide_customize_tutorial' );

		add_settings_error(
			'cd_reset_settings',
			'',
			__( 'Customize Admin tutorial enabled.', 'client-dash' ),
			'updated clientdash-notice'
		);

		set_transient( 'settings_errors', get_settings_errors(), 30 );

		wp_redirect( admin_url( 'admin.php?page=clientdash_settings&settings-updated=1' ) );
		exit();
	}

	/**
	 * Resets the addons cache.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function flush_addons_cache() {

		delete_transient( 'clientdash_addons' );

		wp_redirect( admin_url( 'admin.php?page=clientdash_addons' ) );
		exit();
	}

	/**
	 * Registers all of the Client Dash settings.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function register_settings() {

		register_setting( 'clientdash_helper_pages', 'cd_helper_pages' );
	}

	/**
	 * Gets the current page tab, if any.
	 *
	 * @since 2.0.0
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
	 * @since 2.0.0
	 * @access private
	 */
	function add_pages() {

		global $submenu;

		add_menu_page(
			__( 'Client Dash', 'client-dash' ),
			__( 'Client Dash', 'client-dash' ),
			'manage_options',
			'clientdash',
			null,
			'dashicons-admin-generic',
			100
		);

		add_submenu_page(
			'clientdash',
			__( 'Admin Page', 'client-dash' ),
			__( 'Admin Page', 'client-dash' ),
			'manage_options',
			'clientdash_admin_page',
			array( __CLASS__, 'load_admin_page' )
		);

		add_submenu_page(
			'clientdash',
			__( 'Helper Pages', 'client-dash' ),
			__( 'Helper Pages', 'client-dash' ),
			'manage_options',
			'clientdash_helper_pages',
			array( __CLASS__, 'load_helper_pages' )
		);

		add_submenu_page(
			'clientdash',
			__( 'Settings', 'client-dash' ),
			__( 'Settings', 'client-dash' ),
			'manage_options',
			'clientdash_settings',
			array( __CLASS__, 'load_settings' )
		);

		add_submenu_page(
			'clientdash',
			__( 'Addons', 'client-dash' ),
			__( 'Addons', 'client-dash' ),
			'manage_options',
			'clientdash_addons',
			array( __CLASS__, 'load_addons' )
		);

		if ( current_user_can( 'manage_options' ) ) {

			$submenu['clientdash'][0] = array(
				__( 'Customize Admin', 'client-dash' ),
				'customize_admin',
				'/?clientdash_customize=1'
			);
		}
	}

	/**
	 * Loads the Admin Page screen.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function load_admin_page() {

		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_admin_page_actions' ), 5 );

		$admin_page_content = get_option( 'cd_adminpage_content' );

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/admin-page.php';
	}

	/**
	 * Loads the Helper Pages screen.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function load_helper_pages() {

		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_helper_pages_actions' ), 5 );

		$pages = ClientDash_Helper_Pages::get_pages();

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/helper-pages.php';
	}

	/**
	 * Loads the Addons screen.
	 *
	 * @since 2.0.0
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
	 * @since 2.0.0
	 * @access private
	 */
	static function load_settings() {

		add_action( 'clientdash_sidebar', array( __CLASS__, 'sidebar_settings_page_actions' ), 5 );

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/settings.php';
	}

	/**
	 * Displays the "Feed" settings section.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function settings_page_feed() {

		$feed_url   = get_option( 'cd_adminpage_feed_url', '' );
		$feed_count = get_option( 'cd_adminpage_feed_count', 5 );

		include CLIENTDASH_DIR . 'core/plugin-pages/views/settings/feed.php';
	}

	/**
	 * Displays the "Other" settings section.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function settings_page_other() {

		$reset_settings_link            = admin_url( 'admin.php?page=clientdash_settings&cd_reset_settings' );
		$enable_customize_tutorial_link = admin_url( 'admin.php?page=clientdash_settings&cd_enable_customize_tutorial' );

		include CLIENTDASH_DIR . 'core/plugin-pages/views/settings/other.php';
	}

	/**
	 * Outputs the sidebar pro prompt section.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function sidebar_pro_prompt() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/pro-prompt.php';
	}

	/**
	 * Outputs the sidebar wordpress.org review/support links.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function sidebar_review_support() {

		$rating_confirm = 'onclick="return confirm(\'' .
		                  __( "Is there something we can do better?\\n\\nIf you\\'re having an issue with the plugin, please consider asking us in the support forums instead.\\n\\nIf you still want to leave a low rating, please consider changing it in the future if we fix your issue. Thanks!", 'client-dash' ) .
		                  '\');"';

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/review-support.php';
	}

	/**
	 * Outputs the sidebar Real Big Plugins signup section.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function sidebar_rbp_signup() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/rbp-signup.php';
	}

	/**
	 * Outputs the sidebar admin page actions section.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function sidebar_admin_page_actions() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/admin-page-actions.php';
	}

	/**
	 * Outputs the sidebar settings page actions section.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function sidebar_settings_page_actions() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/settings-page-actions.php';
	}

	/**
	 * Outputs the sidebar helper pages actions section.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function sidebar_helper_pages_actions() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/helper-pages-actions.php';
	}

	/**
	 * Outputs the sidebar addons Real Big Plugins promote section.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	static function sidebar_addons_rbp_promote() {

		include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/addons-rbp-promote.php';
	}
}