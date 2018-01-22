<?php
/**
 * Sidebar section: Helper Pages Actions
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views/sidebar
 */

defined( 'ABSPATH' ) || die;
?>

<section
        class="clientdash-page-sidebar-section clientdash-page-sidebar-helper-pages-actions clientdash-page-sidebar-actions">
    <button type="submit" name="submit" id="submit" class="button button-primary button-hero widefat"
            data-cd-submit-form="clientdash-helper-pages-form" disabled>
		<?php _e( 'Save Helper Pages', 'client-dash' ); ?>
    </button>
</section>