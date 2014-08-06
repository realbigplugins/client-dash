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
		echo '<div class="cd-roles-grid-page" onclick="cd_toggle_roles_page(this)">';
		echo '<p class="cd-roles-grid-title">';
		echo ucwords( str_replace( '_', ' ', $page ) );
		echo '<span class="cd-roles-grid-toggle closed"></span>';
		echo '</p>';
		echo '</div>'; // .cd-roles-grid-page

		foreach ( $tabs as $tab => $blocks ) {

			// Tab Box
			echo '<div class="cd-roles-grid-tab hidden">';
			echo '<p class="cd-roles-grid-title" onclick="cd_toggle_roles_tab(this)">';
			echo ucwords( str_replace( '_', ' ', $tab ) );
			echo '<span class="cd-roles-grid-toggle closed"></span>';
			echo '</p>';

			foreach ( $blocks as $block_ID => $props_block ) {

				// Content Box
				echo '<div class="cd-roles-grid-block hidden">';
				echo '<p class="cd-roles-grid-title">' . $props_block['name'] . '</p>';
				echo '<p class="description">Check all who should <strong>not</strong> see this content.</p>';
				echo '<p class="cd-roles-grid-list">';

				// Create checkboxes for all roles
				foreach ( $roles as $role_ID => $props_role ) {
					// If the current checkbox being generated is the current user (which
					// should always be admin), skip it
					$current_role = cd_get_user_role();
					if ( strtolower( $current_role ) == strtolower(( $props_role['name'] ) ) ) continue;

					echo '<span class="cd-roles-grid-checkbox">';
					echo '<input type="checkbox"
					             name=cd_content_blocks_roles[' . $role_ID . '][' . $block_ID . '][' . $page . ']
					             value="' . $tab . '"
					             id="' . $page . '-' . $tab . '-' . $role_ID . '" ';
					if ( ! empty( $cd_content_blocks_roles[ $role_ID ][ $block_ID ][ $page ] ) ) {
						checked( $cd_content_blocks_roles[ $role_ID ][ $block_ID ][ $page ], $tab );
					}
					echo '/>'; // Close off checkbox
					echo '<label for="' . $page . '-' . $tab . '-' . $role_ID . '">' . $props_role['name'] . '</label>';
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