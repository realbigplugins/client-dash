<?php

/**
 * Class ClientDash_Page_Webmaster_Tab_Feed
 *
 * Adds the core content block for Webmaster -> Feed.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Webmaster_Tab_Feed extends ClientDash {
	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {
		$this->add_content_block(
			'Core Webmaster Feed',
			'webmaster',
			'Feed',
			array( $this, 'block_output' )
		);
	}

	/**
	 * The content for the content block.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {
		global $cd_option_defaults;

		// Get the feed options
		$feed_url = get_option( 'cd_webmaster_feed_url', null );

		// Check if url exists
		if ( empty( $feed_url ) ) {
			$this->error_nag( 'ISSUE: Feed URL must be supplied to use this tab.', 'manage_options' );
			$this->error_nag( 'The feed tab has not yet been set up. If you believe this to be an error, please contact your system administrator' );

			return;
		}

		$feed_count = get_option( 'cd_webmaster_feed_count', $cd_option_defaults['webmaster_feed_count'] );

		// Get the feed items
		$feed = fetch_feed( $feed_url );

		// Check for an error if there's no RSS feed
		if ( is_wp_error( $feed ) ) {
			$this->error_nag( 'ISSUE: Invalid URL' );

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
}