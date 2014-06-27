<?php

/**
 * Outputs General tab under Settings page.
 */
function cd_core_settings_general_tab() {
	// Get options
	$active_widgets = get_option( 'cd_active_widgets', null);

	// Get options
	$cd_remove_which_widgets = get_option( 'cd_remove_which_widgets' );
	$cd_hide_page_account = get_option( 'cd_hide_page_account' );
	$cd_hide_page_reports = get_option( 'cd_hide_page_reports' );
	$cd_hide_page_help = get_option( 'cd_hide_page_help' );
	?>

	<h3>Widget Settings</h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="cd_remove_which_widgets">Widgets to not Remove</label>
			</th>
			<td>
				<?php
				if( !empty( $active_widgets ) ) {
					foreach ( $active_widgets as $widget => $values ) {
						echo '<input type="checkbox" name="cd_remove_which_widgets[' . $widget . ']" id="cd_remove_which_widgets' . $widget . '" value="' . $widget . '" ' . ( isset( $cd_remove_which_widgets[ $widget ] ) ? 'checked' : '' ) . '/><label for="cd_remove_which_widgets' . $widget . '">' . $values['title'] . '</label><br/>';
					}
				} else {
					echo '<div class="settings-error error"><p>Please visit the <a href="/wp-admin/index.php">dashboard</a> once for "Widgets to not Remove" settings to appear.</p></div>';
				}
				?>
			</td>
		</tr>
	</table>

	<h3>Page Settings</h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="cd_remove_which_widgets">Hide these default Client Dash pages</label>
			</th>
			<td>
				<input type="hidden" name="cd_hide_page_account" value="0" />
				<input type="checkbox" name="cd_hide_page_account" id="cd_hide_page_account"
				       value="1" <?php checked( '1', $cd_hide_page_account); ?> />
				<label for="cd_hide_page_account">Account</label><br/>

				<input type="hidden" name="cd_hide_page_reports" value="0" />
				<input type="checkbox" name="cd_hide_page_reports" id="cd_hide_page_reports"
				       value="1" <?php checked( '1', $cd_hide_page_reports); ?> />
				<label for="cd_hide_page_account">Reports</label><br/>

				<input type="hidden" name="cd_hide_page_help" value="0" />
				<input type="checkbox" name="cd_hide_page_help" id="cd_hide_page_help"
				       value="1" <?php checked( '1', $cd_hide_page_help); ?> />
				<label for="cd_hide_page_help">Help</label><br/>
			</td>
		</tr>
	</table>
<?php
}

add_action( 'cd_settings_general_tab', 'cd_core_settings_general_tab' );