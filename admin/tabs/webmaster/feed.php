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
		cd_error( 'ISSUE: Feed URL must be supplied to use this tab.', 'manage_options' );
		cd_error( 'The feed tab has not yet been set up. If you believe this to be an error, please contact your system administrator' );

		return;
	}

	$feed_count = get_option( 'cd_webmaster_feed_count', $cd_option_defaults['webmaster_feed_count'] );

	// Get the feed items
	$feed = fetch_feed( $feed_url );

	// Check for an error if there's no RSS feed
	if ( is_wp_error( $feed ) ) {
		cd_error( 'ISSUE: Invalid URL' );

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

cd_content_block(
	'Core Webmaster Feed',
	'webmaster',
	'feed',
	'cd_core_webmaster_feed_tab'
);