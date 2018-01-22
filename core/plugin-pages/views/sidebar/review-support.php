<?php
/**
 * Sidebar section: Review Support
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views/sidebar
 */

defined( 'ABSPATH' ) || die;
?>

<section class="clientdash-page-sidebar-section clientdash-page-sidebar-review-support">
    <p>
		<?php
		printf(
			__( 'Like this plugin? Consider leaving us a rating.', 'client-dash' ),
			'<a href="https://wordpress.org/support/view/plugin-reviews/client-dash?rate=5#postform">',
			'</a>'
		);
		?>
    </p>

    <p class="clientdash-page-sidebar-ratings">
		<?php for ( $i = 5; $i >= 1; $i -- ) : ?><a
            href="https://wordpress.org/support/plugin/client-dash/reviews/?rate=<?php echo $i; ?>#new-post"
            class="dashicons dashicons-star-empty" target="_blank"
			<?php echo $i < 4 ? $rating_confirm : ''; ?> >
            </a><?php endfor; ?>
    </p>

    <p>
		<?php
		printf(
			__( 'Need help? Visit our %ssupport forums%s.', 'client-dash' ),
			'<a href="https://wordpress.org/support/plugin/client-dash" target="_blank">',
			'</a>'
		);
		?>
    </p>
</section>