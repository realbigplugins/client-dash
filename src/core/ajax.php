<?php

/**
 * Class ClientDash_AJAX
 *
 * Adds all AJAX functionality to Client Dash
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Base Functionality
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

		update_option( 'cd_content_sections_roles', $ClientDash->option_defaults['content_sections_roles'] );

		echo __( 'Roles successfully reset!', 'client-dash' );

		die();
	}

	/**
	 * Resets ALL Client Dash settings by deleting them.
	 *
	 * @since Client Dash 1.6
	 */
	public function reset_all_settings() {

		global $ClientDash, $ClientDash_Core_Page_Settings_Tab_Widgets;

		// Cycle through all option defaults and delete them
		foreach ( $ClientDash->option_defaults as $name => $value ) {
			delete_option( "cd_$name" );
		}

		// Remove all modified admin menus
		foreach ( get_editable_roles() as $role_ID => $role ) {

			// Get the menu object
			$menu_object = wp_get_nav_menu_object( "cd_admin_menu_$role_ID" );

			// If it doesn't exist, it returns false. So skip
			if ( ! $menu_object ) {
				continue;
			}

			wp_delete_nav_menu( "cd_admin_menu_$role_ID" );                  // Delete the nav menu
			delete_transient( "cd_adminmenu_output_$menu_object->term_id" ); // Cached menu info
			delete_option( "{$menu_object->name}_modified" );                // Menu output
			delete_option( "cd_adminmenu_disabled_$menu_object->term_id" );  // Menu disable option
		}

		// Remove all of the widgets

		// Prevent widget syncing
		add_filter( 'cd_sync_widgets', '__return_false' );

		// This forces the widgets to be reset
		delete_option( 'cd_populate_dashboard_widgets' );

		// Remove each CD sidebar settings
		$sidebars = get_option( 'sidebars_widgets' );
		foreach ( $ClientDash_Core_Page_Settings_Tab_Widgets->sidebars as $sidebar ) {
			unset( $sidebars[ $sidebar['id'] ] );
		}
		update_option( 'sidebars_widgets', $sidebars );

		// Remove individual widget settings
		foreach ( ClientDash::$_cd_widgets as $widget_ID => $widget ) {
			delete_option( "widget_$widget_ID" );
		}

		echo __( 'Settings successfully reset!', 'client-dash' );

		die();
	}

	/**
	 * Replaces wp_ajax_add_menu_item() (wp-admin/includes/ajax-actions.php:~1056).
	 *
	 * @since Client Dash 1.6
	 */
	public function add_menu_item() {

		global $ClientDash;

		// Security
		check_ajax_referer( 'add-menu_item', 'menu-settings-column-nonce' );
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( - 1 );
		}

		// Save the new items
		$item_ids = ClientDash_Core_Page_Settings_Tab_Menus::save_menu_items( 0, $_POST['menu-item'] );

		// Setup the new items
		$menu_items = array();
		foreach ( (array) $item_ids as $menu_item_id ) {
			$menu_obj = get_post( $menu_item_id );
			if ( ! empty( $menu_obj->ID ) ) {
				$menu_obj     = wp_setup_nav_menu_item( $menu_obj );
				$menu_items[] = $menu_obj;
			}
		}

		// Include the custom CD walker class
		include_once( $ClientDash->path . '/core/tabs/settings/menus/walkerclass.php' );

		// Output the newly populated nav menu
		if ( ! empty( $menu_items ) ) {
			$args = array(
				'after'       => '',
				'before'      => '',
				'link_after'  => '',
				'link_before' => '',
				'walker'      => new Walker_Nav_Menu_Edit_CD(),
			);
			echo walk_nav_menu_tree( $menu_items, 0, (object) $args );
		}

		wp_die();
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

		// Deal with "Plugins" having an extra space
		$menu_item['menu_title'] = trim( $menu_item['menu_title'] );

		// Deal with "Plugins" having html
		if ( strpos( $menu_item['menu_title'], 'Plugins') !== false ) {
			$menu_item['menu_title'] = 'Plugins';
		}

		// If icon is using "none" or "div", set accordingly
		if ( $menu_item['icon_url'] == 'none' || $menu_item['icon_url'] == 'div' ) {
			unset( $menu_item['icon_url'] );
		}

		// Only add the item if the user is an administrator or if the user has the correct capabilities
		$no_parent = false;
		if ( $role->has_cap( 'manage_options' ) || array_key_exists( $menu_item['capability'], $role->capabilities ) ) {

			$args = ClientDash_Core_Page_Settings_Tab_Menus::sort_original_admin_menu( $menu_item );

			// Predefined menu position
			$args['position'] = $menu_item_position;

			$ID = ClientDash_Core_Page_Settings_Tab_Menus::update_menu_item( $menu_ID, 0, $args );
		} else {
			$no_parent = true;
		}

		// If there are submenus, cycle through them
		if ( isset( $menu_item['submenus'] ) && ! empty( $menu_item['submenus'] ) ) {

			foreach ( $menu_item['submenus'] as $position => $submenu_item ) {

				if ( ! isset( $ID ) ) {
					continue;
				}

				// Pass over if current role doesn't have the capabilities, unless the role is an admin
				if ( ! $role->has_cap( 'manage_options' ) && ! array_key_exists( $submenu_item['capability'], $role->capabilities ) ) {
					continue;
				}

				$args = ClientDash_Core_Page_Settings_Tab_Menus::sort_original_admin_menu( $submenu_item, $menu_item );

				// Make it a child IF it has a parent, otherwise make it top-level
				if ( ! $no_parent ) {
					$args['parent-id']         = $ID;
					$args['cd-submenu-parent'] = $submenu_item['parent_slug'];
				}

				// Predefined menu position
				$args['position'] = $position;

				ClientDash_Core_Page_Settings_Tab_Menus::update_menu_item( $menu_ID, 0, $args );
			}
		}

		// We're done!
		die();
	}
}

new ClientDash_AJAX();