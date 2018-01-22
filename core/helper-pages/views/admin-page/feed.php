<?php
/**
 * The Core CD, custom Admin Page Feed tab.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/core-pages/views/admin_page
 *
 * @var string $feed_url
 * @var string $feed_count
 */

defined( 'ABSPATH' ) || die();
?>

<div class="wrap clientdash clientdash-adminpage">
    <div class="clientdash-adminpage-content">
        <?php
        echo self::shortcode_feed( array(
	        'url'   => $feed_url,
	        'count' => $feed_count,
        ) );
        ?>
    </div>
</div>