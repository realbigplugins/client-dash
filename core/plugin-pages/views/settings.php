<?php
/**
 * The settings page.
 *
 * @since 2.0.0
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
			<?php _e( 'Settings', 'client-dash' ); ?>
        </h1>

		<?php settings_errors(); ?>

        <section class="clientdash-page-wrap">

			<?php do_settings_sections( 'clientdash_settings' ); ?>

        </section>

    </form>

	<?php include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/sidebar.php'; ?>

</div>
