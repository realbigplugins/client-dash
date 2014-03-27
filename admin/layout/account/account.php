<?php
// Add the submenu page
function cd_account_menu() {
	add_submenu_page('index.php', 'Account Information', 'Account', 'edit_others_posts', 'account', 'cd_account_page');
}
add_action( 'admin_menu', 'cd_account_menu' );

// Construct menu tabulation and get the content
function cd_account_page() {
	if ( !current_user_can( 'edit_others_posts' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	?>
	<div class="wrap cd cd-account">

		<h2 class="wp-menu-image">Account</h2>
		<?php
		settings_errors();
		// Get the tab query parameter. If none set, stick with first tab
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'about';
		?>

		<h2 class="nav-tab-wrapper">
			<a href="?page=account&tab=about" class="nav-tab <?php echo $active_tab == 'about' ? 'nav-tab-active' : ''; ?>">About You</a>
			<a href="?page=account&tab=sites" class="nav-tab <?php echo $active_tab == 'sites' ? 'nav-tab-active' : ''; ?>">My Sites</a>
			<?php do_action( 'cd_account_tabs' ); ?>
		</h2>
		<?php cd_get_tab($active_tab, 'account');	?>
	</div><!--.wrap-->
	<?php
}
?>