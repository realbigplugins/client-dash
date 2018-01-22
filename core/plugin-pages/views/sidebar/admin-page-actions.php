<?php
/**
 * Sidebar section: Admin Page Actions
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views/sidebar
 */

defined( 'ABSPATH' ) || die;
?>

<section
        class="clientdash-page-sidebar-section clientdash-page-sidebar-admin-pages-actions clientdash-page-sidebar-actions">
    <button type="submit" name="submit" id="submit" class="button button-primary button-hero widefat"
            data-cd-submit-form="clientdash-admin-page-form" disabled>
		<?php _e( 'Save Admin Page', 'client-dash' ); ?>
    </button>
</section>