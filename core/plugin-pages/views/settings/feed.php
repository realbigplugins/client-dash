<?php
/**
 * Settings page section for Feed.
 *
 * @since 2.0.0
 *
 * @var string $feed_url
 * @var string $feed_count
 */

defined( 'ABSPATH' ) || die();
?>

<h2><?php _e( 'Admin Page Feed', 'client-dash' ); ?></h2>

<table class="form-table">
	<tbody>
	<tr valign="top">
		<th scope="row">
			<label for="cd_adminpage_feed_url">
				<?php _e( 'Feed URL', 'client-dash' ); ?>
			</label>
		</th>

		<td>
			<input type="text" name="cd_adminpage_feed_url" id="cd_adminpage_feed_url" class="regular-text"
			       value="<?php echo esc_attr( $feed_url ); ?>"/>

			<p class="description">
				<?php _e( 'RSS feed url that will be used on the custom Admin Page.', 'client-dash' ); ?>
			</p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			<label for="cd_adminpage_feed_count">
				<?php _e( 'Feed Count', 'client-dash' ); ?>
			</label>
		</th>

		<td>
			<input type="text" name="cd_adminpage_feed_count" id="cd_adminpage_feed_count"
			       class="regular-text"
			       value="<?php echo esc_attr( $feed_count ); ?>"/>

			<p class="description">
				<?php _e( 'Number of items to display in the feed.', 'client-dash' ); ?>
			</p>
		</td>
	</tr>
	</tbody>
</table>