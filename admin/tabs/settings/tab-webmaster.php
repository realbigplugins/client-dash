<?php

/**
 * Outputs Settings tab under Webmaster page.
 */
function cd_core_settings_webmaster() {
	global $cd_option_defaults;

	// Get the options
	$webmaster_enable             = get_option( 'cd_webmaster_enable', $cd_option_defaults['webmaster_enable'] );
	$webmaster_name               = get_option( 'cd_webmaster_name', $cd_option_defaults['webmaster_name'] );
	$webmaster_custom_content     = get_option( 'cd_webmaster_custom_content', $cd_option_defaults['webmaster_custom_content'] );
	$webmaster_custom_content_tab = get_option( 'cd_webmaster_custom_content_tab', $cd_option_defaults['webmaster_custom_content_tab'] );
	$webmaster_feed               = get_option( 'cd_webmaster_feed', false );
	$webmaster_feed_url           = get_option( 'cd_webmaster_feed_url', null );
	$webmaster_feed_count         = get_option( 'cd_webmaster_feed_count', $cd_option_defaults['webmaster_feed_count'] );

	// If empty, delete
	if ( ! $webmaster_name ) {
		delete_option( 'cd_webmaster_name' );
	}
	?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="cd_webmaster_enable">Show Webmaster Page</label>
			</th>
			<td>
				<input type="hidden" name="cd_webmaster_enable" value="0"/>
				<input type="checkbox" id="cd_webmaster_enable" name="cd_webmaster_enable"
				       value="1" <?php checked( $webmaster_enable, '1' ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="cd_webmaster_name">Webmaster Name</label>
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
				<label for="cd_webmaster_custom_content_tab">Tab Name</label>
			</th>
			<td>
				<input type="text" id="cd_webmaster_custom_content_tab" name="cd_webmaster_custom_content_tab"
				       class="regular-text"
				       value="<?php echo $webmaster_custom_content_tab; ?>"/>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="cd_webmaster_custom_content">Content</label>
			</th>
			<td>
				<?php wp_editor( $webmaster_custom_content, 'cd_webmaster_custom_content' ); ?>
			</td>
		</tr>
	</table>

	<h3>Custom Blog Feed</h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="cd_webmaster_feed">Show Blog Feed</label>
			</th>
			<td>
				<input type="hidden" name="cd_webmaster_feed" value="0"/>
				<input type="checkbox" id="cd_webmaster_feed" name="cd_webmaster_feed"
				       value="1" <?php checked( $webmaster_feed, '1' ); ?> />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="cd_webmaster_feed_url">Feed URL</label>
			</th>
			<td>
				<input type="text" id="cd_webmaster_feed_url" name="cd_webmaster_feed_url" class="widefat"
				       value="<?php echo $webmaster_feed_url; ?>"/>

				<p class="description">This should be a link to the RSS feed you want to pull from.</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="cd_webmaster_feed_count">Number of entries</label>
			</th>
			<td>
				<input type="text" id="cd_webmaster_feed_count" name="cd_webmaster_feed_count"
				       value="<?php echo $webmaster_feed_count; ?>"/>

				<p class="description">How many entries to show.</p>
			</td>
		</tr>
	</table>
<?php
}

add_action( 'cd_settings_webmaster_tab', 'cd_core_settings_webmaster' );