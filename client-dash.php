<?php
/*
Plugin Name: Client Dash
Description: Addressing the Real Big needs for a client interface.
Version: 0.2
Author: Kyle Maurer
Author URI: http://realbigmarketing.com/staff/kyle
*/

// Enqueue stylesheet
require_once('css/style.php');

// Dashboard widgets
require_once('widgets/widgets.php');

// Create admin pages
require_once('admin/admin.php');

// Include core functionality
require_once('admin/functions.php');

// Enhance the toolbar
require_once('toolbar/toolbar.php');
?>