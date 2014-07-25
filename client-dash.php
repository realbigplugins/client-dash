<?php

/*
Plugin Name: Client Dash
Description: Creating a more intuitive admin interface for clients.
Version: 1.4
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

// Require the functions class first so we can extend it
require_once( plugin_dir_path( __FILE__ ) . 'core/functions.php' );

/**
 * Class ClientDash
 *
 * The main class for Client Dash. This class does everything needed to
 * initialize the plugin.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash extends ClientDash_Functions {
	/**
	 * A list of all tab files to include.
	 *
	 * First level in the array are pages and second level are tabs. Altering this
	 * property will directly impact file loading.
	 *
	 * @since Client Dash 1.5
	 */
	public $core_files = array(
		'account' => array(
			'about',
			'sites'
		),
		'help'      => array(
			'info',
			'domain'
		),
		'reports'   => array(
			'site'
		),
		'webmaster' => array(
			'main',
			'feed'
		),
		'settings'  => array(
			'general',
			'icons',
			'webmaster',
			'roles',
			'addons'
		)
	);

	/**
	 * Declaring all widgets that exist within Client Dash.
	 *
	 * @since Client Dash 1.1
	 */
	public $core_widgets = array(
		'account',
		'help',
		'reports',
		'webmaster'
	);

	/**
	 * The current admin color scheme.
	 *
	 * @since Client Dash 1.5
	 */
	public $admin_colors;

	/**
	 * Default option settings for Client Dash options (settings).
	 *
	 * @since Client Dash 1.0
	 */
	public $option_defaults = array(
		'webmaster_name'          => 'Webmaster',
		'webmaster_enable'        => false,
		'webmaster_main_tab_name' => 'Main',
		'webmaster_feed_count'    => 5,
		'dashicon_account'        => 'dashicons-id-alt',
		'dashicon_reports'        => 'dashicons-chart-area',
		'dashicon_help'           => 'dashicons-editor-help',
		'dashicon_webmaster'      => 'dashicons-businessman',
		'dashicon_settings'       => 'dashicons-admin-settings'
	);

	/**
	 * The magical content block property.
	 *
	 * This property will be populated with ALL content. It allows extensions
	 * to add content with little effort.
	 *
	 * @since Client Dash 1.4
	 */
	public $content_blocks;

	/**
	 * Constructs the class.
	 *
	 * Here we will include ALL necessary files as well as perform ALL
	 * necessary actions and filters.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {
		// Register and enqueue our scripts / styles
		add_action( 'admin_init', array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Save our color scheme and then use it
		add_action( 'admin_init', array( $this, 'save_admin_colors' ) );
		add_action( 'admin_head', array( $this, 'assign_admin_colors' ) );

		// Make the dashboard one column
		add_filter( 'screen_layout_columns', array( $this, 'alter_dashboard_columns' ) );
		add_filter( 'get_user_option_screen_layout_dashboard', array( $this, 'return_1' ) );

		// Remove my sites from admin bar
		add_action( 'admin_bar_menu', array( $this, 'remove_my_sites' ), 999 );

		// Remove the WP logo
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_wp_logo' ), 0 );

		// Remove some default toolbar items
		add_action( 'admin_menu', array( $this, 'remove_toolbar_items' ), 999 );

		// Save all of the active widgets into an option for use on non-dashboard pages
		add_action( 'wp_dashboard_setup', array( $this, 'get_active_widgets' ), 100 );

		// Removes default dashboard widgets
		add_action( 'wp_dashboard_setup', array( $this, 'remove_default_dashboard_widgets' ), 1000 );

		// Removes the WordPress welcome panel from the dashboard
		remove_action( 'welcome_panel', 'wp_welcome_panel' );

		// Remove the screen options from the dashboard
		add_action( 'admin_head-index.php', array( $this, 'remove_screen_options' ) );
	}

	/**
	 * Registers all Client Dash scripts.
	 *
	 * @since Client Dash 1.0
	 */
	public function register_scripts() {
		// The main script for Client Dash
		wp_register_script(
			'cd-main',
			plugin_dir_url( __FILE__ ) . 'assets/js/client-dash.js',
			array( 'jquery', 'jquery-ui-sortable' )
		);

		// The main stylesheet for Client Dash
		wp_register_style(
			'cd-main',
			plugins_url( 'assets/css/client-dash.css', __FILE__ ),
			array(),
			null
		);
	}

	/**
	 * Enqueues all Client Dash scripts.
	 *
	 * @since Client Dash 1.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'cd-main' );
		wp_enqueue_style( 'cd-main' );
	}

	/**
	 * Saves our color scheme for later.
	 *
	 * Normally, WordPress ditches these global variables and makes them
	 * unavailable, not sure why. So I'm storing them in my own variables.
	 *
	 * @since Client Dash 1.0
	 */
	public function save_admin_colors() {
		global $_wp_admin_css_colors;
		$this->admin_colors = $_wp_admin_css_colors;
	}

	/**
	 * Assigns all the admin colors to Client Dash classes.
	 *
	 * @since Client Dash 1.1
	 */
	public function assign_admin_colors() {
		// Get the current color scheme
		$active_theme = $this->get_color_scheme();

		echo '<style>';
		echo '.cd-icon{ color: ' . $active_theme['primary'] . '}';
		echo '.cd-icon:hover{ color: ' . $active_theme['secondary'] . '}';
		echo '.cd-dashicons-grid-item.active .container{ ';
		echo 'background-color: ' . $active_theme['tertiary'];
		echo 'color: #eee;';
		echo '}';
		echo '#cd-dashicons-selections .dashicons.active{ color: ' . $active_theme['secondary'] . '}';
		echo '</style>';
	}

	/**
	 * Force dashboard widgets to one column.
	 *
	 * @since Client Dash 1.4
	 */
	public function alter_dashboard_columns( $columns ) {
		$columns['dashboard'] = 1;

		return $columns;
	}

	public function remove_dashboard_settings() {
		$dashboard_order = get_user_meta( get_current_user_id(), 'meta-box-order_dashboard', true );
		$dashboard_closed = get_user_meta( get_current_user_id(), 'closedpostboxes_dashboard', true );
		$dashboard_hidden = get_user_meta( get_current_user_id(), 'metaboxhidden_dashboard', true );

//		if ( ! empty( $dashboard_order ) ) ;
	}

	/**
	 * Gets rid of "my sites" if in multi-site environment.
	 *
	 * @since Client Dash 1.0
	 *
	 * @param mixed $wp_admin_bar The supplied admin bar object.
	 */
	public function remove_my_sites( $wp_admin_bar ) {
		if ( ! current_user_can( 'manage_network' ) ) {
			$wp_admin_bar->remove_node( 'my-sites' );
		}
	}

	/**
	 * Removes the WordPress logo from the admin bar.
	 *
	 * @since Client Dash 1.0
	 */
	public function remove_wp_logo() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu( 'wp-logo' );
	}

	/**
	 * Removes default toolbar items.
	 *
	 * @since Client Dash 1.5
	 */
	public function remove_toolbar_items() {
		remove_submenu_page( 'index.php', 'update-core.php' );
	}
	/**
	 * Gets all of the active dashboard widgets.
	 *
	 * @since Client Dash 1.1
	 */
	public function get_active_widgets() {
		global $wp_meta_boxes;

		// Initialize
		$active_widgets = array();

		// This lovely, crazy loop is what gathers all of the widgets and organizes it into MY array
		foreach ( $wp_meta_boxes['dashboard'] as $context => $widgets ) {
			foreach ( $widgets as $priority => $widgets ) {
				foreach ( $widgets as $id => $values ) {
					$active_widgets[ $id ]['title']    = $values['title'];
					$active_widgets[ $id ]['context']  = $context;
					$active_widgets[ $id ]['priority'] = $priority;
				}
			}
		}

		// Unset OUR widgets
		foreach ( $this->core_widgets as $widget ) {
			unset( $active_widgets[ 'cd-' . $widget ] );
		}

		update_option( 'cd_active_widgets', $active_widgets );
	}

	/**
	 * Removes all default dashboard widgets.
	 *
	 * @since Client Dash 1.1
	 */
	public function remove_default_dashboard_widgets() {
		$active_widgets = get_option( 'cd_active_widgets', null );

		// If no active widgets (which is never...), bail
		if ( ! $active_widgets ) {
			return;
		}

		// Don't remove selected widgets
		$dont_remove = get_option( 'cd_remove_which_widgets' );
		if ( $dont_remove ) {
			foreach ( $dont_remove as $widget ) {
				unset( $active_widgets[ $widget ] );
			}
		}

		// Allow removing/adding of widgets to ditch externally
		$active_widgets = apply_filters( 'cd_remove_widgets', $active_widgets );

		if ( current_user_can( 'publish_posts' ) ) {
			foreach ( $active_widgets as $widget => $values ) {
				remove_meta_box( $widget, 'dashboard', $values['context'] );
			}
		}
	}

	/**
	 * Remove Screen Options and Help on Dashboard.
	 *
	 * @since Client Dash 1.2
	 */
	public function remove_screen_options() {
		// Removes the "Help" button from the dashboard
		add_filter( 'contextual_help', array( $this, 'remove_help_tab' ), 999, 3 );

		// Removes the "Screen Options" from the dashboard
		add_filter( 'screen_options_show_screen', '__return_false' );
	}

	/**
	 * Removes the help tab.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param $old_help
	 * @param $screen_id
	 * @param $screen
	 *
	 * @return mixed
	 */
	public function remove_help_tab( $old_help, $screen_id, $screen ) {
		$screen->remove_help_tabs();

		return $old_help;
	}
}

