<?php

/**
 * Class ClientDash_Page_Account_Tab_Sites
 *
 * Adds the core content section for Account -> Sites.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Account_Tab_Sites extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		if ( is_multisite() ) {
			$this->add_content_section( array(
				'name'     => 'List of Sites',
				'page'     => 'Account',
				'tab'      => 'Sites',
				'callback' => array( $this, 'block_output' )
			) );
		}
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		// Set up current user information
		$current_user = wp_get_current_user();

		// Get the blogs this current user has access to
		$blogs = get_blogs_of_user( $current_user->ID );

		?>
		<table class="widefat fixed">
			<tbody>
			<?php
			// Construct a table row for each blog owned by user
			$i = 0;
			foreach ( $blogs as $blog ) {
				$i ++;
				// Every other row is alternate for coloring
				if ( $i % 2 == 0 ) {
					echo '<tr class="alternate">';
				} else {
					echo '<tr>';
				}

				echo '<td valign="top" style="border-right: 1px solid #ccc;">';
				echo '<h3>' . $blog->blogname . '</h3>';
				echo '<p><a href="' . $blog->siteurl . '">' . __( 'Visit', 'client-dash' ) . '</a> | ';
				echo '<a href="' . $blog->siteurl . '/wp-admin/">' . __( 'Dashboard', 'client-dash' ) . '</a></p>';
				echo '</td>';

				echo '</tr>';
			}
			?>
			</tbody>
		</table>
	<?php
	}
}