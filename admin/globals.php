<?php
/*
* This is where we include all variables that need to be global
*/

// Option defaults
$cd_option_defaults = array(
	'webmaster_name'               => 'Webmaster',
	'webmaster_enable'             => false,
	'webmaster_custom_content_tab' => 'Main',
	'webmaster_custom_content'     => '',
	'webmaster_feed_count'         => 5,
	'dashicon_account'             => 'dashicons-id-alt',
	'dashicon_reports'             => 'dashicons-chart-area',
	'dashicon_help'                => 'dashicons-editor-help',
	'dashicon_webmaster'           => 'dashicons-businessman'
);

// Declare existing pages/tabs
$cd_existing_pages = array(
	'account'   => array(
		'About' => 'about',
		//    'Sites' => 'sites'
	),
	'help'      => array(
		//      'FAQ' => 'faq',
		//      'Forum' => 'forum',
		'Info' => 'info',
		//      'Tickets' => 'tickets',
		//      'Tutorials' => 'tutorials'
	),
	'reports'   => array(
		'Site' => 'site',
		//      'Analytics' =>'analytics',
		//      'Ecommerce' => 'ecommerce',
		//      'SEO' => 'seo'
	),
	'webmaster' => array(
		'Main' => 'main',
		'Feed' => 'feed'
	),
	'settings'  => array(
		'General'   => 'general',
		'Icons'     => 'icons',
		'Webmaster' => 'webmaster',
		'Addons'    => 'addons'
	)
);

// If multisite, add sites
if ( is_multisite() ) {
	$cd_existing_pages['account']['Sites'] = 'sites';
}

// Declare existing CD widgets
$cd_widgets = array( 'cd-account', 'cd-help', 'cd-reports', 'cd-webmaster' );