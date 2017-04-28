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
			'name'     => __( 'Core Settings Tools', 'client-dash' ),
			'page'     => __( 'Settings', 'client-dash' ),
			'tab'      => __( 'Tools', 'client-dash' ),
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		$confirm_string = sprintf(
		/* translators: %s: line breaks (leave as is) */
			__( 'WARNING: This will reset ALL settings back to default.\nThis can NOT be undone.\n\nAre you sure you ' .
			    'want to do this?', 'client-dash' ),
			"\n"
		);

        add_filter( 'cd_submit', '__return_false' );
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="cd_webmaster_enable">
						<?php _e( 'Reset all Client Dash settings', 'client-dash' ); ?>
                    </label>
                </th>
                <td>
                    <input type="button" class="button" value="Reset All Settings"
                           onclick="if ( confirm(<?php echo $confirm_string; ?>) cdAJAX.reset_all_settings();"/>
                </td>
            </tr>
        </table>
		<?php
	}
}