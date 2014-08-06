<?php
function cd_help_widget_content() {
	global $cd_option_defaults;

	// Get the set dashicon
	$dashicon = get_option( 'cd_dashicon_help', $cd_option_defaults['dashicon_help'] );

	$widget = '<a href="' . cd_get_help_url() . '" class="cd-dashboard-widget cd-help">
    <span class="dashicons ' . $dashicon . ' cd-icon cd-title-icon"></span>
  </a>';
	echo apply_filters( 'cd_help_widget', $widget );
}