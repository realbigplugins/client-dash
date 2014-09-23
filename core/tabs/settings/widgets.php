<?php

// FUTUREBUILD Mimic the WordPress HTML exactly and configure CD to work with the new HTMl
// FUTUREBUILD Use "do_accordion_sections()" with modified "$wp_meta_boxes" in order to produce dropdowns

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
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		// Anything in here will ONLY apply to this particular settings page
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'cd_settings'
		     && isset( $_GET['tab'] ) && $_GET['tab'] == 'widgets'
		) {

			// Necessary scripts
			add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

			// Add nav-menus body class for our custom CD page
			add_filter( 'admin_body_class', array( $this, 'add_nav_menu_class' ) );
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

	public function populate_available_widgets() {

		global $wp_registered_widgets, $wp_registered_widget_controls;

		$wp_registered_widgets = array(
			'cd_core_account-1' => array(
				'name'        => 'CD Account',
				'id'          => 'cd_account-1',
				'callback'    => array(),
				'params'      => array(),
				'classname'   => 'cd-core-account',
				'description' => 'The Client Dash core Account page.',
			),
		);

		$wp_registered_widget_controls = array(
			'cd_core_account-1' => array(
				'name'     => 'CD Account',
				'id'       => 'cd_account-1',
				'callback' => array(),
				'params'   => array(),
				'width'    => 250,
				'height'   => 200,
				'id_base'  => 'cd-core-account',
			),
		);
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		global $ClientDash;

		// Get the available widgets
		$this->populate_available_widgets();

		// Check to see if any plugin modifications have been made, and notify the user
		$active_plugins    = get_option( 'active_plugins' );
		$cd_active_plugins = get_option( 'cd_active_plugins' );
		if ( $active_plugins != $cd_active_plugins ) {

			$dashboard_link = get_admin_url();
			$dashboard_link = add_query_arg( 'cd_update_dash', 'true', $dashboard_link );
			$dashboard_link = "<a href='$dashboard_link'>Dashboard</a>";

			$this->error_nag( "Hate to bother you, but one or more plugins has been activated / deactivated. Could you please visit the $dashboard_link to refresh them?" );

			// Hide the submit button
			add_filter( 'cd_submit', '__return_false' );

			return;
		}

		// Widgets added by plugins and WP Core
		$active_widgets = get_option( 'cd_active_widgets' );

		// Widgets added by Client Dash
		$cd_available_widgets = $ClientDash->widgets;

		// Get our active widgets
		$cd_widgets = get_option( 'cd_widgets', $this->option_defaults['widgets'] );

		// All widgets to use
		if ( ! empty( $active_widgets ) ) {
			$all_widgets = array_merge( $cd_available_widgets, $active_widgets );
		} else {
			$all_widgets = $cd_available_widgets;
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
						<?php // TODO Not getting 'ui-sortable' class, so sortable not working ?>
						<?php wp_list_widget_controls( 'testing', 'Testing' ); // Show the control forms for each of the widgets in this sidebar ?>
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