<?php
/**
 * The Core CD, custom Admin Page Feed tab.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/core-pages/views/admin_page
 *
 * @var array $feed_items
 */

defined( 'ABSPATH' ) || die();
?>

<ul class="clientdash-feed-list">
	<?php foreach ( $feed_items as $item ): ?>
        <li>
            <h3 class="clientdash-feed-title">
                <a href="<?php echo $item->get_permalink(); ?>"><?php echo $item->get_title(); ?></a>
            </h3>

            <p class="clientdash-feed-content"><?php echo $item->get_description(); ?></p>

            <p class="clientdash-feed-meta">
                <span class="clientdash-feed-author">
                      <?php
                      $authors = $item->get_authors();
                      printf(
                      /* translators: %s: the name of the author */
                          __( 'Posted by %s', 'client-dash' ),
                          $authors[0]->name
                      );
                      ?>
                </span>
                <span class="clientdash-feed-date">
                    <?php
                    printf(
                    /* translators: %s: date of the blog post */
                        __( 'On %s', 'client-dash' ),
                        $item->get_date()
                    );
                    ?>
                </span>
            </p>
        </li>
	<?php endforeach; ?>
</ul>