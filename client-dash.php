<?php
/*
Plugin Name: Client Dash
Description: Creating a more intuitive admin interface for clients.
Version: 1.4
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

// Dashboard widgets
require_once( plugin_dir_path( __FILE__ ) . 'dashboard/dashboard.php' );

// Create admin pages
require_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );

// Enhance the toolbar
require_once( plugin_dir_path( __FILE__ ) . 'toolbar/toolbar.php' );

// Store admin color scheme for later
global $admin_colors;

/**
 * Saves the color scheme for later.
 *
 * Wordpress normally purges this value pretty quickly, so we're saving it for ourselves.
 */
function cd_admin_colors() {
	global $_wp_admin_css_colors, $admin_colors;
	$admin_colors = $_wp_admin_css_colors;
}

add_action( 'admin_init', 'cd_admin_colors' );

/**
 * Register files.
 */
function cd_register_scripts() {
	wp_register_script(
		'cd-scripts',
		plugin_dir_url( __FILE__ ) . 'js/client-dash.js',
		array( 'jquery', 'jquery-ui-sortable' )
	);
}

add_action( 'admin_init', 'cd_register_scripts' );

/**
 * Enqueue files.
 */
function cd_enqueue_scripts() {
	wp_enqueue_script( 'cd-scripts' );
}

add_action( 'admin_enqueue_scripts', 'cd_enqueue_scripts' );