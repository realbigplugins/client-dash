<?php

// MAYBETODO Translate pre-1.6 widgets to post

/**
 * Class ClientDash_Page_Settings_Tab_Widgets
 *
 * Adds the core content section for Settings -> Widgets.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_Widgets extends ClientDash {

	/**
	 * Client Dash Core available widgets.
	 *
	 * Private.
	 *
	 * @since Client Dash 1.6
	 */
	public static $_cd_widgets = array(
		'account'   => array(
			'title'       => 'Client Dash Account',
			'description' => 'The core Client Dash account page.',
			'callback'    => array( 'ClientDash_Widget_Account', 'widget_content' ),
		),
		'help'      => array(
			'title'       => 'Client Dash Help',
			'description' => 'The core Client Dash help page.',
			'callback'    => array( 'ClientDash_Widget_Help', 'widget_content' ),
		),
		'reports'   => array(
			'title'       => 'Client Dash Reports',
			'description' => 'The core Client Dash reports page.',
			'callback'    => array( 'ClientDash_Widget_Reports', 'widget_content' ),
		),
		'webmaster' => array(
			'title'       => 'Client Dash Webmaster',
			'description' => 'The core Client Dash webmaster page.',
			'callback'    => array( 'ClientDash_Widget_Webmaster', 'widget_content' ),
		),
	);

	/**
	 * Available widgets.
	 *
	 * @since Client Dash 1.6
	 */
	public $widgets = [ ];

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		global $ClientDash;

		// Anything in here will ONLY apply to this particular settings page
		if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'cd_settings'
		       && isset( $_GET['tab'] ) && $_GET['tab'] == 'widgets'
		     ) || isset( $_POST['cd-widgets'] )
		) {

			// Remove form wrap and submit button
			add_filter( 'cd_settings_form_wrap', '__return_false' );
			add_filter( 'cd_submit', '__return_false' );

			// Necessary scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Add nav-menus body class for our custom CD page
			add_filter( 'admin_body_class', array( $this, 'add_nav_menu_class' ) );

			// Remove all existing widgets
			add_action( 'widgets_init', array( $this, 'remove_existing_widgets' ), 99 );

			// Add extra field(s) to the widget form
			add_action( 'in_widget_form', array( $this, 'add_extra_fields' ), 10, 2 );

			// Register our dashboard
			register_sidebar( array(
				'id'   => 'cd-dashboard',
				'name' => 'Dashboard',
			) );

			// Include widget interface
			include_once( $ClientDash->path . 'core/tabs/settings/widgets/widget-interface.php' );

			// Create widgets from CD Core
			add_action( 'widgets_init', array( $this, 'create_cd_core_widgets' ), 100 );

			// Create widgets from existing dashboard widgets
			add_action( 'widgets_init', array( $this, 'create_existing_dashboard_widgets' ), 100 );
		}

		$this->add_content_section( array(
			'name'     => 'Core Settings Widgets',
			'page'     => 'Settings',
			'tab'      => 'Widgets',
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * Include necessary scripts for the page.
	 *
	 * @since Client Dash 1.6
	 */
	public function enqueue_scripts() {

		$scripts = array(
			'admin-widgets',
			'jquery-ui-widgets',
			'jquery-ui-draggable',
			'jquery-ui-sortable',
			'jquery-effects-shake',
		);

		foreach ( $scripts as $script ) {
			wp_enqueue_script( $script );
		}
	}

	/**
	 * Adds the nav-menu body class (which is normally present on the nav-menus page and necessary).
	 *
	 * @since Client Dash 1.6
	 */
	public function add_nav_menu_class( $classes ) {
		return $classes . ' cd-widgets widgets-php';
	}

	/**
	 * Removes existing widgets.
	 *
	 * @since Client Dash 1.6
	 */
	public function remove_existing_widgets() {

		// Get the registered widgets, and if there are none, exit
		$widgets = ! empty( $GLOBALS['wp_widget_factory'] ) ? $GLOBALS['wp_widget_factory'] : false;
		if ( ! $widgets ) {
			return;
		}

		// Remove ALL but those that are explicitly stated as allowed
		foreach ( $widgets->widgets as $widget_class => $widget ) {

			/**
			 * Allows specific default available widgets to not be removed. Simply return false after
			 * checking if the current widget is the one you want to keep.
			 *
			 * @since Client Dash 1.6
			 */
			if ( ! apply_filters( 'cd_widgets_remove_default', true, $widget_class, $widget ) ) {
				return;
			}

			unregister_widget( $widget_class );
		}
	}

	/**
	 * Adds CD Core widgets to available widgets.
	 *
	 * @since Client Dash 1.6
	 */
	public function create_cd_core_widgets() {

		foreach ( $this::$_cd_widgets as $widget_ID => $widget ) {

			/**
			 * Allows filtering of supplied values for CD Core available widgets.
			 *
			 * @since Client Dash 1.6
			 */
			$args = apply_filters( 'cd_widgets_available_cd_core', array(
				'id'          => "cd_$widget_ID",
				'title'       => $widget['title'],
				'description' => $widget['description'],
				'callback'    => $widget['callback'],
				'cd_core'     => '1',
			) );

			$this->register_widget( $args );
		}
	}

	/**
	 * Adds plugin / theme / WP Core widgets to available widgets.
	 *
	 * @since Client Dash 1.6
	 */
	public function create_existing_dashboard_widgets() {

		foreach ( get_option( 'cd_active_widgets', array() ) as $widget_ID => $widget ) {

			/**
			 * Allows filtering of supplied values for plugin / theme / WP Core available widgets.
			 *
			 * @since Client Dash 1.6
			 */
			$args = apply_filters( 'cd_widgets_available_plugin', array(
				'id'          => $widget_ID,
				'title'       => $widget['title'],
				'description' => null,
				'callback'    => $widget['callback'],
			) );

			$this->register_widget( $args );
		}
	}

	/**
	 * Registers a widget, CD style.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param array $args Args for the available widget.
	 */
	public function register_widget( $args ) {

		global $wp_widget_factory;

		// Add our new widget to the array that the interface will be pulling from
		$this->widgets[] = $args;

		// Now add the widget into the WP Widget Factory
		$wp_widget_factory->widgets[ $args['id'] ] = new CD_Widget();
	}

	/**
	 * Adds this extra field on initial saving of widgets so on AJAX loads, this class still gets loaded.
	 *
	 * @since Client Dash 1.6
	 */
	public function add_extra_fields() {

		echo '<input type="hidden" name="cd-widgets" value="1" />';
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		// Check to see if any plugin modifications have been made, and notify the user
		$active_plugins    = get_option( 'active_plugins' );
		$cd_active_plugins = get_option( 'cd_active_plugins' );
		if ( $active_plugins != $cd_active_plugins ) {

			$dashboard_link = get_admin_url();
			$dashboard_link = add_query_arg( 'cd_update_dash', 'true', $dashboard_link );
			$dashboard_link = "<a href='$dashboard_link'>Dashboard</a>";

			$this->error_nag( "Hate to bother you, but one or more plugins has been activated / deactivated. Could you please visit the $dashboard_link to refresh the available widgets?" );

			return;
		}

		// WP API for widgets; required for use
		require_once( ABSPATH . 'wp-admin/includes/widgets.php' );

		// From wp-admin/widgets.php. Modified for CD use.
		?>

		<div class="widget-liquid-left">
			<div id="widgets-left">
				<div id="available-widgets" class="widgets-holder-wrap">
					<div class="sidebar-name">
						<div class="sidebar-name-arrow"><br/></div>
						<h3><?php _e( 'Available Widgets' ); ?> <span
								id="removing-widget"><?php _ex( 'Deactivate', 'removing-widget' ); ?>
								<span></span></span></h3>
					</div>
					<div class="widget-holder">
						<div class="sidebar-description">
							<p class="description"><?php _e( 'To activate a widget drag it to a sidebar or click on it. To deactivate a widget and delete its settings, drag it back.' ); ?></p>
						</div>
						<div id="widget-list">
							<?php wp_list_widgets(); ?>
						</div>
						<br class='clear'/>
					</div>
					<br class="clear"/>
				</div>
			</div>
		</div>

		<div class="widget-liquid-right">
			<div id="widgets-right" class="single-sidebar">
				<div class="sidebars-column-1">
				</div>
				<div class="sidebars-col 0-umn-2">
					<div class="widgets-holder-wrap">
						<?php wp_list_widget_controls( 'cd-dashboard', 'Dashboard' ); ?>
					</div>
				</div>
			</div>
		</div>
		<form action="" method="post">
			<?php wp_nonce_field( 'save-sidebar-widgets', '_wpnonce_widgets', false ); ?>
		</form>
		<br class="clear"/>

		<div class="widgets-chooser">
			<ul class="widgets-chooser-sidebars"></ul>
			<div class="widgets-chooser-actions">
				<button class="button-secondary"><?php _e( 'Cancel' ); ?></button>
				<button class="button-primary"><?php _e( 'Add Widget' ); ?></button>
			</div>
		</div>
	<?php
	}
}