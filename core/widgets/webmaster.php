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

		$webmaster = get_option( 'cd_webmaster_name', $this->option_defaults['webmaster_name'] );

		$this->add_widget( array(
			'ID'            => 'webmaster',
			'title'         => $webmaster,
			'description'   => 'The core Webmaster widget',
			'callback'      => array( 'ClientDash_Widget_Webmaster', 'widget_content' ),
			'edit_callback' => false,
			'cd_page'       => 'webmaster'
		) );
	}

	/**
	 * The content of the widget.
	 *
	 * @since Client Dash 1.2
	 */
	public static function widget_content() {

		global $ClientDash;

		// Get the set dashicon
		$dashicon = get_option( 'cd_dashicon_webmaster', $ClientDash->option_defaults['dashicon_webmaster'] );

		$widget = '<a href="' . $ClientDash->get_webmaster_url() . '" class="cd-dashboard-widget cd-webmaster">
    <span class="dashicons ' . $dashicon . ' cd-icon cd-title-icon"></span>
  </a>';
		echo apply_filters( 'cd_webmaster_widget', $widget );
	}
}