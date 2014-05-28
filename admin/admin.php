<?php
/*
* Include necessary files
*/

// Include files
require_once( 'globals.php' );
require_once( 'submenus.php' );
require_once( 'includes.php' );
require_once( 'functions.php' );

// Include pages
require_once( 'pages/page-account.php' );
require_once( 'pages/page-help.php' );
require_once( 'pages/page-webmaster.php' );
require_once( 'pages/page-reports.php' );
//require_once( 'pages/page-settings.php' );

// Add tab files
foreach ( $cd_existing_pages as $page => $tabs ):
	foreach ( $tabs as $tab ):
		require_once( 'tabs/' . $page . '/tab-' . $tab . '.php' );
	endforeach;
endforeach;
?>