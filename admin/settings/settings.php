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
<h1>Hello world!</h1>
</div><!--.wrap-->
<?php
}
?>