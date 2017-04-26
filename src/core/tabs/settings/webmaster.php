<?php

/**
 * Class ClientDash_Page_Settings_Tab_Webmaster
 *
 * Adds the core content section for Settings -> Webmaster.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Webmaster
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_Webmaster extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		$this->add_content_section( array(
			'name'     => __( 'Core Settings Webmaster', 'client-dash' ),
			'page'     => __( 'Settings', 'client-dash' ),
			'tab'      => __( 'Webmaster', 'client-dash' ),
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		// Get the options
		$webmaster_name             = get_option( 'cd_webmaster_name', $this->option_defaults['webmaster_name'] );
		$webmaster_main_tab_content = get_option( 'cd_webmaster_main_tab_content' );
		$webmaster_main_tab_name    = get_option( 'cd_webmaster_main_tab_name', $this->option_defaults['webmaster_main_tab_name'] );
		$webmaster_feed             = get_option( 'cd_webmaster_feed', $this->option_defaults['webmaster_feed'] );
		$webmaster_feed_url         = get_option( 'cd_webmaster_feed_url', null );
		$webmaster_feed_count       = get_option( 'cd_webmaster_feed_count', $this->option_defaults['webmaster_feed_count'] );

		// If empty, delete
		if ( empty( $webmaster_name ) ) {
			delete_option( 'cd_webmaster_name' );
			$webmaster_name = $this->option_defaults['webmaster_name'];
		}
		?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="cd_webmaster_name"><?php _e( 'Webmaster Name', 'client-dash' ); ?></label>
				</th>
				<td>
					<input type="text" id="cd_webmaster_name" name="cd_webmaster_name" class="regular-text"
					       value="<?php echo $webmaster_name; ?>"/>
				</td>
			</tr>
		</table>

		<h3>Custom Tab Page</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="cd_webmaster_main_tab_name"><?php _e( 'Tab Name', 'client-dash' ); ?></label>
				</th>
				<td>
					<input type="text" id="cd_webmaster_main_tab_name" name="cd_webmaster_main_tab_name"
					       class="regular-text"
					       value="<?php echo $webmaster_main_tab_name; ?>"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="cd_webmaster_main_tab_content">Content</label>
				</th>
				<td>
					<div style="max-width:625px">
						<?php wp_editor( $webmaster_main_tab_content, 'cd_webmaster_main_tab_content' ); ?>
					</div>
				</td>
			</tr>
		</table>

		<h3>Custom Blog Feed</h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label>Show Blog Feed</label>
				</th>
				<td>
					<?php
					echo '<span class="cd-toggle-switch ' . ( ! empty( $webmaster_feed ) ? 'on' : 'off' ) . '">';
					echo '<input type="hidden" name="cd_webmaster_feed" value="1" ' . ( empty( $webmaster_feed ) ? 'disabled' : '' ) . '/>';
					echo '</span>';
					?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="cd_webmaster_feed_url"><?php _e( 'Feed URL', 'client-dash' ); ?></label>
				</th>
				<td>
					<input type="text" id="cd_webmaster_feed_url" name="cd_webmaster_feed_url" class="regular-text"
					       value="<?php echo $webmaster_feed_url; ?>"/>

					<p class="description"><?php _e( 'This should be a link to the RSS feed you want to pull from.',
                            'client-dash' ); ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="cd_webmaster_feed_count"><?php _e( 'Number of entries', 'client-dash' ); ?></label>
				</th>
				<td>
					<input type="text" id="cd_webmaster_feed_count" name="cd_webmaster_feed_count"
					       value="<?php echo $webmaster_feed_count; ?>"/>

					<p class="description"><?php _e( 'How many entries to show.', 'client-dash' ); ?></p>
				</td>
			</tr>
		</table>
	<?php
	}
}