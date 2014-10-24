<?php

/*
Plugin Name: Client Dash DEVELOPMENT
Description: The development build for Client Dash.
Version: 1.6.6-alpha
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

//define( 'SCRIPT_DEBUG', true );

define( 'CD_DEVELOPMENT', true );

include_once( 'tools/development-database.php' );
register_activation_hook( __FILE__, array( 'ClientDash_Develop', 'create_table' ) );

include_once( 'src/client-dash.php' );