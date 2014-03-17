<?php
// Add the menu link
add_action( 'admin_menu', 'cd_account_menu' );
/*
*	/\Create the menu page/\
*	note the capability is set to edit_others_posts which would be editor and up
*	also note that we are making this a submenu of the Dashboard page
*/
function cd_account_menu() {
add_submenu_page('index.php', 'Account Information', 'Account', 'edit_others_posts', 'account', 'cd_account_page');
}
// Fill the page with juicy goodness
function cd_account_page() {
if ( !current_user_can( 'edit_others_posts' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
} ?>
	HTML here
<?php
}
?>