<?php
// Get the user information
$cd_current_theme = get_current_theme();
$cd_plugins = get_plugins();
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
		<th scope="row">Installed plugins</th>
		<td><?php foreach ($cd_plugins as $plugin) {
			echo $plugin['Name'];
			echo "<br/>";
		} ?></td>
	</tr>
</table>