<?php
/*
* This is where we include all variables that need to be global
*/

// Declare existing pages/tabs
$cd_existing_pages = array(
  'account' => array('about', 'sites'),
  'help' => array('faq', 'forum', 'info', 'tickets', 'tutorials'),
  'reports' => array('site', 'analytics', 'ecommerce', 'seo'),
  'webmaster' => array('news', 'promotions')
);

// Declare global variables
$cd_remove_widgets = array(
  // Wordpress widgets
  'dashboard_right_now' => array(
    'position' => 'normal',
    'title' => 'At A Glance'
  ),
  'dashboard_activity' => array(
    'position' => 'normal',
    'title' => 'Activity'
  ),
  'dashboard_quick_press' => array(
    'position' => 'side',
    'title' => 'Quick Draft'
  ),
  'dashboard_primary' => array(
    'position' => 'side',
    'title' => 'Wordpress News'
  ),

  // Other plugin widgets

  // BB Press
  'bbp-dashboard-right-now' => array(
    'position' => 'normal',
    'title' => 'Right Now in Forums'
  ),

  // WP Help
  'cws-wp-help-dashboard-widget' => array(
    'position' => 'normal',
    'title' => 'Publishing Help'
  ),

  // WooCommerce
  'woocommerce_dashboard_recent_reviews' => array(
    'position' => 'normal',
    'title' => 'WooCommerce Recent Reviews'
  ),
  'woocommerce_dashboard_status' => array(
    'position' => 'normal',
    'title' => 'WooCommerce Status'
  )
);