// Initialize the main Client Dash object
$ClientDash = new ClientDash();

/**
 * Class ClientDash_RequireFiles
 *
 * This class requires all of the various files needed to make Client
 * Dash core run.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_RequireFiles extends ClientDash {

	/**
	 * Requires all necessary files for Client Dash.
	 *
	 * Also initiates all page and tab classes.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {
		// Core page and tab files
		foreach ( $this->core_files as $page => $tabs ) {
			// Include page file
			require_once( plugin_dir_path( __FILE__ ) . 'core/pages/' . $page . '.php' );

			// Initiate the new page class to launch it
			$page_class = 'ClientDash_Page_' . $page;
			new $page_class;

			foreach ( $tabs as $tab ) {
				// Include tabs
				require_once( plugin_dir_path( __FILE__ ) . 'core/tabs/' . $page . '/' . $tab . '.php' );

				// Initiate the new tab class to launch it
				$tab_class = 'ClientDash_Core_Page_' . $page . '_Tab_' . $tab;
				new $tab_class;
			}
		}

		// Core widget files
		foreach ( $this->core_widgets as $widget ) {
			require_once( plugin_dir_path( __FILE__ ) . 'core/widgets/' . $widget . '.php' );

			// Initiate the new widget class to launch it
			$widget_class = 'ClientDash_Widget_' . $widget;
			new $widget_class;
		}
	}
}

// Initialize the class into nothing in order to run it and require all of
// the needed files
new ClientDash_RequireFiles();