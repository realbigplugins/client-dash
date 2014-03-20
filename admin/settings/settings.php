<?php
// Add the menu link
add_action( 'admin_menu', 'cd_settings_menu' );

function cd_settings_menu() {
add_submenu_page('options-general.php', 'Client Dash Settings', 'Client Dash', 'activate_plugins', 'client-dash', 'cd_settings_page');
}
// Fill the page with juicy goodness
function cd_settings_page() {
if ( !current_user_can( 'activate_plugins' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
} ?>
<div class="wrap cd cd-settings">
<h2 class="wp-menu-image">Client Dash Settings</h2>
	<?php settings_errors();
	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
	?>
	<h2 class="nav-tab-wrapper">
		<a href="?page=client-dash&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
		<a href="?page=client-dash&tab=account" class="nav-tab <?php echo $active_tab == 'account' ? 'nav-tab-active' : ''; ?>">Account</a>
		<a href="?page=client-dash&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>">Help</a>
	</h2>
<?php
if ($active_tab == 'general') {
	include_once('general-tab.php');
} elseif ($active_tab == 'account') {
	include_once('account-tab.php');
} elseif ($active_tab == 'help') {
	include_once('help-tab.php');
} else { return "Something went wrong."; }
?>
</div><!--.wrap-->
<?php
}
?>