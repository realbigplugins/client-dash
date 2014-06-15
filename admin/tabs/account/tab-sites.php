<?php

/**
 * Outputs Sites tab under Account page.
 */
function cd_core_account_sites_tab() {
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
			echo '<p><a href="' . $blog->siteurl . '">Visit</a> | ';
			echo '<a href="' . $blog->siteurl . '/wp-admin/">Dashboard</a></p>';
			echo '</td>';

			echo '</tr>';
		}
		?>
		</tbody>
	</table>
<?php
}

add_action( 'cd_account_sites_tab', 'cd_core_account_sites_tab' );