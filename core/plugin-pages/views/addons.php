<?php
/**
 * The addons page.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views
 *
 * @var array $addons
 */

defined( 'ABSPATH' ) || die;
?>

<div class="wrap clientdash">

    <h1 class="clientdash-page-title">
		<?php echo get_admin_page_title(); ?>
    </h1>

    <section class="clientdash-page-wrap">

        <ul class="clientdash-addons-list">
			<?php foreach ( $addons as $addon ) : ?>
                <li class="clientdash-addons-list-item">
                    <div class="clientdash-addons-list-item-container">
                        <img src="<?php echo esc_attr( $addon->info->thumbnail ); ?>" class="clientdash-addon-image"/>

                        <div class="clientdash-addon-content">
                            <h3 class="clientdash-addon-title">
								<?php echo esc_html( $addon->info->title ); ?>
                            </h3>

                            <p class="clietndash-addon-description">
								<?php echo $addon->info->excerpt ?
									esc_html( $addon->info->excerpt ) : esc_html( $addon->info->content ); ?>
                            </p>

                            <a href="<?php echo esc_url_raw( $addon->info->link ); ?>"
                               class="clientdash-addon-link button">
								<?php _e( 'Get Add On', 'client-dash' ); ?>
                            </a>
                        </div>
                    </div>
                </li>
			<?php endforeach; ?>
        </ul>

        <a href="<?php echo admin_url( 'admin.php?page=clientdash_addons&cd_flush_addons' ); ?>"
           class="page-title-action">
			<?php _e( 'Refresh Addons', 'client-dash' ); ?>
        </a>

    </section>

	<?php include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/sidebar.php'; ?>

</div>
