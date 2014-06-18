<?php

/**
 * Outputs Info tab under Help page.
 */
function cd_core_info_tab() {
	// Get the user information
	$cd_current_theme  = wp_get_theme();
	$cd_plugins        = get_plugins();
	$cd_active_plugins = get_option( 'active_plugins' );
	global $wp_version;

	?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Current WordPress version</th>
			<td><?php echo $wp_version; ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Current theme</th>
			<td><?php echo $cd_current_theme; ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Active plugins</th>
			<td><?php foreach ( $cd_active_plugins as $key => $value ) {
					$string = explode( '/', $value ); // Display folder name
					echo $string[0];
					echo "<br/>";
				} ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Installed plugins</th>
			<td><?php foreach ( $cd_plugins as $plugin ) {
					echo $plugin['Name'];
					echo "<br/>";
				} ?></td>
		</tr>
	</table>
<?php
}

add_action( 'cd_help_info_tab', 'cd_core_info_tab' );