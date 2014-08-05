<?php

/**
 * Class ClientDash_Widget_Reports
 *
 * Creates a dashboard widget that is tied to a specific Client Dash
 * core page.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Widget_Reports extends ClientDash {
	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	public function __construct() {
		$this->add_widget( array(
			'title'    => 'Reports',
			'description' => 'The core Reports widget',
			'callback' => array( $this, 'widget_content' )
		) );
	}

	/**
	 * The content of the widget.
	 *
	 * @since Client Dash 1.2
	 */
	public function widget_content() {
		// Get the set dashicon
		$dashicon = get_option( 'cd_dashicon_reports', $this->option_defaults['dashicon_reports'] );

		$widget = '<a href="' . $this->get_reports_url() . '" class="cd-dashboard-widget cd-reports">
      <span class="dashicons ' . $dashicon . ' cd-icon cd-title-icon"></span>
    </a>';
		echo apply_filters( 'cd_reports_widget', $widget );
	}
}