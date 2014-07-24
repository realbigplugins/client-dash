<?php
/*
* This is where we include all variables that need to be global
*/

// Option defaults
$cd_option_defaults = array(
	'webmaster_name'          => 'Webmaster',
	'webmaster_enable'        => false,
	'webmaster_main_tab_name' => 'Main',
	'webmaster_feed_count'    => 5,
	'dashicon_account'        => 'dashicons-id-alt',
	'dashicon_reports'        => 'dashicons-chart-area',
	'dashicon_help'           => 'dashicons-editor-help',
	'dashicon_webmaster'      => 'dashicons-businessman',
	'dashicon_settings'       => 'dashicons-admin-settings'
);

// Declare existing CD widgets
$cd_widgets = array( 'cd-account', 'cd-help', 'cd-reports', 'cd-webmaster' );