<?php

// Include files
require_once( plugin_dir_path( __FILE__ ) . 'globals.php' );
require_once( plugin_dir_path( __FILE__ ) . 'submenus.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes.php' );
require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );

// Include pages
require_once( plugin_dir_path( __FILE__ ) . 'pages/page-account.php' );
require_once( plugin_dir_path( __FILE__ ) . 'pages/page-help.php' );
require_once( plugin_dir_path( __FILE__ ) . 'pages/page-webmaster.php' );
require_once( plugin_dir_path( __FILE__ ) . 'pages/page-reports.php' );
require_once( plugin_dir_path( __FILE__ ) . 'pages/page-settings.php' );

// Add tab files
$cd_existing_pages = array(
	'account'   => array(
		'about',
		'sites'
	),
	'help'      => array(
		'info',
		'domain'
	),
	'reports'   => array(
		'site'
	),
	'webmaster' => array(
		'main',
		'feed'
	),
	'settings'  => array(
		'general',
		'icons',
		'webmaster',
		'roles',
		'addons'
	)
);
foreach ( $cd_existing_pages as $page => $tabs ) {
	foreach ( $tabs as $tab ) {
		require_once( plugin_dir_path( __FILE__ ) . 'tabs/' . $page . '/tab-' . $tab . '.php' );
	}
}