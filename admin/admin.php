<?php
/*
* Include necessary files
*/

/* Layout necessities */
include_once('layout/account/account.php');
include_once('layout/help/help.php');
include_once('layout/settings/settings.php');

function jpl_remove_my_sites( $wp_admin_bar ) {
  
  if (current_user_can('manage_network'))
    
    $wp_admin_bar->remove_node('my-sites');
 
}
 
add_action( 'admin_bar_menu', 'jpl_remove_my_sites', 999 );
 
function jpl_my_sites($admin_bar) {
 
  if (current_user_can('manage_network'))
 
  $admin_bar->add_menu( array(
    'id'    => 'jpl-my-sites',
    'title' => 'My Sites',
    'href'  => admin_url('my-sites.php'),
    'meta'  => array(
      'title' => __('My Sites'),      
    ),
  ));
  
  $admin_bar->add_menu( array(
    'id'    => 'jpl-network-admin',
    'parent' => 'jpl-my-sites',
    'title' => 'Network Dashboard',
    'href'  => network_admin_url(),
  ));
  
  $admin_bar->add_menu( array(
    'id'    => 'jpl-network-sites',
    'parent' => 'jpl-my-sites',
    'title' => 'Network Sites',
    'href'  => network_admin_url('sites.php'),
  ));
  
  $admin_bar->add_menu( array(
    'id'    => 'jpl-network-users',
    'parent' => 'jpl-my-sites',
    'title' => 'Network Users',
    'href'  => network_admin_url('users.php'),
  ));
  
}
 
add_action('admin_bar_menu', 'jpl_my_sites', 20);
?>