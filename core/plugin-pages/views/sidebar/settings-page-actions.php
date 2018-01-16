<?php
/**
 * Sidebar section: Settings Page Actions
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views/sidebar
 */

defined( 'ABSPATH' ) || die;
?>

<section
        class="clientdash-page-sidebar-section clientdash-page-sidebar-settings-pages-actions clientdash-page-sidebar-actions">
    <button type="submit" name="submit" id="submit" class="button button-primary button-hero widefat"
            data-cd-submit-form="clientdash-settings-page-form" disabled>
		<?php _e( 'Save Settings', 'client-dash' ); ?>
    </button>
</section>