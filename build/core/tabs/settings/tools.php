<?php

/**
 * Class ClientDash_Page_Settings_Tab_Tools
 *
 * Adds the core content section for Settings -> Tools.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_Tools extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		$this->add_content_section( array(
			'name'     => 'Core Settings Tools',
			'page'     => 'Settings',
			'tab'      => 'Tools',
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		add_filter( 'cd_submit', '__return_false' );
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="cd_webmaster_enable">Reset all Client Dash settings</label>
				</th>
				<td>
					<input type="button" class="button" value="Reset All Settings"
					       onclick="if ( confirm('WARNING: This will reset ALL settings back to default.\nThis can NOT be undone.\n\nAre you sure you want to do this?') ) cdAJAX.reset_all_settings();"/>
				</td>
			</tr>
		</table>
	<?php
	}
}