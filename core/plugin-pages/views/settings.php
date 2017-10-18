<?php
/**
 * The settings page.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views
 */

defined( 'ABSPATH' ) || die;
?>

<div class="wrap clientdash">

    <form action="options.php" method="post" id="clientdash-settings-page-form">

		<?php settings_fields( 'clientdash_settings' ); ?>

        <h1 class="clientdash-page-title">
			<?php echo get_admin_page_title(); ?>
        </h1>

		<?php settings_errors(); ?>

        <section class="clientdash-page-wrap">

            <div class="clientdash-page-description">
				<?php
				_e( 'Modify Client Dash settings on this page.', 'client-dash' );
				?>
            </div>

			<?php
			/**
			 * Settings page content hook.
			 *
			 * @since {{VERSION}}
			 *
			 * @hooked ClientDash_PluginPages::settings_page_feed() 10
			 * @hooked ClientDash_PluginPages::settings_page_other() 20
			 */
			do_action( 'clientdash_settings_page_content' );
			?>

        </section>

    </form>

	<?php include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/sidebar.php'; ?>

</div>
