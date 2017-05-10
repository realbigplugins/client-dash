<?php
/**
 * Sidebar section: Pro Prompt
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views/sidebar
 */

defined( 'ABSPATH' ) || die;
?>

<section class="clientdash-page-sidebar-section clientdash-page-sidebar-pro-prompt">
    <a href="https://realbigplugins.com/plugins/client-dash-pro/?utm_source=Client%20Dash&utm_medium=Plugin%20settings%20sidebar%20link&utm_campaign=Client%20Dash%20Plugin"
       target="_blank">
        <img src="<?php echo CLIENTDASH_URI; ?>/assets/dist/images/cd-pro-logo.png"
             alt="<?php _e( 'Client Dash Pro', 'clientdash' ); ?>"/>
    </a>

    <h3><?php _e( 'Go Pro!', 'clientdash' ); ?></h3>

    <p>
		<?php _e( 'Extend Client Dash with numerous powerful WordPress dashboard customization ' .
		          'features', 'clientdash' ) ?>
    </p>

    <p class="clientdash-page-sidebar-centered">
        <a href=https://realbigplugins.com/plugins/client-dash-pro/?utm_source=Client%20Dash&utm_medium=Plugin%20settings%20sidebar%20link&utm_campaign=Client%20Dash%20Plugin"
           class="button" target="_blank">
			<?php _e( 'Check it out!', 'clientdash' ); ?>
        </a>
    </p>
</section>
