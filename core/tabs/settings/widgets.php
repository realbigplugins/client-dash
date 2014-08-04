<?php

/**
 * Class ClientDash_Page_Settings_Tab_Widgets
 *
 * Adds the core content section for Settings -> Widgets.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_Widgets extends ClientDash {
	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {
		$this->add_content_section( array(
			'name' => 'Core Settings Widgets',
			'page' => 'Settings',
			'tab' => 'Widgets',
			'callback' => array( $this, 'block_output' )
		));
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {
		// Get options
		$active_widgets          = get_option( 'cd_active_widgets', null );
		?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="cd_remove_which_widgets">Widgets to not Remove</label>
				</th>
				<td>
					<?php
					if ( ! empty( $active_widgets ) ) {
						foreach ( $active_widgets as $widget => $values ) {
							echo '<input type="checkbox" name="cd_remove_which_widgets[' . $widget . ']" id="cd_remove_which_widgets' . $widget . '" value="' . $widget . '" ' . ( isset( $cd_remove_which_widgets[ $widget ] ) ? 'checked' : '' ) . '/><label for="cd_remove_which_widgets' . $widget . '">' . $values['title'] . '</label><br/>';
						}
					} else {
						cd_error( 'Please visit the <a href="/wp-admin/index.php">dashboard</a> once for "Widgets to not Remove" settings to appear.' );
					}
					?>
				</td>
			</tr>
		</table>
	<?php
	}
}