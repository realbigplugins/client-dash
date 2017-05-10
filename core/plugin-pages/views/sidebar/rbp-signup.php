<?php
/**
 * Sidebar section: RBP Signup
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views/sidebar
 */

defined( 'ABSPATH' ) || die;
?>

<section class="clientdash-page-sidebar-section clientdash-page-sidebar-rbp-signup">
    <p>
		<?php
		printf(
			__( 'We make other cool plugins and share updates and special offers to anyone who ' .
			    '%ssubscribes here%s.' ),
			'<a href="http://realbigplugins.com/subscribe/?utm_source=Client%20Dash&utm_medium=Plugin' .
			'%20settings%20sidebar%20link&utm_campaign=Client%20Dash%20Plugin" target="_blank">',
			'</a>'
		);
		?>
    </p>
</section>