<?php
// Add the submenu page under Settings
// This is where webmaster only settings for the plugin go
function cd_settings_menu() {
	add_submenu_page('options-general.php', 'Client Dash Settings', 'Client Dash', 'activate_plugins', 'client-dash', 'cd_settings_page');
}
add_action( 'admin_menu', 'cd_settings_menu' );

// Construct menu tabulation and get the content
function cd_settings_page() {
	if ( !current_user_can( 'edit_others_posts' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="wrap cd cd-settings">

		<h2 class="wp-menu-image">Settings</h2>
		<?php settings_errors();
		// Get the tab query parameter. If none set, stick with first tab
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'account';
		?>

		<h2 class="nav-tab-wrapper">
			<a href="?page=client-dash&tab=account" class="nav-tab <?php echo $active_tab == 'account' ? 'nav-tab-active' : ''; ?>">Account</a>
			<a href="?page=client-dash&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
			<a href="?page=client-dash&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>">Help</a>
			<?php do_action( 'cd_settings_tabs' ); ?>
		</h2>
		<?php cd_get_tab($active_tab, 'settings');	?>
	</div><!--.wrap-->
	<?php
}
?>