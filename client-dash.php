<?php
/*
Plugin Name: Client Dash
Description: Creating a more intuitive admin interface for clients.
Version: 1.2.1
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

// Dashboard widgets
require_once(plugin_dir_path(__FILE__) . 'dashboard/dashboard.php');

// Create admin pages
require_once(plugin_dir_path(__FILE__) . 'admin/admin.php');

// Enhance the toolbar
require_once(plugin_dir_path(__FILE__) . 'toolbar/toolbar.php');

// Store admin color scheme for later
global $admin_colors;

function cd_admin_colors() {
  global $_wp_admin_css_colors, $admin_colors;
  $admin_colors = $_wp_admin_css_colors;
}

add_action('admin_init', 'cd_admin_colors');

function cd_scripts() {
  wp_enqueue_script('cd-scripts', plugin_dir_url(__FILE__) . 'js/client-dash.js');
}

add_action('admin_enqueue_scripts', 'cd_scripts');