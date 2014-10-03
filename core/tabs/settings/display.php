<?php

/**
 * Class ClientDash_Page_Settings_Tab_Display
 *
 * Adds the core content section for Settings -> Display.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_Display extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		$this->add_content_section( array(
			'name'     => 'Core Settings Display',
			'page'     => 'Settings',
			'tab'      => 'Display',
			'callback' => array( $this, 'block_output' )
		) );
	}

	public function add_reset_button( $submit ) {

		$reset = '<input type="button" class="button cd-reset-display" value="Reset Roles" onclick="if ( confirm(\'WARNING: This will reset all role settings back to default. \\n\\nAre you sure you want to do this?\') ) cdAJAX.reset_roles();" />';

		return $submit . $reset;
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		global $ClientDash;

		// Tells us that this page has been updated
		?>
		<input type="hidden" name="cd_display_settings_updated" value="1" />
		<?php

		// Add our reset roles button next to submit
		add_filter( 'cd_submit', array( $this, 'add_reset_button' ) );
		?>
		<p>Use this page to disable specific content for specific roles. Simply un-check the role inside of any content
			block you want to disable for that role.</p>
		<?php
		// If no content sections, bail
		if ( empty( $ClientDash->content_sections ) ) {
			$this->error_nag( 'There seems to be no content... that\'s really weird... I\'d contact Joel or Kyle' );

			return;
		}

		// Get options
		$option_defaults = $ClientDash->option_defaults['content_sections_roles'];
		$content_sections_roles = get_option( 'cd_content_sections_roles', $option_defaults );

		// Get roles
		$roles = get_editable_roles();

		// Main container
		echo '<ul id="cd-roles-grid">';

		// Cycle through all content sections and output accordingly
		foreach ( $ClientDash->content_sections_unmodified as $page => $tabs ) {

			// Find out if all roles have been unchecked
			$all_disabled = true;
			if ( isset( $content_sections_roles[ $page ] ) ) {
				foreach ( $content_sections_roles[ $page ] as $sections ) {
					foreach ( $sections as $section_roles ) {
						foreach ( $section_roles as $section_role ) {
							if ( $section_role == 0 ) {
								// If even one of these is enabled, we can assume that all are not disabled
								$all_disabled = false;
							}
						}
					}
				}
			} else {
				// ... or if the option is NOT set, we can assume defaults wouldn't do this... right?
				$all_disabled = false;
			}

			// Skip page "Settings"
			if ( $page == 'settings' ) {
				continue;
			}

			// Item
			echo '<li class="cd-display-grid-item">';

			// Page box
			echo '<div class="cd-display-grid-page" onclick="cdMain.toggle_roles_page(this)">';

			echo '<p class="cd-display-grid-title">';

			echo ucwords( str_replace( '_', ' ', $page ) );

			// All disabled tip
			echo '<span style="position:relative;">';
			if ( $all_disabled ) {
				echo $this->pointer( 'All roles are disabled so this page won\'t show for anybody.', 'left', 'cd-tip-all-disabled' );
			}
			echo '</span>';

			echo '<span class="cd-up-down"></span>';
			echo '</p>';
			echo '</div>'; // .cd-display-grid-page

			foreach ( $tabs as $tab => $props ) {

				// Tab Box
				echo '<div class="cd-display-grid-tab hidden">';
				echo '<p class="cd-display-grid-title">';
				echo $props['name'];
				echo '</p>';

				foreach ( $props['content-sections'] as $block_ID => $props_block ) {

					// Content Box
					echo '<div class="cd-display-grid-block">';
					echo '<p class="cd-display-grid-title">' . $props_block['name'] . '</p>';
					echo '<p class="description">All checked sections will be visible to the given role.</p>';
					echo '<p class="cd-roles-grid-list">';

					// Create checkboxes for all roles
					foreach ( $roles as $role_ID => $props_role ) {

						// If the current checkbox being generated is the current user (which
						// should always be admin), skip it
						$current_role = $this->get_user_role();
						if ( strtolower( $current_role ) == strtolower( ( $props_role['name'] ) ) ) {
							continue;
						}

						// Get these values so we can save space later
						if ( isset( $content_sections_roles[ $page ][ $tab ][ $block_ID ][ $role_ID ] ) ) {
							$option_value  = $content_sections_roles[ $page ][ $tab ][ $block_ID ][ $role_ID ];
						} elseif ( isset( $option_defaults[ $page ][ $tab ][ $block_ID ][ $role_ID ] ) ) {
							$option_value = $option_defaults[ $page ][ $tab ][ $block_ID ][ $role_ID ];
						} else {
							$option_value = 'hidden';
						}

						echo '<span class="cd-display-grid-checkbox">';
						echo "<input type='hidden'
						             name='cd_content_sections_roles[$page][$tab][$block_ID][$role_ID]'
						             value='hidden'>";
						echo "<input type='checkbox'
					             name='cd_content_sections_roles[$page][$tab][$block_ID][$role_ID]'
					             value='visible'
					             id='$page-$tab-$role_ID' ";

						// Check the checkbox if set to visible
						echo $option_value == 'visible' ? 'checked' : '';
						echo '/>'; // Close off checkbox
						echo '<label for="' . $page . '-' . $tab . '-' . $role_ID . '">' . $props_role['name'] . '</label>';
						echo '</span>';
					}
					echo '</p>'; // .cd-roles-grid-list

					echo '</div>'; // .cd-display-grid-block
				}
				echo '</div>'; // .cd-display-grid-tab
			}
			echo '</li>'; // .cd-display-grid-item
		}

		echo '</ul>'; // #cd-roles-grid
	}
}