<?php

/*
Plugin Name: Client Dash
Description: Creating a more intuitive admin interface for clients.
Version: 1.5
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
	 * property will directly impact file loading. The order of this is also the order
	 * of the output pages and tabs.
	 *
	 * @since Client Dash 1.5
	 */
	public $core_files = array(
		'account'   => array(
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
			'display',
			'icons',
			'webmaster',
			'widgets',
			'tools',
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
		// The default webmaster title that shows up all over the place
		// (normally set to a company name)
		'webmaster_name'             => 'Webmaster',
		//
		// The name of the "main" tab for the webmaster page
		'webmaster_main_tab_name'    => 'Main',
		//
		// The content for the webmaster page
		'webmaster_main_tab_content' => null,
		//
		// Whether or not to show the feed tab at all
		'webmaster_feed'             => null,
		// The url for the RSS feed
		'webmaster_feed_url'         => null,
		//
		// The number of blog posts to show on the feed tab of the webmaster page
		'webmaster_feed_count'       => 5,
		//
		// Page visibility
		'hide_page_account'          => null,
		'hide_page_reports'          => null,
		'hide_page_help'             => null,
		'hide_page_webmaster'        => '1',
		//
		// The default dashicons for everything
		'dashicon_account'           => 'dashicons-id-alt',
		'dashicon_reports'           => 'dashicons-chart-area',
		'dashicon_help'              => 'dashicons-editor-help',
		'dashicon_webmaster'         => 'dashicons-businessman',
		'dashicon_settings'          => 'dashicons-admin-settings',
		//
		// Default widgets
		'widgets' => array(
			array(
				'ID' => 'cd-account',
				'title'    => 'Account',
				'callback' => array( 'ClientDash_Widget_Account', 'widget_content' ),
				'edit_callback' => false
			),
			array(
				'ID' => 'cd-reports',
				'title'    => 'Reports',
				'callback' => array( 'ClientDash_Widget_Reports', 'widget_content' ),
				'edit_callback' => false
			),
			array(
				'ID' => 'cd-help',
				'title'    => 'Help',
				'callback' => array( 'ClientDash_Widget_Help', 'widget_content' ),
				'edit_callback' => false
			),
			array(
				'ID' => 'cd-webmaster',
				'title'    => 'Webmaster',
				'callback' => array( 'ClientDash_Widget_Webmaster', 'widget_content' ),
				'edit_callback' => false
			)
		),
		//
		// This one is big. It's the default visibility of core
		// content sections by role. "0" is showing and "1" is hidden.
		// It is VERY important that these names all match EXACTLY
		'content_sections_roles'     => array(
			'account'   => array(
				'about_you' => array(
					'basic_information' => array(
						'editor'      => 0,
						'author'      => 0,
						'contributor' => 0,
						'subscriber'  => 0
					)
				),
				'sites'     => array(
					'list_of_sites' => array(
						'editor'      => 0,
						'author'      => 0,
						'contributor' => 1,
						'subscriber'  => 1
					)
				)
			),
			'help'      => array(
				'info'   => array(
					'basic_information' => array(
						'editor'      => 0,
						'author'      => 1,
						'contributor' => 1,
						'subscriber'  => 1
					)
				),
				'domain' => array(
					'basic_information' => array(
						'editor'      => 0,
						'author'      => 1,
						'contributor' => 1,
						'subscriber'  => 1
					)
				)
			),
			'reports'   => array(
				'site' => array(
					'basic_information' => array(
						'editor'      => 0,
						'author'      => 0,
						'contributor' => 1,
						'subscriber'  => 1
					)
				)
			),
			'webmaster' => array(
				'your_site' => array(
					'main' => array(
						'editor'      => 0,
						'author'      => 0,
						'contributor' => 0,
						'subscriber'  => 0
					)
				),
				'feed'      => array(
					'feed' => array(
						'editor'      => 0,
						'author'      => 0,
						'contributor' => 0,
						'subscriber'  => 0
					)
				)
			)
		)
	);

	/**
	 * The magical content section property.
	 *
	 * This property will be populated with ALL content. It allows extensions
	 * to add content with little effort.
	 *
	 * @since Client Dash 1.5
	 */
	public $content_sections = array();

	/**
	 * The semi-magical widget property.
	 *
	 * This property will be populated with ALL widgets. It allows extensions
	 * to add widgets with little effort.
	 *
	 * @since Client Dash 1.5
	 */
	public $widgets = array();

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

		// Remove any dashboard settings that may have been previously set
		add_action( 'admin_init', array( $this, 'remove_dashboard_settings' ) );

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

		// Initialize the content sections
		// NOTE: this is intentionally not "admin_init". This needs to fire before "admin_menu",
		// but unfortunately "admin_init" fires after "admin_menu". Which is dumb.
		add_action( 'init', array( $this, 'content_sections_init' ) );

		// Initializes the dashboard widgets
		add_action( 'wp_dashboard_setup', array( $this, 'widgets_init' ), 1001 );
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
			array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable' )
		);

		// The main script for Client Dash
		wp_register_script(
			'cd-ajax',
			plugin_dir_url( __FILE__ ) . 'assets/js/client-dash-ajax.js',
			array( 'jquery' )
		);

		// The script for dealing with the Widgets tab under Settings
		wp_register_script(
			'cd-widgets',
			plugin_dir_url( __FILE__ ) . 'assets/js/cd.widgets.js',
			array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable' )
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
		wp_enqueue_script( 'cd-ajax' );
		wp_enqueue_style( 'cd-main' );

		// Include widgets.js only on widgets page
		if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'widgets' ) {
			wp_enqueue_script( 'cd-widgets' );
		}
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

		// Build style array
		$styles = array(
			'.cd-icon'                                   => array(
				'color' => $active_theme['primary']
			),
			'.cd-icon:hover'                             => array(
				'color' => $active_theme['secondary']
			),
			'.cd-dashicons-grid-item.active .container'  => array(
				'background-color' => $active_theme['tertiary'],
				'color'            => '#eee'
			),
			'#cd-dashicons-selections .dashicons.active' => array(
				'color' => $active_theme['secondary']
			)
		);

		// Build our styles
		if ( ! empty( $styles ) ) {
			echo '<!--Client Dash Colors-->';
			echo '<style>';
			foreach ( $styles as $selector => $properties ) {
				echo "$selector {";
				foreach ( $properties as $property => $value ) {
					echo "$property: $value;";
				}
				echo '}';
			}
			echo '</style>';
		}
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

	/**
	 * Purges all dashboard widget settings that may have been set.
	 *
	 * @since Client Dash 1.5
	 */
	public function remove_dashboard_settings() {

		$ID = get_current_user_id();

		delete_user_meta( $ID, 'meta-box-order_dashboard' );
		delete_user_meta( $ID, 'closedpostboxes_dashboard' );
		delete_user_meta( $ID, 'metaboxhidden_dashboard' );
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

		// Bail if not admin
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		global $wp_meta_boxes;

		// For use on widgets page
		$active_plugins = get_option( 'active_plugins' );
		update_option( 'cd_active_plugins', $active_plugins );

		// Initialize
		$active_widgets = array();

		// This lovely, crazy loop is what gathers all of the widgets and organizes it into MY array
		foreach ( $wp_meta_boxes['dashboard'] as $context => $widgets ) {
			foreach ( $widgets as $priority => $widgets ) {
				foreach ( $widgets as $id => $values ) {
					$active_widgets[ $id ]['title']    = $values['title'];
					$active_widgets[ $id ]['context']  = $context;
					$active_widgets[ $id ]['priority'] = $priority;
					$active_widgets[ $id ]['callback'] = $values['callback'];
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

		foreach ( $active_widgets as $widget => $values ) {
			remove_meta_box( $widget, 'dashboard', $values['context'] );
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

	/**
	 * Initializes all content sections.
	 *
	 * Cycles through all content sections that have been set and removes any
	 * that should not be available to the current role.
	 *
	 * @since Client Dash 1.5
	 */
	public function content_sections_init() {

		$current_role           = $this->get_user_role();
		$content_sections_roles = get_option( 'cd_content_sections_roles', $this->option_defaults['content_sections_roles'] );

		// Cycles through all content sections to see if they're disabled
		foreach ( $this->content_sections as $page => $tabs ) {
			foreach ( $tabs as $tab => $blocks ) {
				foreach ( $blocks as $name => $info ) {
					if ( ! empty( $content_sections_roles[ $page ][ $tab ][ $name ][ $current_role ] )
					     && $content_sections_roles[ $page ][ $tab ][ $name ][ $current_role ] != '0'
					) {
						// If they are disabled, unset it and then remove tab and page if necessary
						unset( $this->content_sections[ $page ][ $tab ][ $name ] );

						// Remove tab
						if ( empty( $this->content_sections[ $page ][ $tab ] ) ) {
							unset( $this->content_sections[ $page ][ $tab ] );
						}

						// Remove page
						if ( empty( $this->content_sections[ $page ] ) ) {
							unset( $this->content_sections[ $page ] );
						}
					}
				}
			}
		}
	}

	/**
	 * Adds our widgets to the dashboard.
	 *
	 * @since Client Dash 1.5
	 */
	public function widgets_init() {
		$widgets = get_option( 'cd_widgets', $this->option_defaults['widgets'] );

		foreach ( $widgets as $widget ) {
			add_meta_box(
				$widget['ID'],
				$widget['title'],
				$widget['callback'],
				'dashboard',
				'normal',
				'core'
			);
		}
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

		// Require our AJAX file
		require_once( plugin_dir_path( __FILE__ ) . 'core/ajax.php' );

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