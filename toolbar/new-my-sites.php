<?php
function cd_my_sites($admin_bar) {
 
  if (current_user_can('manage_network'))
 
  $admin_bar->add_menu( array(
    'id'    => 'cd-my-sites',
    'title' => 'My Sites',
    'href'  => admin_url('my-sites.php'),
    'meta'  => array(
      'title' => __('My Sites'),      
    ),
  ));
  
  $admin_bar->add_menu( array(
    'id'    => 'cd-network-admin',
    'parent' => 'cd-my-sites',
    'title' => 'Network Dashboard',
    'href'  => network_admin_url(),
  ));
  
  $admin_bar->add_menu( array(
    'id'    => 'cd-network-sites',
    'parent' => 'cd-my-sites',
    'title' => 'Network Sites',
    'href'  => network_admin_url('sites.php'),
  ));
  
  $admin_bar->add_menu( array(
    'id'    => 'cd-network-users',
    'parent' => 'cd-my-sites',
    'title' => 'Network Users',
    'href'  => network_admin_url('users.php'),
  ));
  
}
 
add_action('admin_bar_menu', 'cd_my_sites', 20);
?>