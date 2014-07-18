<?php

/**
 * Outputs General tab under Settings page.
 */
function cd_core_settings_roles_tab() {
	?>
	<p>Use this page to disable specific content for specific roles. Do so by checking roles on content you do not want
		them to see.</p>
	<?php
	global $cd_content_blocks;

	// If no content blocks, bail
	if ( empty( $cd_content_blocks ) ) {
		return;
	}

	// Get options
	$cd_content_blocks_roles = get_option( 'cd_content_blocks_roles' );

	// Get roles
	$roles = get_editable_roles();

	// Main container
	echo '<ul id="cd-roles-grid">';

	// Cycle through all content blocks and output accordingly
	foreach ( $cd_content_blocks as $page => $tabs ) {

		// Skip page "Settings"
		if ( $page == 'settings' ) {
			continue;
		}

		// Item
		echo '<li class="cd-roles-grid-item">';

		// Page box
		echo '<div class="cd-roles-grid-page">';
		echo '<p class="cd-roles-grid-title">';
		echo ucwords( $page );
		echo '<span class="cd-roles-grid-toggle closed" onclick="cd_updown_target(this, \'.cd-roles-grid-tab\', \'.cd-roles-grid-item\')"></span>';
		echo '</p>';
		echo '</div>'; // .cd-roles-grid-page

		foreach ( $tabs as $tab => $blocks ) {

			// Tab Box
			echo '<div class="cd-roles-grid-tab hidden">';
			echo '<p class="cd-roles-grid-title">';
			echo ucwords( $tab );
			echo '<span class="cd-roles-grid-toggle closed" onclick="cd_updown_target(this, \'.cd-roles-grid-block\', \'.cd-roles-grid-tab\')"></span>';
			echo '</p>';

			foreach ( $blocks as $block_ID => $props ) {

				// Content Box
				echo '<div class="cd-roles-grid-block hidden">';
				echo '<p class="cd-roles-grid-title">' . $props['name'] . '</p>';
				echo '<p class="description">Check all who should <strong>not</strong> see this content.</p>';
				echo '<p class="cd-roles-grid-list">';

				// Create checkboxes for all roles
				foreach ( $roles as $role_ID => $props ) {
					echo '<span class="cd-roles-grid-checkbox">';
					echo '<input type="checkbox"
					             name=cd_content_blocks_roles[' . $role_ID . '][' . $block_ID . '][' . $page . ']
					             value="' . $tab . '" ';
					if ( ! empty( $cd_content_blocks_roles ) ) {
						checked( $cd_content_blocks_roles[ $role_ID ][ $block_ID ][ $page ], $tab );
					}
					echo '/>'; // Close off checkbox
					echo '<label>' . $props['name'] . '</label>';
					echo '</span>';
				}
				echo '</p>'; // .cd-roles-grid-list

				echo '</div>'; // .cd-roles-grid-block
			}
			echo '</div>'; // .cd-roles-grid-tab
		}
		echo '</li>'; // .cd-roles-grid-item
	}

	echo '</ul>'; // #cd-roles-grid
}

cd_content_block( 'Core Settings Roles', 'settings', 'roles', 'cd_core_settings_roles_tab' );