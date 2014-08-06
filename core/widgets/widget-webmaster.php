<?php
function cd_webmaster_widget_content() {
	global $cd_option_defaults;

	// Get the set dashicon
	$dashicon = get_option( 'cd_dashicon_webmaster', $cd_option_defaults['dashicon_webmaster'] );

	$widget = '<a href="' . cd_get_webmaster_url() . '" class="cd-dashboard-widget cd-webmaster">
    <span class="dashicons ' . $dashicon . ' cd-icon cd-title-icon"></span>
  </a>';
	echo apply_filters( 'cd_webmaster_widget', $widget );
}