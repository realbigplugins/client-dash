<?php

/**
 * Outputs Feed tab under Webmaster page.
 */
function cd_core_webmaster_feed_tab() {
	global $cd_option_defaults;

	// Get the feed options
	$feed_url = get_option( 'cd_webmaster_feed_url', null );

	// Check if url exists
	if ( empty( $feed_url ) ) {
		echo '<div class="settings-error error"><p>ISSUE: Feed URL must be supplied to use this tab.</p></div>';

		return;
	}

	$feed_count = get_option( 'cd_webmaster_feed_count', $cd_option_defaults['webmaster_feed_count'] );

	// Get the feed items
	$feed = fetch_feed( $feed_url );

	// Check for an error if there's no RSS feed
	if ( is_wp_error( $feed ) ) {
		echo '<div class="settings-error error"><p>ISSUE: Invalid URL.</p></div>';

		return;
	}

	$feed_items = $feed->get_items( 0, $feed_count );
	?>

	<ul class="cd-feed-list">
		<?php foreach ( $feed_items as $item ): ?>
			<li>
				<h3 class="cd-feed-title">
					<a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a>
				</h3>

				<p class="cd-feed-content"><?php echo $item->get_description(); ?></p>

				<p class="cd-feed-meta">
          <span class="cd-feed-author">
            Posted by
	          <?php
	          $authors = $item->get_authors();
	          echo $authors[0]->name;
	          ?>
          </span>
          <span class="cd-feed-date">
            On <?php echo $item->get_date(); ?>
          </span>
				</p>
			</li>
		<?php endforeach; ?>
	</ul>
<?php
}

add_action( 'cd_webmaster_feed_tab', 'cd_core_webmaster_feed_tab' );