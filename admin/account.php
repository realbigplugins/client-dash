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
// Tutorial here http://code.tutsplus.com/tutorials/the-complete-guide-to-the-wordpress-settings-api-part-5-tabbed-navigation-for-your-settings-page--wp-24971
function cd_account_page() {
if ( !current_user_can( 'edit_others_posts' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
} ?>
<div class="wrap cd cd-account">
	<h2 class="wp-menu-image">Account Information</h2>
	<?php settings_errors();
	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'about';
	?>
	<h2 class="nav-tab-wrapper">
		<a href="?page=account&tab=about" class="nav-tab <?php echo $active_tab == 'about' ? 'nav-tab-active' : ''; ?>">About You</a>
		<a href="?page=account&tab=billing" class="nav-tab <?php echo $active_tab == 'billing' ? 'nav-tab-active' : ''; ?>">Billing</a>
	</h2>
<?php
if ($active_tab == 'about') {
	include_once('about-tab.php');
} else {
	echo 'other tab';
}
?>
</div><!--.wrap-->
<?php
}
?>