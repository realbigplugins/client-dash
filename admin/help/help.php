<?php
// Add the menu link
add_action( 'admin_menu', 'cd_help_menu' );
// Add the submenu page
function cd_help_menu() {
add_submenu_page('index.php', 'Helpful Information', 'Help', 'edit_others_posts', 'help', 'cd_help_page');
//add_action('admin_init', 'cd_reg_faq_options');
}
// Simpler way to include tabs
function cd_tab($cd_tab_name){ ?>
	<table class="form-table cd-<?php echo $cd_tab_name; ?>">
		<?php include_once($cd_tab_name.'-tab.php'); ?>
	</table>
<?php }
// Fill the page with juicy goodness
function cd_help_page() {
if ( !current_user_can( 'edit_others_posts' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
} ?>
<div class="wrap cd cd-help">
	<h2 class="wp-menu-image">Help</h2>
	<?php settings_errors();
	// Define tab variable and make first the default value
	$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'faq';
	?>
	<h2 class="nav-tab-wrapper">
		<a href="?page=help&tab=faq" class="nav-tab <?php echo $active_tab == 'faq' ? 'nav-tab-active' : ''; ?>">FAQs</a>
		<a href="?page=help&tab=tutorials" class="nav-tab <?php echo $active_tab == 'tutorials' ? 'nav-tab-active' : ''; ?>">Tutorials</a>
		<a href="?page=help&tab=tickets" class="nav-tab <?php echo $active_tab == 'tickets' ? 'nav-tab-active' : ''; ?>">Tickets</a>
		<a href="?page=help&tab=forum" class="nav-tab <?php echo $active_tab == 'forum' ? 'nav-tab-active' : ''; ?>">Forum</a>
		<a href="?page=help&tab=info" class="nav-tab <?php echo $active_tab == 'info' ? 'nav-tab-active' : ''; ?>">Site Info</a>
	</h2>
<?php
if ($active_tab == 'faq') {
	cd_tab('faq');
} elseif ($active_tab == 'tutorials') {
	cd_tab('tutorials');
} elseif ($active_tab == 'tickets') {
	cd_tab('tickets');
} elseif ($active_tab == 'forum') {
	cd_tab('forum');
} elseif ($active_tab == 'info') {
	cd_tab('info');
} else {}
?>
</div><!--.wrap-->
<?php
}
?>