<?php

/**
 * Class ClientDash_AJAX
 *
 * Adds all AJAX functionality to Client Dash
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since Client Dash 1.5
 */
class ClientDash_AJAX {

	/**
	 * Constructs the class.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		// Cycle through each method and add its ajax action
		foreach ( get_class_methods( 'ClientDash_AJAX' ) as $method ) {

			// Skip construct method
			if ( $method == '__construct' ) {
				continue;
			}

			add_action( "wp_ajax_cd_$method", array( $this, $method ) );
		}
	}

	/**
	 * Resets all of the roles settings to default.
	 *
	 * @since Client Dash 1.5
	 */
	public function reset_roles() {

		global $ClientDash;

		foreach ( $ClientDash->core_widgets as $page ) {
			update_option( "cd_hide_page_$page", $ClientDash->option_defaults["hide_page_$page"] );
		}
		update_option( 'cd_content_sections_roles', $ClientDash->option_defaults['content_sections_roles'] );
		echo 'Roles successfully reset!';

		die();
	}

	/**
	 * Resets ALL Client Dash settings by deleting them.
	 *
	 * @since Client Dash 1.6
	 */
	public function reset_all_settings() {

		global $ClientDash;

		// Cycle through all option defaults and delete them
		foreach ( $ClientDash->option_defaults as $name => $value ) {
			delete_option( "cd_$name" );
		}

		// Remove the modified nav menu
		wp_delete_nav_menu( 'cd_admin_menu' );

		echo 'Settings successfully reset!';

		die();
	}

	/**
	 * Simply launches the save_cd_menu() function with the proper menu ID.
	 *
	 * @since Client Dash 1.6
	 */
	public function save_nav_menu() {

		$menu_ID = $_POST['menu_ID'];

		ClientDash_Core_Page_Settings_Tab_Menus::save_cd_menu( $menu_ID );

		// We're done!
		die();
	}

	/**
	 * Creates a nav menu item and it's sub-menus. Used for initial creation of nav menus.
	 *
	 * @since Client Dash 1.6
	 */
	public function populate_nav_menu() {

		// Get our POST data from AJAX
		$menu_item          = $_POST['menu_item'];
		$menu_item_position = $_POST['menu_item_position'];
		$menu_ID            = $_POST['menu_ID'];
		$role               = $_POST['role'];

		// Get the role object (for capabilities)
		$role = get_role( $role );

		// Pass over if current role doesn't have the capabilities
		// TODO Make sure this works well with parent -> child relationships
		if ( ! array_key_exists( $menu_item['capability'], $role->capabilities ) ) {
			die();
		}

		// Skip links
		// TODO Figure out how to better deal with this
		if ( $menu_item['menu_title'] == 'Links' ) {
			die();
		}

		// Deal with "Plugins" having an extra space
		$menu_item['menu_title'] = trim( $menu_item['menu_title'] );

		$sorted      = ClientDash_Core_Page_Settings_Tab_Menus::sort_original_admin_menu( $menu_item );
		$args        = $sorted[0];
		$custom_meta = $sorted[1];

		// Predefined menu position
		$args['menu-item-position'] = $menu_item_position;

		$ID = wp_update_nav_menu_item( $menu_ID, 0, $args );

		if ( ! is_wp_error( $ID ) ) {
			foreach ( $custom_meta as $meta_name => $meta_value ) {
				if ( $meta_value ) {
					update_post_meta( $ID, $meta_name, $meta_value );
				}
			}
		}

		// If there are submenus, cycle through them
		if ( isset( $menu_item['submenus'] ) && ! empty( $menu_item['submenus'] ) ) {

			foreach ( $menu_item['submenus'] as $position => $submenu_item ) {

				$sorted      = ClientDash_Core_Page_Settings_Tab_Menus::sort_original_admin_menu( $submenu_item, $menu_item );
				$args        = $sorted[0];
				$custom_meta = $sorted[1];

				// Make it a child
				$args['menu-item-parent-id'] = $ID;

				// Predefined menu position
				$args['menu-item-position'] = $position;

				$submenu_ID = wp_update_nav_menu_item( $menu_ID, 0, $args );

				if ( ! is_wp_error( $submenu_ID ) ) {
					foreach ( $custom_meta as $meta_name => $meta_value ) {
						if ( $meta_value ) {
							update_post_meta( $submenu_ID, $meta_name, $meta_value );
						}
					}
				}

				// Also update custom meta for it's parent
				update_post_meta( $submenu_ID, 'cd-submenu-parent', $submenu_item['parent_slug'] );
			}
		}

		// We're done!
		die();
	}
}

new ClientDash_AJAX();