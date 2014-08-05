<?php

/**
 * Class ClientDash_Widget_Help
 *
 * Creates a dashboard widget that is tied to a specific Client Dash
 * core page.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Widget_Help extends ClientDash {
	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	public function __construct() {
		$this->add_widget( array(
			'title'    => 'Help',
			'description' => 'The core Help widget',
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
		$dashicon = get_option( 'cd_dashicon_help', $this->option_defaults['dashicon_help'] );

		$widget = '<a href="' . $this->get_help_url() . '" class="cd-dashboard-widget cd-help">
	      <span class="dashicons ' . $dashicon . ' cd-icon cd-title-icon"></span>
	    </a>';

		echo apply_filters( 'cd_help_widget', $widget );
	}
}