<?php
function cd_reports_widget_content() {
	global $cd_option_defaults;

	// Get the set dashicon
	$dashicon = get_option( 'cd_dashicon_reports', $cd_option_defaults['dashicon_reports'] );

	$widget = '<a href="' . cd_get_reports_url() . '" class="cd-dashboard-widget cd-reports">
      <span class="dashicons ' . $dashicon . ' cd-icon cd-title-icon"></span>
    </a>';
	echo apply_filters( 'cd_reports_widget', $widget );
}