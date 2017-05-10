<?php
/**
 * The plugin pages' sidebar.
 *
 * @since {{VERSION}}
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
	 * @since {{VERSION}}
	 *
	 * @hooked ClientDash_Page_Settings::sidebar_rbp_pro-prompt() 10
	 * @hooked ClientDash_Page_Settings::sidebar_rbp_signup() 15
	 */
	do_action( 'clientdash_sidebar' );
	?>

</sidebar>