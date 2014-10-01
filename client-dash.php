<?php

/*
Plugin Name: Client Dash
Description: Creating a more intuitive admin interface for clients.
Version: 1.6.4
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

// TODO Allow dashboard meta box styling to be disabled (possibly extension?)
// TODO Correctly line break documentation to PHP guideline

// NEXTUPDATE 1.7 - Themes

// FUTUREBUILD Only require page / tab specific files WHEN they are needed. Not always.

// Require the functions class first so we can extend it
include_once( plugin_dir_path( __FILE__ ) . 'core/functions.php' );

/**
 * Class ClientDash
 *
 * The main class for Client Dash. This class does everything needed to
 * initialize the plugin.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Base Functionality
 *
 * @since Client Dash 1.5
 */
class ClientDash extends ClientDash_Functions {

	/**
	 * Current version of Client Dash.
	 *
	 * @since Client Dash 1.5
	 */
	public $version = '1.6.4';

	/**
	 * The path to the plugin.
	 *
	 * @since Client Dash 1.6
	 */
	public $path;

	/**
	 * A list of all tab files to include.
	 *
	 * First level in the array are pages and second level are tabs. Altering this
	 * property will directly impact file loading. The order of this is also the order
	 * of the output pages and tabs.
	 *
	 * @since Client Dash 1.5
	 */
	public static $core_files = array(
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
			'menus',
			'icons',
			'webmaster',
			'widgets',
			'tools',
			'addons'
		)
	);

	/**
	 * Client Dash Core available widgets.
	 *
	 * Private.
	 *
	 * @since Client Dash 1.6
	 */
	public static $_cd_widgets = array(
		'cd_account'   => array(
			'title'       => 'Account',
			'ID'          => 'cd_account',
			'description' => 'The core Client Dash account page.',
			'_cd_core'    => '1',
			'_callback'   => array( 'ClientDash_Widget_Account', 'widget_content' ),
		),
		'cd_help'      => array(
			'title'       => 'Help',
			'ID'          => 'cd_help',
			'description' => 'The core Client Dash help page.',
			'_cd_core'    => '1',
			'_callback'   => array( 'ClientDash_Widget_Help', 'widget_content' ),
		),
		'cd_reports'   => array(
			'title'       => 'Reports',
			'ID'          => 'cd_reports',
			'description' => 'The core Client Dash reports page.',
			'_cd_core'    => '1',
			'_callback'   => array( 'ClientDash_Widget_Reports', 'widget_content' ),
		),
		'cd_webmaster' => array(
			'title'       => 'Webmaster',
			'ID'          => 'cd_webmaster',
			'description' => 'The core Client Dash webmaster page.',
			'_cd_core'    => '1',
			'_callback'   => array( 'ClientDash_Widget_Webmaster', 'widget_content' ),
		),
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
		// Whether or not to show the feed tab
		'webmaster_feed'             => null,
		// The url for the RSS feed
		'webmaster_feed_url'         => null,
		//
		// The number of blog posts to show on the feed tab of the webmaster page
		'webmaster_feed_count'       => 5,
		//
		// The default dashicons for everything
		'dashicon_account'           => 'dashicons-id-alt',
		'dashicon_reports'           => 'dashicons-chart-area',
		'dashicon_help'              => 'dashicons-editor-help',
		'dashicon_webmaster'         => 'dashicons-businessman',
		'dashicon_settings'          => 'dashicons-admin-settings',
		//
		// Default widgets
		'widgets'                    => array(
			array(
				'ID'                => 'cd_account',
				'title'             => 'Account',
				'callback'          => array( 'ClientDash_Widget_Account', 'widget_content' ),
				'settings_callback' => false,
				'cd_core'           => true,
				'cd_page'           => 'account'
			),
			array(
				'ID'                => 'cd_reports',
				'title'             => 'Reports',
				'callback'          => array( 'ClientDash_Widget_Reports', 'widget_content' ),
				'settings_callback' => false,
				'cd_core'           => true,
				'cd_page'           => 'reports'
			),
			array(
				'ID'                => 'cd_help',
				'title'             => 'Help',
				'callback'          => array( 'ClientDash_Widget_Help', 'widget_content' ),
				'settings_callback' => false,
				'cd_core'           => true,
				'cd_page'           => 'help'
			),
			array(
				'ID'                => 'cd_webmaster',
				'title'             => 'Webmaster',
				'callback'          => array( 'ClientDash_Widget_Webmaster', 'widget_content' ),
				'settings_callback' => false,
				'cd_core'           => true,
				'cd_page'           => 'webmaster'
			),
		),
		//
		// This one is big. It's the default visibility of core content sections by role.
		// It is VERY important that these names all match EXACTLY
		'content_sections_roles'     => array(
			'account'   => array(
				'about_you' => array(
					'basic_information' => array(
						'editor'      => 'visible',
						'author'      => 'visible',
						'contributor' => 'visible',
						'subscriber'  => 'visible'
					)
				),
				'sites'     => array(
					'list_of_sites' => array(
						'editor'      => 'visible',
						'author'      => 'visible',
						'contributor' => 'hidden',
						'subscriber'  => 'hidden'
					)
				)
			),
			'help'      => array(
				'info'   => array(
					'basic_information' => array(
						'editor'      => 'visible',
						'author'      => 'hidden',
						'contributor' => 'hidden',
						'subscriber'  => 'hidden'
					)
				),
				'domain' => array(
					'basic_information' => array(
						'editor'      => 'visible',
						'author'      => 'hidden',
						'contributor' => 'hidden',
						'subscriber'  => 'hidden'
					)
				)
			),
			'reports'   => array(
				'site' => array(
					'basic_information' => array(
						'editor'      => 'visible',
						'author'      => 'visible',
						'contributor' => 'hidden',
						'subscriber'  => 'hidden'
					)
				)
			),
			'webmaster' => array(
				'main' => array(
					'main' => array(
						'editor'      => 'hidden',
						'author'      => 'hidden',
						'contributor' => 'hidden',
						'subscriber'  => 'hidden'
					)
				),
				'feed' => array(
					'feed' => array(
						'editor'      => 'hidden',
						'author'      => 'hidden',
						'contributor' => 'hidden',
						'subscriber'  => 'hidden'
					)
				)
			)
		),
	);

	/**
	 * Items to remove from the WP admin bar by default.
	 *
	 * @since Client Dash 1.5
	 */
	public $remove_menu_items = array(
		'menu'    => array(),
		'submenu' => array(
			array(
				'menu_slug'    => 'index.php',
				'submenu_slug' => 'my-sites.php'
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
	 * A duplicate of content sections that is NOT filtered.
	 *
	 * @since Client Dash 1.5
	 */
	public $content_sections_unmodified = array();

	/**
	 * Data to be sent to the main JS file.
	 *
	 * @since Client Dash 1.6
	 */
	public $jsData = array();

	/**
	 * Widgets that are active by default.
	 *
	 * @since Client Dash 1.6
	 */
	public $active_widgets = array();

	/**
	 * Constructs the class.
	 *
	 * Here we will include ALL necessary files as well as perform ALL
	 * necessary actions and filters.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		// Update all options if not set
		$init_reset = get_option( 'cd_initial_reset' );
		if ( empty( $init_reset ) ) {
			add_action( 'admin_init', array( $this, 'reset_settings' ) );
			update_option( 'cd_initial_reset', true );
		}

		// Set the path
		$this->path = plugin_dir_path( __FILE__ );

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

		// Remove items from admin bar
		add_action( 'admin_menu', array( $this, 'remove_admin_bar_menus' ), 999 );

		// Remove the WP logo
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_wp_logo' ), 0 );

		// Remove some default toolbar items
		add_action( 'admin_menu', array( $this, 'remove_toolbar_items' ), 999 );

		// Save all of the active widgets into an option for use on non-dashboard pages
		add_action( 'wp_dashboard_setup', array( $this, 'get_active_widgets' ), 997 );

		// Removes default dashboard widgets
		add_action( 'wp_dashboard_setup', array( $this, 'remove_default_dashboard_widgets' ), 998 );

		// Initializes the dashboard widgets
		add_action( 'wp_dashboard_setup', array( $this, 'add_new_widgets' ), 999 );

		// Removes the WordPress welcome panel from the dashboard
		remove_action( 'welcome_panel', 'wp_welcome_panel' );

		// Remove the screen options from the dashboard
		add_action( 'admin_head-index.php', array( $this, 'remove_screen_options' ) );

		// Initialize the content sections
		// NOTE: this is intentionally not "admin_init". This needs to fire before "admin_menu",
		// but unfortunately "admin_init" fires after "admin_menu". Which is dumb.
		add_action( 'init', array( $this, 'content_sections_init' ) );

		// Shows any admin notices
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
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
			plugin_dir_url( __FILE__ ) . 'assets/js/clientdash.min.js',
			array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-effects-shake' ),
			WP_DEBUG == false ? $this->version : time()
		);

		wp_localize_script( 'cd-main', 'cdData', $this->jsData );

		// The main stylesheet for Client Dash
		wp_register_style(
			'cd-main',
			plugins_url( 'assets/css/clientdash.min.css', __FILE__ ),
			array(),
			WP_DEBUG == false ? $this->version : time()
		);
	}

	/**
	 * Enqueues all Client Dash scripts.
	 *
	 * @since Client Dash 1.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		// Don't add scripts for network admin
		if ( is_multisite() && ! empty( $screen ) && $screen->in_admin( 'network' ) ) {
			return;
		}

		wp_enqueue_script( 'cd-main' );
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
				'color' => $active_theme['primary'],
			),
			'.cd-icon:hover'                             => array(
				'color' => $active_theme['secondary'],
			),
			'.cd-dashicons-grid-item.active .container'  => array(
				'background-color' => $active_theme['tertiary'],
				'color'            => '#eee',
			),
			'#cd-dashicons-selections .dashicons.active' => array(
				'color' => $active_theme['secondary'],
			),
			'.cd-progress-bar .cd-progress-bar-inner'    => array(
				'background-color' => $active_theme['secondary'],
			),
			'.cd-menu-icon-selector li:hover .dashicons' => array(
				'color' => $active_theme['secondary'] . '!important',
			),
			'.cd-menu-icon-selector .active .dashicons'  => array(
				'color' => $active_theme['secondary'] . '!important',
			),
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
	 *
	 * @param array $columns The supplied columns.
	 *
	 * @return array The altered columns.
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
	 * Removes some default admin items.
	 *
	 * @since Client Dash 1.0
	 *
	 * @param mixed $wp_admin_bar The supplied admin bar object.
	 */
	public function remove_admin_bar_menus() {

		foreach ( $this->remove_menu_items['menu'] as $item ) {
			remove_menu_page( $item );
		}

		foreach ( $this->remove_menu_items['submenu'] as $item ) {
			remove_submenu_page( $item['menu_slug'], $item['submenu_slug'] );
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

		// This lovely, crazy loop is what gathers all of the widgets and organizes it into MY array
		foreach ( $wp_meta_boxes['dashboard'] as $context => $priorities ) {
			foreach ( $priorities as $priority => $widgets ) {
				foreach ( $widgets as $id => $values ) {
					$this->active_widgets[ $id ]['title']    = $values['title'];
					$this->active_widgets[ $id ]['context']  = $context;
					$this->active_widgets[ $id ]['priority'] = $priority;
					$this->active_widgets[ $id ]['callback'] = $values['callback'];
				}
			}
		}

		// Only update for Admin
		if ( current_user_can( 'manage_options' ) ) {

			// Update these values so that we can signal the user on the widgets page to come back here
			$active_plugins = get_option( 'active_plugins' );
			update_option( 'cd_active_plugins', $active_plugins );
			update_option( 'cd_active_widgets', $this->active_widgets );
		}
	}

	/**
	 * Removes all default dashboard widgets.
	 *
	 * @since Client Dash 1.1
	 */
	public function remove_default_dashboard_widgets() {

		global $wp_meta_boxes;

		// Allow removing/adding of widgets to ditch externally
		$active_widgets = apply_filters( 'cd_remove_widgets', $this->active_widgets );

		foreach ( $active_widgets as $widget => $values ) {
			remove_meta_box( $widget, 'dashboard', $values['context'] );
		}

		$wp_meta_boxes = array();
	}

	/**
	 * Adds our widgets to the dashboard.
	 *
	 * @since Client Dash 1.5
	 */
	public function add_new_widgets() {

		global $wp_meta_boxes;

		$sidebars = get_option( 'sidebars_widgets' );
		
		/**
		 * This allows the currently visible dashboard "sidebar" to be changed from the default.
		 *
		 * @since Client Dash 1.6.4
		 */
		$current_sidebar = apply_filters( 'cd_dashboard_widgets_sidebar', "cd-dashboard" );

		// If no widgets have been set up yet, just use default ones. Otherwise, the new
		// widgets need to be translated
		if ( empty( $sidebars[ $current_sidebar ] ) ) {


			// MAYBETODO Make widgets init on startup so this can just be a "return;"
			$new_widgets = $this::$_cd_widgets;
		} else {

			// Cycle through each widget
			foreach ( $sidebars[ $current_sidebar ] as $ID ) {

				// Break apart the ID
				preg_match_all( "/(.*)(-\d+)/", $ID, $matches );
				$ID_base   = $matches[1][0];
				$ID_number = str_replace( '-', '', $matches[2][0] );

				// Get all widgets of this type
				$widgets = get_option( "widget_{$ID_base}" );

				// Get the current widget
				$widget = $widgets[ $ID_number ];

				// Set the ID
				$widget['ID'] = isset( $widget['_cd_extension'] ) && $widget['_cd_extension'] == '1' ? $ID : $ID_base;

				// Add it on
				$new_widgets[] = $widget;
			}
		}

		if ( ! empty( $new_widgets ) ) {
			foreach ( $new_widgets as $widget ) {

				// Pass over if is a plugin / theme / WP Core widget and didn't original exist for current user
				if ( isset( $widget['plugin'] ) && $widget['plugin'] == '1' && ! array_key_exists( $widget['ID'], $this->active_widgets ) ) {
					return;
				}

				// Remove old value
				unset( $new_ID );

				// Figure out the title
				$title = ! empty( $widget['title'] ) ? $widget['title'] : $widget['_original_title'];

				// Client Dash core widgets conditional visibility
				if ( isset( $widget['_cd_core'] ) && $widget['_cd_core'] === '1' ) {
					if ( ! isset( $this->content_sections[ str_replace( 'cd_', '', $widget['ID'] ) ] ) ) {
						continue;
					}
				}

				// If this ID already exists, change the ID to something new
				if ( ! empty( $wp_meta_boxes ) && $this->array_key_exists_r( $widget['ID'], $wp_meta_boxes ) ) {

					// If the ID contains "_duplicate_{n}", then we need another new ID, so
					// we use a prep_replace_callback to replace the "_duplicate_{n}" with
					// "_duplicate_{n+1}". Otherwise, just add on the "_duplicate_1" to the
					// end
					foreach ( $wp_meta_boxes['dashboard'] as $context ) {
						foreach ( $context as $priority ) {
							foreach ( $priority as $ID => $wp_widget ) {
								if ( strpos( $ID, $widget['ID'] ) !== false ) {
									if ( preg_match( '/(_duplicate_)(\d+)/', $ID ) ) {
										$new_ID = preg_replace_callback(
											'/(_duplicate_)(\d+)/',
											array( $this, 'replace_count' ),
											$ID
										);
									} else {
										$new_ID = "$widget[ID]_duplicate_1";
									}
								}
							}
						}
					}
				}

				// For webmaster widget
				if ( $widget['ID'] == 'cd_webmaster' ) {
					$title = get_option( 'cd_webmaster_name', $this->option_defaults['webmaster_name'] );
				}

				// If callback should be an object
				if ( isset( $widget['_is_object'] ) && $widget['_is_object'] === '1' ) {
					if ( ! class_exists( $widget['_callback'][0] ) ) {
						continue;
					}
					$widget['_callback'][0] = new $widget['_callback'][0];
				}

				add_meta_box(
					isset( $new_ID ) ? $new_ID : $widget['ID'],
					$title,
					$widget['_callback'],
					'dashboard',
					'normal',
					'core'
				);
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

	/**
	 * Initializes all content sections.
	 *
	 * Cycles through all content sections that have been set and removes any
	 * that should not be available to the current role.
	 *
	 * @since Client Dash 1.5
	 */
	public function content_sections_init() {

		$current_role = $this->get_user_role();
		if ( $current_role == 'administrator' ) {
			return;
		}

		$content_sections_roles = get_option( 'cd_content_sections_roles', $this->option_defaults['content_sections_roles'] );

		// Cycles through all content sections to see if they're disabled
		foreach ( $this->content_sections as $page => $tabs ) {
			foreach ( $tabs as $tab => $props ) {
				foreach ( $props['content-sections'] as $ID => $info ) {

					// Move on if it's been unset
					if ( ! isset( $content_sections_roles[ $page ][ $tab ][ $ID ][ $current_role ] ) ) {
						continue;
					}

					// Get our values for easier use
					$option_value = $content_sections_roles[ $page ][ $tab ][ $ID ][ $current_role ];

					// See if this is disabled
					if ( $option_value == 'hidden' ) {

						// If they are disabled, unset it and then remove tab and page if necessary
						unset( $this->content_sections[ $page ][ $tab ]['content-sections'][ $ID ] );

						// Remove tab
						if ( empty( $this->content_sections[ $page ][ $tab ]['content-sections'] ) ) {
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
	 * Adds admin notices based on pre-specified query args.
	 *
	 * @since Client Dash 1.5
	 */
	public function admin_notices() {

		// ==============================================================================
		// Notice for after visiting the dashboard when told to from Settings -> Widgets
		// ==============================================================================
		if ( isset( $_GET['cd_update_dash'] ) ) {
			?>
			<div class="updated">
				<p>
					Great! Thanks! Now you can return to the settings <a
						href="<?php echo $this->get_settings_url( 'widgets' ); ?>">here</a>.
				</p>
			</div>
		<?php
		}

		// ==============================================================================
		// Notice for if roles are added or taken away since visiting the display page
		// ==============================================================================
		$cd_existing_roles = get_option( 'cd_existing_roles' );
		$existing_roles    = get_editable_roles();

		// Resets cd_existing_roles on save or for the first time
		if ( get_option( 'cd_display_settings_updated', false ) || empty( $cd_existing_roles ) ) {
			update_option( 'cd_existing_roles', get_editable_roles() );
			$cd_existing_roles = get_editable_roles();
		}

		if ( $existing_roles != $cd_existing_roles && current_user_can( 'manage_options' ) ) {
			?>
			<div class="error">
				<p>
					It seems that there are either new roles, or some roles have been deleted, or the roles have been
					modified in some other way. Please visit the <a
						href="<?php echo $this->get_settings_url( 'display' ); ?>">Display Settings</a> and confirm that
					the role display settings are still to your liking. (this message will go away once you hit "Save
					Changes" on the display settings page).
				</p>
			</div>
		<?php
		}

		// Ensure option is always unset (except right before the initial checking)
		delete_option( 'cd_display_settings_updated' );
	}
}

// Initialize the main Client Dash object
$ClientDash = new ClientDash();

// Require other files
include_once( plugin_dir_path( __FILE__ ) . 'core/include-files.php' );