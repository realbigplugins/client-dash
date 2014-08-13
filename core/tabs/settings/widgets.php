<?php

/**
 * Class ClientDash_Page_Settings_Tab_Widgets
 *
 * Adds the core content section for Settings -> Widgets.
 *
 * @package WordPress
 * @subpackage Client Dash
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

		$this->add_content_section( array(
			'name'     => 'Core Settings Widgets',
			'page'     => 'Settings',
			'tab'      => 'Widgets',
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		global $ClientDash;

		// Check to see if any plugin modifications have been made, and notify the user
		$active_plugins    = get_option( 'active_plugins' );
		$cd_active_plugins = get_option( 'cd_active_plugins' );

		if ( $active_plugins != $cd_active_plugins ) {
			$dashboard_link = get_admin_url();
			$dashboard_link = add_query_arg( 'cd_update_dash', 'true', $dashboard_link );
			$dashboard_link = "<a href='$dashboard_link'>Dashboard</a>";
			$this->error_nag( "Hate to bother you, but one or more plugins has been activated / deactivated. Could you please visit the $dashboard_link to refresh them?" );
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
		?>
		<div id="cd-dash-widgets-left">
			<h3>Available Dashboard Widgets</h3>

			<p class="description">
				To activate a dashboard widget, simply drag it into the dashboard area. To delete, click "delete".
			</p>

			<ul class="cd-dash-widgets-container">
				<?php
				if ( ! empty( $all_widgets ) ) {
					$this->widget_loop( $all_widgets, true, true );
				}
				?>
			</ul>
		</div>

		<div id="cd-dash-widgets-right">
			<h3>Dashboard</h3>

			<ul id="cd-dash-widgets-sortable" class="cd-dash-widgets-container">
				<?php
				if ( ! empty( $cd_widgets ) ) {
					$this->widget_loop( $cd_widgets );
				}
				?>
			</ul>
		</div>

		<div class="clear"></div>
	<?php
	}
}