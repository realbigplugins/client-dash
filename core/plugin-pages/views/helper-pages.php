<?php
/**
 * The Helper Pages page.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views
 *
 * @param array $pages Helper pages.
 */

defined( 'ABSPATH' ) || die;
?>

<div class="wrap clientdash">

    <form method="post" action="options.php" id="clientdash-admin-page-form">

		<?php settings_fields( 'clientdash_helper_pages' ); ?>

        <h1 class="clientdash-page-title">
			<?php echo get_admin_page_title(); ?>
        </h1>

        <section class="clientdash-page-wrap">

            <div class="clientdash-page-description">
				<?php
				_e( 'Helper pages are special admin pages that Client Dash provides. You can enable or disbale them' .
				    ' for different roles.', 'client-dash' );
				?>
            </div>

			<?php foreach ( $pages as $page ) : ?>
                <div class="clientdash-helper-page-wrap">
                    <div class="clientdash-helper-page-title">
						<?php
						cd_dashicon_selector( array(
							'name'     => "$page[id]_icon",
							'selected' => $page['icon'] ? $page['icon'] : $page['original_icon'],
						) );
						?>
                        <input type="text" name="<?php echo "$page[id]_title"; ?>" id="<?php echo "$page[id]_title"; ?>"
                               class="cd-title-input widefat"
                               placeholder="<?php _e( 'Helper Page Title', 'client-dash' ); ?>"
                               value="<?php echo esc_attr( $page['title'] ? $page['title'] : $page['original_title'] ); ?>"/>
                    </div>
                </div>
			<?php endforeach; ?>

        </section>

		<?php include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/sidebar.php'; ?>

    </form>
</div>
