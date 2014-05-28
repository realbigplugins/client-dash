<?php
/*
* This is where we include all variables that need to be global
*/

// Option defaults
$cd_option_defaults = array(
	'webmaster_name'               => 'Webmaster',
	'webmaster_enable'             => false,
	'webmaster_custom_content_tab' => '',
	'webmaster_custom_content'     => ''
);

// Declare existing pages/tabs
$cd_existing_pages = array(
	'account'   => array(
		'About' => 'about',
		'Sites' => 'sites'
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
		'News'       => 'news',
		'Promotions' => 'promotions'
	),
	'settings'  => array(
		'General'   => 'general',
		'Webmaster' => 'webmaster'
	)
);

// Declare existing CD widgets
$cd_widgets = array( 'cd-account', 'cd-help', 'cd-reports' );