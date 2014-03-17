<?php
/*
Plugin Name: Client Dash
Description: Addressing the Real Big needs for a client interface.
Version: 0.1
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

// Enqueue stylesheet
include_once('css/style.php');

// Remove all dashboard widgets
include_once('widgets/ditch-widgets.php');

// Add new dashboard widgets


// Create admin pages
require_once('admin/admin.php');

// Create settings page


?>