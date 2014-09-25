<?php

/**
 * Class ClientDash_Widget_Help
 *
 * Creates a dashboard widget that is tied to a specific Client Dash
 * core page.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Widgets
 *
 * @since Client Dash 1.5
 */
class ClientDash_Widget_Help extends ClientDash {

	/**
	 * The content of the widget.
	 *
	 * @since Client Dash 1.2
	 */
	public static function widget_content() {

		global $ClientDash;

		// Get the set dashicon
		$dashicon = get_option( 'cd_dashicon_help', $ClientDash->option_defaults['dashicon_help'] );

		$widget = '<a href="' . $ClientDash->get_help_url() . '" class="cd-dashboard-widget cd-help">
	      <span class="dashicons ' . $dashicon . ' cd-icon cd-title-icon"></span>
	    </a>';

		echo apply_filters( 'cd_help_widget', $widget );
	}
}