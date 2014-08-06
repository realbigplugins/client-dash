<?php
function cd_account_widget_content() {
	global $cd_option_defaults;

	// Get the set dashicon
	$dashicon = get_option( 'cd_dashicon_account', $cd_option_defaults['dashicon_account'] );

	$widget = '<a href="' . cd_get_account_url() . '" class="cd-dashboard-widget cd-account">
      <span class="dashicons ' . $dashicon . ' cd-icon cd-title-icon"></span>
    </a>';
	echo apply_filters( 'cd_account_widget', $widget );
}