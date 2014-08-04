<?php

/**
 * Class ClientDash_Widget_Webmaster
 *
 * Creates a dashboard widget that is tied to a specific Client Dash
 * core page.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Widget_Webmaster extends ClientDash {
	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	public function __construct() {
		// Adds the widget to the dashboard
		add_action( 'wp_dashboard_setup', array( $this, 'add_widget' ) );
	}

	/**
	 * Adds the widget to the dashboard.
	 *
	 * @since Client Dash 1.2
	 */
	public function add_widget() {
		global $ClientDash;

		$webmaster = get_option( 'cd_webmaster_name', $ClientDash->option_defaults['webmaster_name'] );

		$disabled = get_option( 'cd_hide_page_webmaster' );

		if ( empty( $disabled ) && ! empty( $ClientDash->content_sections['webmaster'] ) ) {
			add_meta_box(
				'cd-webmaster',
				$webmaster,
				array( $this, 'widget_content' ),
				'dashboard',
				'normal',
				'core'
			);
		}
	}

	/**
	 * The content of the widget.
	 *
	 * @since Client Dash 1.2
	 */
	public function widget_content() {
		// Get the set dashicon
		$dashicon = get_option( 'cd_dashicon_webmaster', $this->option_defaults['dashicon_webmaster'] );

		$widget = '<a href="' . $this->get_webmaster_url() . '" class="cd-dashboard-widget cd-webmaster">
    <span class="dashicons ' . $dashicon . ' cd-icon cd-title-icon"></span>
  </a>';
		echo apply_filters( 'cd_webmaster_widget', $widget );
	}
}