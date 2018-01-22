<?php
/**
 * The plugin pages' sidebar.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views/sidebar
 */

defined( 'ABSPATH' ) || die;
?>

<sidebar class="clientdash-page-sidebar">

	<?php
	/**
	 * Fires inside sidebar.
	 *
	 * @since 2.0.0
	 *
	 * @hooked ClientDash_PluginPages::sidebar_rbp_pro-prompt() 10
     * @hooked ClientDash_PluginPages::sidebar_review_support() 15
	 * @hooked ClientDash_PluginPages::sidebar_rbp_signup() 20
	 */
	do_action( 'clientdash_sidebar' );
	?>

</sidebar>