<?php
/*
* Include dashboard necessities
*/
require_once( plugin_dir_path( __FILE__ ) . 'clean-dashboard.php' );
require_once( plugin_dir_path( __FILE__ ) . 'add-widgets.php' );

// Widgets
require_once( plugin_dir_path( __FILE__ ) . 'widgets/widget-account.php' );
require_once( plugin_dir_path( __FILE__ ) . 'widgets/widget-help.php' );
require_once( plugin_dir_path( __FILE__ ) . 'widgets/widget-reports.php' );
require_once( plugin_dir_path( __FILE__ ) . 'widgets/widget-webmaster.php' );