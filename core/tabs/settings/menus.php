<?php

// TODO Handling roles with multiple words
// TODO Show existing menus at top so admin knows what menus exist and if they're disabled / enabled
// TODO Page title broke
// TODO Pretty icon selector

// TODO Clean up warnings, notices, and stricts
// TODO Re-order methods
// TODO Documentation


/**
 * Class ClientDash_Core_Page_Settings_Tab_AdminMenu
 *
 * Adds the core content section for Settings -> Admin Menu.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since Client Dash 1.6
 */
class ClientDash_Core_Page_Settings_Tab_Menus extends ClientDash {

	/**
	 * The pre-modified admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public $original_admin_menu;

	/**
	 * The available items to show on the left.
	 *
	 * @since Client Dash 1.6
	 */
	public $available_items;

	/**
	 * The menu ID for the currently being edited cd_admin_menu nav menu.
	 *
	 * @since Client Dash 1.6
	 */
	public $menu_ID;

	/**
	 * The role that the current menu is for.
	 *
	 * @since Client Dash 1.6
	 */
	public $role;

	/**
	 * Lets us know if we're making a new menu.
	 *
	 * @since Client Dash 1.6
	 */
	public $create_new = false;

	public $total_menu_items;

	/**
	 * The ID's for all of the CD nav menus (one for each role).
	 *
	 * @since Client Dash 1.6
	 */
	// TODO Test if new roles have been added, and if so, create them (or at least tell the user they need to do so and have a button for doing it)
	public $all_menu_IDs;

	/**
	 * All WordPress core nav menu items (aside from post types).
	 *
	 * Unfortunatley, there's no good way to get this dynamically BECAUSE I can't tell the
	 * difference between an item added by WP Core and an item added by a plugin. So my
	 * work-around is to define WP Core items and have the rest automatically fall into the
	 * plugin / theme category. This is fine, we just need to make sure this array is always
	 * up to date.
	 *
	 * @since Client Dash 1.6
	 */
	public static $wp_core = array(
		'Dashboard'  => array(
			'url'      => 'index.php',
			'icon'     => 'dashicons-dashboard',
			'submenus' => array(
				'Home'     => 'index.php',
				'My Sites' => 'my-sites.php',
				'Updates'  => 'update-core.php'
			)
		),
		'Comments'   => array(
			'url'      => 'edit-comments.php',
			'icon'     => 'dashicons-admin-comments',
			// TODO Find better way to deal with this
			'submenus' => array(
				'All Comments' => 'index.php'
			)
		),
		'Appearance' => array(
			'url'      => 'themes.php',
			'icon'     => 'dashicons-admin-appearance',
			'submenus' => array(
				'Themes'    => 'themes.php',
				'Customize' => 'customize.php',
				'Widgets'   => 'widgets.php',
				'Menus'     => 'nav-menus.php',
				'Editor'    => 'theme-editor.php'
			)
		),
		'Plugins'    => array(
			'url'      => 'plugins.php',
			'icon'     => 'dashicons-admin-plugins',
			'submenus' => array(
				'Installed Plugins' => 'plugins.php',
				'Add New'           => 'plugin-install.php',
				'Editor'            => 'plugin-editor.php'
			)
		),
		'Users'      => array(
			'url'      => 'users.php',
			'icon'     => 'dashicons-admin-users',
			'submenus' => array(
				'All Users'    => 'users.php',
				'Add New'      => 'user-new.php',
				'Your Profile' => 'profile.php'
			)
		),
		'Tools'      => array(
			'url'      => 'tools.php',
			'icon'     => 'dashicons-admin-tools',
			'submenus' => array(
				'Available Tools' => 'tools.php',
				'Import'          => 'import.php',
				'Export'          => 'export.php'
			)
		),
		'Settings'   => array(
			'url'      => 'options-general.php',
			'icon'     => 'dashicons-admin-settings',
			'submenus' => array(
				'General'    => 'options-general.php',
				'Writing'    => 'options-writing.php',
				'Reading'    => 'options-reading.php',
				'Discussion' => 'options-discussion.php',
				'Media'      => 'options-media.php',
				'Permalinks' => 'options-permalink.php'
			)
		)
	);

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.6
	 */
	function __construct() {

		global $ClientDash;

		// Get the original admin menus
		add_action( 'admin_menu', array( $this, 'get_orig_admin_menu' ), 99990 );

		// Delete the menu if told so from POST
		if ( isset( $_GET['cd_delete_admin_menu'] ) ) {
			add_action( 'admin_menu', array( $this, 'delete_nav_menu' ), 99995 );
		}

		// Create if told so from POST
		if ( isset( $_POST['cd_create_admin_menu'] ) ) {
			add_action( 'admin_menu', array( $this, 'create_nav_menu' ), 99995 );
		}

		// Create/Get CD nav menu
		add_action( 'admin_menu', array( $this, 'get_cd_nav_menus' ), 99996 );

		// Remove the original admin menu (and also add the modified menu)
		add_action( 'admin_menu', array( $this, 'remove_orig_admin_menu' ), 99999 );

		// Hide the CD nav menu from the normal nav menu page
		add_filter( 'wp_get_nav_menus', array( $this, 'hide_cd_nav_menu' ) );

		// Use custom walker menu for displaying sortable menu items
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'return_new_walker_menu' ), 10, 2 );

		// Add the content
		$this->add_content_section( array(
			'name'     => 'Core Menu Settings',
			'page'     => 'Settings',
			'tab'      => 'Menus',
			'callback' => array( $this, 'block_output' )
		) );

		// Anything in here will ONLY apply to this particular settings page
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'cd_settings'
		     && isset( $_GET['tab'] ) && $_GET['tab'] == 'menus'
		) {

			// Required functions
			include_once( ABSPATH . '/wp-admin/includes/nav-menu.php' );

			// Includes callbacks
			include_once( $ClientDash->path . '/core/tabs/settings/menus/availableitems-callbacks.php' );

			// Disable the default CD form wrap
			add_filter( 'cd_settings_form_wrap', '__return_false' );

			// Disable the default CD "Save Changes" button
			add_filter( 'cd_submit', '__return_false' );

			// Add nav-menus body class for our custom CD page
			add_filter( 'admin_body_class', array( $this, 'my_class_names' ) );

			// Save menus (only when updating)
			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' ) {
				add_action( 'admin_menu', array( $this, 'save_menu' ), 99998 );
			}

			// Make sure we include the WP nav menu script for our custom CD page
			wp_enqueue_script( 'nav-menu' );

			// Localize the default items for "nav-menu" script
			$nav_menus_l10n = array(
				'oneThemeLocationNoMenus' => false,
				'moveUp'                  => __( 'Move up one' ),
				'moveDown'                => __( 'Move down one' ),
				'moveToTop'               => __( 'Move to the top' ),
				/* translators: %s: previous item name */
				'moveUnder'               => __( 'Move under %s' ),
				/* translators: %s: previous item name */
				'moveOutFrom'             => __( 'Move out from under %s' ),
				/* translators: %s: previous item name */
				'under'                   => __( 'Under %s' ),
				/* translators: %s: previous item name */
				'outFrom'                 => __( 'Out from under %s' ),
				/* translators: 1: item name, 2: item position, 3: total number of items */
				'menuFocus'               => __( '%1$s. Menu item %2$d of %3$d.' ),
				/* translators: 1: item name, 2: item position, 3: parent item name */
				'subMenuFocus'            => __( '%1$s. Sub item number %2$d under %3$s.' ),
			);
			wp_localize_script( 'nav-menu', 'menus', $nav_menus_l10n );
		}
	}

	/**
	 * Get the original, un-modified admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function get_orig_admin_menu() {
		global $menu, $submenu;

		foreach ( $menu as $menu_location => $menu_item ) {

			$menu_array = array(
				'menu_title' => isset( $menu_item[0] ) ? $menu_item[0] : false,
				'capability' => isset( $menu_item[1] ) ? $menu_item[1] : false,
				'menu_slug'  => isset( $menu_item[2] ) ? $menu_item[2] : false,
				'page_title' => isset( $menu_item[3] ) ? $menu_item[3] : false,
				'position'   => $menu_location,
				'hookname'   => isset( $menu_item[5] ) ? $menu_item[5] : false,
				'icon_url'   => isset( $menu_item[6] ) ? $menu_item[6] : false
			);

			$orig_menu[ $menu_location ] = $menu_array;

			// Loop through all of the sub-menus IF they exist
			if ( ! empty( $submenu[ $menu_array['menu_slug'] ] ) && is_array( $submenu[ $menu_array['menu_slug'] ] ) ) {
				foreach ( $submenu[ $menu_array['menu_slug'] ] as $submenu_location => $submenu_item ) {

					$submenu_array = array(
						'menu_title'  => isset( $submenu_item[0] ) ? $submenu_item[0] : false,
						'capability'  => isset( $submenu_item[1] ) ? $submenu_item[1] : false,
						'menu_slug'   => isset( $submenu_item[2] ) ? $submenu_item[2] : false,
						'page_title'  => isset( $submenu_item[3] ) ? $submenu_item[3] : false,
						'parent_slug' => $menu_array['menu_slug']
					);

					$orig_menu[ $menu_location ]['submenus'][ $submenu_location ] = $submenu_array;
				}
			}
		}

		// Sort the menus, then re-index them
		ksort( $orig_menu );

		$i = - 1;
		foreach ( $orig_menu as $menu_item ) {
			$i ++;

			$i_parent       = $i;
			$new_menu[ $i ] = $menu_item;

			if ( isset( $menu_item['submenus'] ) ) {

				unset( $new_menu[ $i ]['submenus'] );

				foreach ( $menu_item['submenus'] as $submenu_item ) {
					$i ++;

					$new_menu[ $i_parent ]['submenus'][ $i ] = $submenu_item;
				}
			}
		}
//		$orig_menu = array_values( $orig_menu );
//		foreach ( $orig_menu as $menu_position => $menu_item ) {
//
//			if ( isset( $menu_item['submenus'] ) ) {
//				ksort( $orig_menu[ $menu_position ]['submenus'] );
//				$orig_menu[ $menu_position ]['submenus'] = array_values( $orig_menu[ $menu_position ]['submenus'] );
//			}
//		}

		$this->total_menu_items    = $i;
		$this->original_admin_menu = $new_menu;
	}

	/**
	 * Deletes the specified nav menu permanently.
	 *
	 * @since Client Dash 1.6
	 */
	public function delete_nav_menu() {

		$menu_ID = $_GET['cd_delete_admin_menu'];

		// Make sure they got here correctly
		check_admin_referer( 'delete-cd_nav_menu-' . $menu_ID );

		$menu_object = wp_get_nav_menu_object( $menu_ID );

		// If we got here incorrectly, the menu may not actually exist
		if ( empty( $menu_object ) || is_wp_error( $menu_object ) ) {
			return;
		}

		// Delete everything pertaining to this menu
		delete_transient( "cd_adminmenu_output_$menu_ID" ); // Cached menu info
		delete_option( "{$menu_object->name}_modified" );   // Menu output
		delete_option( "cd_adminmenu_disabled_$menu_ID" );  // Menu disable option
		wp_delete_nav_menu( $menu_ID );                     // The nav menu item

		// Redirect
		wp_redirect( remove_query_arg( array( 'cd_delete_admin_menu', '0', '_wpnonce', 'menu' ) ) );
		exit();
	}

	/**
	 * Creates each role's nav menu for the first time.
	 *
	 * @since Client Dash 1.6
	 */
	public function create_nav_menu() {

		// The role name to create for
		$role_name = $_POST['cd_create_admin_menu'];

		// Bail if it exists (fail-safe)
		$menu_object = wp_get_nav_menu_object( "cd_admin_menu_$role_name" );
		if ( ! empty( $menu_object ) ) {
			return;
		}

		// Create the nav menu
		$this->menu_ID = wp_create_nav_menu( "cd_admin_menu_$role_name" );

		// Only import and save the existing menu items IF the checkbox is checked (default)
		if ( isset( $_POST['import_items'] ) ) {

			// Now populate it with menu items (this is a hefty memory toll)
			$this->populate_nav_menu( $role_name );

			// Save it into our modified menu option
			$this->save_cd_menu( $this->menu_ID );
		}
	}

	public function populate_nav_menu( $role ) {

		global $ClientDash;

		$menu_items = wp_get_nav_menu_items( $this->menu_ID );

		$AJAX_output = [ ];

		$AJAX_output['menu_ID'] = $this->menu_ID;
		$AJAX_output['role']    = $role;
		$AJAX_output['total']   = $this->total_menu_items;

		// Cycle through each item and create the nav menu accordingly
		foreach ( $this->original_admin_menu as $position => $menu ) {

			// REMOVE
			// Call AJAX sending:
			// $menu_item ($menu)
			// $menu_item_position ($position)
			// $menu_ID ($this->menu_ID)
			// $role ($role)

			// Prepare AJAX data to send
			$AJAX_output['menu_items'][] = array(
				'menu_item'          => $menu,
				'menu_item_position' => $position
			);

//			// Pass over if current role doesn't have the capabilities
//			// TODO Make sure this works well with parent -> child relationships
//			if ( ! array_key_exists( $menu['capability'], $role_info->capabilities ) ) {
//				continue;
//			}
//
//			// Skip links
//			// TODO Figure out how to better deal with this
//			if ( $menu['menu_title'] == 'Links' ) {
//				continue;
//			}
//
//			// Deal with "Plugins" having an extra space
//			$menu['menu_title'] = trim( $menu['menu_title'] );
//
//			$sorted      = self::sort_original_admin_menu( $menu );
//			$args        = $sorted[0];
//			$custom_meta = $sorted[1];
//
//			$ID = wp_update_nav_menu_item( $this->menu_ID, 0, $args );
//
//			if ( ! is_wp_error( $ID ) ) {
//				foreach ( $custom_meta as $meta_name => $meta_value ) {
//					update_post_meta( $ID, $meta_name, $meta_value );
//				}
//			}
//
//			// If there are submenus, cycle through them
//			if ( isset( $menu['submenus'] ) && ! empty( $menu['submenus'] ) ) {
//
//				foreach ( $menu['submenus'] as $submenu_item ) {
//
//					$sorted      = self::sort_original_admin_menu( $submenu_item, $menu );
//					$args        = $sorted[0];
//					$custom_meta = $sorted[1];
//
//					// Make it a child
//					$args['menu-item-parent-id'] = $ID;
//
//					$submenu_ID = wp_update_nav_menu_item( $this->menu_ID, 0, $args );
//
//					if ( ! is_wp_error( $submenu_ID ) ) {
//						foreach ( $custom_meta as $meta_name => $meta_value ) {
//							update_post_meta( $submenu_ID, $meta_name, $meta_value );
//						}
//					}
//
//					// Also update custom meta for it's parent
//					update_post_meta( $submenu_ID, 'cd-submenu-parent', $submenu_item['parent_slug'] );
//				}
//			}
		}

		// Send off the ajax data to be localized
		$ClientDash->jsData['navMenusAJAX'] = $AJAX_output;
	}

	/**
	 * Gets our main admin menu nav menu, and creates it if it doesn't already
	 * exist.
	 *
	 * @since Client Dash 1.6
	 */
	public function get_cd_nav_menus() {

		// Get the nav menu for each role
		$roles = get_editable_roles();
		foreach ( $roles as $role_ID => $role ) {

			$menu_object = wp_get_nav_menu_object( "cd_admin_menu_$role_ID" );

			// If it doesn't exist return false, otherwise return the menu ID
			if ( ! $menu_object ) {
				$this->all_menu_IDs[ $role_ID ] = false;
			} else {
				$this->all_menu_IDs[ $role_ID ] = $menu_object->term_id;
			}
		}

		$this->get_current_menu();
	}

	/**
	 * Gets the menu being currently edited. Also gets current role.
	 *
	 * @since Client Dash 1.6
	 */
	public function get_current_menu() {

		global $cd_current_menu_id;

		// If a menu isn't set, just take the first one that exists. Otherwise, get it from the url
		if ( isset( $_GET['menu'] ) ) {
			$this->menu_ID = $_GET['menu'];
		} else {

			// Cycle through to find the first role with a menu ID
			foreach ( $this->all_menu_IDs as $menu_ID ) {
				if ( ! $menu_ID ) {
					continue;
				}

				$this->menu_ID = $menu_ID;
				break;
			}
		}

		$cd_current_menu_id = $this->menu_ID;

		// If this menu doesn't exist, return false
		$menu_object = wp_get_nav_menu_object( $this->menu_ID );
		if ( ! $menu_object ) {
			$this->menu_ID = false;
			$this->role    = false;

			// We may be on create a new menu?
			if ( isset( $_GET['menu'] ) ) {
				$this->role       = $_GET['menu'];
				$this->create_new = true;
			}

			// Globalize the menu ID
			$cd_current_menu_id = $this->menu_ID;

			return;
		}

		// Now figure out which role this menu is for
		foreach ( $this->all_menu_IDs as $role => $menu ) {
			if ( $this->menu_ID == $menu ) {
				$this->role = $role;

				return;
			}
		}
	}

	// TODO Is this function necessary? It's not populating the menu
	public static function return_cd_nav_menu() {
		// Get our nav menu (if it exists)
		$term = get_term_by( 'name', 'cd_admin_menu', 'nav_menu' );

		$term_id = $term->term_id;

		// If it doesn't exist, create it
		if ( ! $term ) {
			$term_id = wp_create_nav_menu( 'cd_admin_menu' );
		}

		return $term_id;
	}

	/**
	 * Filters the walker class used on the CD admin menu page.
	 *
	 * @return string The new Walker class.
	 */
	public function return_new_walker_menu( $walker, $menu ) {

		// Needed to get the plugin path
		global $ClientDash;

		// When being loaded via AJAX, we won't have the menu ID, but we can assume that
		// the supplied menu is correct
		if ( ! isset( $this->menu_ID ) ) {

			$this->menu_ID = $menu;

			// Includes our modified walker class for when ajax-actions.php tries to call it
			include_once( $ClientDash->path . '/core/tabs/settings/menus/walkerclass.php' );
		}

		// TODO Address PHP notices in AJAX response

		if ( $menu == $this->menu_ID ) {
			return 'Walker_Nav_Menu_Edit_CD';
		}

		return $walker;
	}

	/**
	 * Filters the returned nav menus on the nav menu edit screen to remove
	 * the CD admin nav menu from the list.
	 *
	 * @param array $menus The supplied available nav menus.
	 *
	 * @return mixed The filtered nav menus.
	 */
	public function hide_cd_nav_menu( $menus ) {

		// Get the current screen and make sure it's on the nav-menus.php page
		global $current_screen;

		if ( $current_screen->base != 'nav-menus' ) {
			return $menus;
		}

		// Cycle through each available menu and remove it if it's name is cd_admin_menu
		foreach ( $menus as $key => $menu_ID ) {
			$menu = wp_get_nav_menu_object( $menu_ID );

			if ( strpos( $menu->name, 'cd_admin_menu' ) !== false ) {
				unset( $menus[ $key ] );
			}
		}

		return $menus;
	}

	/**
	 * Returns the original, un-modified admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public static function return_orig_admin_menu() {
		global $menu, $submenu;

		foreach ( $menu as $menu_location => $menu_item ) {

			$menu_array = array(
				'menu_title' => isset( $menu_item[0] ) ? $menu_item[0] : false,
				'capability' => isset( $menu_item[1] ) ? $menu_item[1] : false,
				'menu_slug'  => isset( $menu_item[2] ) ? $menu_item[2] : false,
				'page_title' => isset( $menu_item[3] ) ? $menu_item[3] : false,
				'position'   => $menu_location,
				'hookname'   => isset( $menu_item[5] ) ? $menu_item[5] : false,
				'icon_url'   => isset( $menu_item[6] ) ? $menu_item[6] : false
			);

			$orig_menu[ $menu_location ] = $menu_array;

			// Loop through all of the sub-menus IF they exist
			if ( ! empty( $submenu[ $menu_array['menu_slug'] ] ) && is_array( $submenu[ $menu_array['menu_slug'] ] ) ) {
				foreach ( $submenu[ $menu_array['menu_slug'] ] as $submenu_location => $submenu_item ) {

					$submenu_array = array(
						'menu_title'  => isset( $submenu_item[0] ) ? $submenu_item[0] : false,
						'capability'  => isset( $submenu_item[1] ) ? $submenu_item[1] : false,
						'menu_slug'   => isset( $submenu_item[2] ) ? $submenu_item[2] : false,
						'page_title'  => isset( $submenu_item[3] ) ? $submenu_item[3] : false,
						'parent_slug' => $menu_array['menu_slug']
					);

					$orig_menu[ $menu_location ]['submenus'][ $submenu_location ] = $submenu_array;
				}
			}
		}

		// Sort the menus, then re-index them
		ksort( $orig_menu );
		$orig_menu = array_values( $orig_menu );
		foreach ( $orig_menu as $menu_position => $menu_item ) {

			if ( isset( $menu_item['submenus'] ) ) {
				ksort( $orig_menu[ $menu_position ]['submenus'] );
				$orig_menu[ $menu_position ]['submenus'] = array_values( $orig_menu[ $menu_position ]['submenus'] );
			}
		}

		return $orig_menu;
	}

	public static function sort_original_admin_menu( $menu, $is_submenu = false ) {
		// Account for "Comments" having html in the title
		if ( strpos( $is_submenu ? $is_submenu['menu_title'] : $menu['menu_title'], 'Comments' ) !== false ) {
			if ( $is_submenu ) {
				$is_submenu['menu_title'] = 'Comments';
			} else {
				$menu['menu_title'] = 'Comments';
			}
		}

		// Account for whitespace
		$menu['menu_title'] = trim( $menu['menu_title'] );

		// Args to send to new nav menu item
		$args = array(
			'menu-item-title'  => $menu['menu_title'],
			'menu-item-url'    => $menu['menu_slug'],
			'menu-item-status' => 'publish'
		);

		// Meta to attach to new nav menu item
		$custom_meta = array(
			'cd-original-title'        => $menu['menu_title'],
			'cd-icon'                  => isset( $menu['icon_url'] ) ? $menu['icon_url'] : false,
			'cd-url'                   => $menu['menu_slug'],
			'cd-page-title'            => $menu['page_title'],
			'cd-duplicate-parent-slug' => false
		);

		// Figure out what we're dealing with
		if ( strpos( $menu['menu_slug'], 'separator' ) !== false ) {

			// Separator
			$args['menu-item-title'] = 'Separator';

			$custom_meta['cd-original-title']   = 'Separator';
			$custom_meta['cd-type']             = 'separator';
			$custom_meta['cd-separator-height'] = 5;

			if ( ! $is_submenu ) {
				$custom_meta['cd-object-type'] = 'toplevel';
			} else {
				$custom_meta['cd-object-type'] = 'submenu';
			}

		} elseif ( self::strposa( $menu['menu_slug'], array(
				'edit.php',
				'post-new.php',
				'media-new.php',
				'upload.php',
				'link_category'
			) ) !== false
		) {
			// Posts (and the weird link thing...)
			$custom_meta['cd-type'] = 'post_type';

			// Which type?
			switch ( $menu['menu_title'] ) {
				case 'Posts':
					$custom_meta['cd-post-type'] = 'post';
					break;
				case 'Media':
					$custom_meta['cd-post-type'] = 'media';
					break;
				default:
					// Custom post types caught by this. Takes whatever is after "edit.php?post_type="
					preg_match( '/[^=]+$/', $menu['menu_slug'], $posttype );
					$custom_meta['cd-post-type'] = $posttype;
			}
		} elseif ( self::strposa( $menu['menu_slug'], array(
				'edit-tags.php'
			) ) !== false
		) {

			// Taxonomy
			$custom_meta['cd-type'] = 'taxonomy';

			// Which type?
			switch ( $is_submenu ? $is_submenu['menu_title'] : $menu['menu_title'] ) {
				case 'Posts':
					$custom_meta['cd-post-type'] = 'post';
					break;
				case 'Media':
					$custom_meta['cd-post-type'] = 'media';
					break;
				default:
					// Custom post types caught by this. Takes whatever is after "edit.php?post_type="
					preg_match( '/[^=]+$/', $menu['menu_slug'], $posttype );
					$custom_meta['cd-post-type'] = $posttype;
			}

			// This looks nasty, but it's not too bad. If $is_submenu has a value, then we're dealing with a
			// submenu, and that value is it's parent. So the first checks if it is NOT a submenu and then looks
			// one level in for the parent key. The second checks if it IS a submenu, and then looks in the parent's
			// "submenus" array for the submenu key.
		} elseif ( ( ! $is_submenu && array_key_exists(
					$menu['menu_title'],
					self::$wp_core
				) )
		           || ( isset( self::$wp_core[ $is_submenu['menu_title'] ]['submenus'] )
		                && $is_submenu && array_key_exists(
			                $menu['menu_title'],
			                self::$wp_core[ $is_submenu['menu_title'] ]['submenus']
		                ) )
		) {

			// WordPress core
			$custom_meta['cd-type'] = 'wp_core';
		} else {
			// The catchall for everything else (defaults to plugin)
			$custom_meta['cd-type'] = 'plugin';
		}

		return array( $args, $custom_meta );
	}

	/**
	 * Removes the original admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function remove_orig_admin_menu() {

		// Don't replace the default admin menu if the current user's role does NOT have
		// a menu ready, OR if the menu is disabled OOOOOR if it has no items
		$menu_items   = wp_get_nav_menu_items( $this->menu_ID );
		$current_role = $this->get_user_role();
		if ( ! $this->all_menu_IDs[ $current_role ]
		     || get_option( "cd_adminmenu_disabled_{$this->all_menu_IDs[$current_role]}" )
		     || empty( $menu_items )
		) {
			return;
		}

		// If the menus have been set, remove all of them
		if ( ! empty( $this->original_admin_menu ) ) {
			foreach ( $this->original_admin_menu as $menu_item ) {
				remove_menu_page( $menu_item['menu_slug'] );

				// Cycle through all sub-menus now, if they exist
				if ( ! empty( $menu_item['submenus'] ) ) {
					foreach ( $menu_item['submenus'] as $submenu_item ) {
						remove_submenu_page( $menu_item['menu_slug'], $submenu_item['menu_slug'] );
					}
				}
			}
		}

		// We removed it, now add it
		$this->add_modified_admin_menu();
	}

	/**
	 * Adds the new, modified admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function add_modified_admin_menu() {

		global $menu, $cd_parent_file, $_registered_pages, $plugin_page, $cd_submenu_file;

		// This is a strange little hack. When moving a sub-menu page to a top-level page, there are some
		// caveats. One being, WordPress doesn't know what the heck to do!... You will get a permissions
		// denied error without this line because of the hook names being different than normal. This simply
		// ensures that it will not be un-reachable.
		if ( ! empty( $plugin_page ) ) {
			$_registered_pages["admin_page_$plugin_page"] = true;
		}

		// In the case of a sub-menu item being moved to a parent item, WordPress will be confused
		// about which menu item is active. So I compensate for this by overriding the "self" and
		// "parent_file" globals with the new (previously sub-menu) slug. This corrects the issue.

		// We compare the REQUEST_URI (minus all extra query args), but we allow the query args
		// that will be found within the slug
		$allowed_args = array( 'page' => true, 'post_type' => true, 'taxonomy' => true, 'tab' => true );
		$url          = remove_query_arg( array_keys( array_diff_key( $_GET, $allowed_args ) ) );
		if ( $url === false ) {
			// If false, there were no extra query args, so just use the REQUEST_URI
			$url = $_SERVER['REQUEST_URI'];
		}

		// Filter out the WP base url (from wp-admin/menu-header.php:~16)
		$url = preg_replace( '|^.*/wp-admin/network/|i', '', $url );
		$url = preg_replace( '|^.*/wp-admin/|i', '', $url );
		$url = preg_replace( '|^.*/plugins/|i', '', $url );
		$url = preg_replace( '|^.*/mu-plugins/|i', '', $url );

		// Get current role
		$current_role = $this->get_user_role();

		// Get the modified menu
		$menu_object   = wp_get_nav_menu_object( $this->all_menu_IDs[ $current_role ] );
		$modified_menu = get_option( "{$menu_object->name}_modified" );

		// If the modified menus are set, then add them
		if ( $modified_menu ) {
			foreach ( $modified_menu as $menu_position => $menu_item ) {

				// Now for each top level item, let's see if it's currently active
				if ( $url == $menu_item['menu_slug'] ) {
					$cd_parent_file = $menu_item['menu_slug'];
					add_filter( 'parent_file', array( $this, 'modify_self' ) );
				}

				$classes = [ ];

				// If a separator, do that instead
				if ( strpos( $menu_item['menu_slug'], 'separator' ) !== false ) {
					$menu[ $menu_position ] = array(
						'',
						'read',
						$menu_item['menu_slug'],
						'',
						'wp-menu-separator'
					);
					$classes[]              = 'wp-menu-separator';
				} else {
					add_menu_page(
						! empty( $menu_item['page_title'] ) ? $menu_item['page_title'] : null,
						! empty( $menu_item['menu_title'] ) ? $menu_item['menu_title'] : null,
						! empty( $menu_item['capability'] ) ? $menu_item['capability'] : null,
						! empty( $menu_item['menu_slug'] ) ? $menu_item['menu_slug'] : null,
						! empty( $menu_item['callback'] ) ? $menu_item['callback'] : null,
						! empty( $menu_item['icon_url'] ) ? $menu_item['icon_url'] : 'none',
						$menu_position
					);

					$classes[] = "toplevel_page_$menu_item[menu_slug]";
					$classes[] = 'menu-top';
				}

				// Now for the sub-menus (if they exist)
				if ( ! empty( $menu_item['submenus'] ) ) {

					// Add the class
					$classes[] = 'wp-has-submenu';

					foreach ( $menu_item['submenus'] as $submenu_item ) {

						// Now for each sub-menu item, let's see if it's currently active
						if ( $url == $submenu_item['menu_slug'] ) {
							$cd_parent_file  = $menu_item['menu_slug'];
							$cd_submenu_file = $submenu_item['menu_slug'];
							add_filter( 'parent_file', array( $this, 'modify_self' ) );
						}

						add_submenu_page(
							! empty( $menu_item['menu_slug'] ) ? $menu_item['menu_slug'] : null,
							! empty( $submenu_item['page_title'] ) ? $submenu_item['page_title'] : null,
							! empty( $submenu_item['menu_title'] ) ? $submenu_item['menu_title'] : null,
							! empty( $submenu_item['capability'] ) ? $submenu_item['capability'] : null,
							! empty( $submenu_item['menu_slug'] ) ? $submenu_item['menu_slug'] : null,
							! empty( $submenu_item['callback'] ) ? $submenu_item['callback'] : null
						);
					}
				}
				// Add the menu classes
//				$menu[ $menu_position ][4] = implode( ' ', $classes );
			}
		}
	}

	/**
	 * This is where the magic happens.
	 *
	 * When you tinker around with WP and move sub-menus to parents and parents to submenus... well,
	 * WP doesn't get too happy about it and doesn't know what to do with it. So I need to tell WP
	 * a few things about what's going on. I need to modify 3 globals: $self, $parent_file, and
	 * $submenu_file. These 3 globals tell WP what we're currently viewing. Because of the strange
	 * URL's and moving around of menus, I have to let WP know accordingly what's going on.
	 *
	 * @return mixed The parent file.
	 */
	public function modify_self() {
		global $self, $cd_parent_file, $submenu_file, $cd_submenu_file;

		// Set the self (or what WP thinks we're viewing) to the ENTIRE slug, not just the parent.
		$self = $cd_parent_file;

		// Tell WP what our new submenu file is (because it's custom), otherwise, default to
		// the parent
		$submenu_file = ! empty( $cd_submenu_file ) ? $cd_submenu_file : $cd_parent_file;

		return $cd_parent_file;
	}

	// Add specific CSS class by filter
	public function my_class_names( $classes ) {
		return $classes . ' nav-menus-php cd-nav-menu';
	}

	/**
	 * Saves the admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function save_menu() {
		// TODO Secure page better with nonce

		// Remove the transient so it resets
		delete_transient( "cd_adminmenu_output_$this->menu_ID" );

		$menu_object = wp_get_nav_menu_object( $this->menu_ID );

		// Update menu items
		if ( ! is_wp_error( $_menu_object ) ) {

			// Update the disabled option
			if ( isset( $_POST["cd_adminmenu_disabled_$this->menu_ID"] ) ) {
				update_option( "cd_adminmenu_disabled_$this->menu_ID", '1' );
			} else {
				delete_option( "cd_adminmenu_disabled_$this->menu_ID" );
			}

			// Default WP nav menu save
			wp_nav_menu_update_menu_items( $this->menu_ID, $menu_object->name );

			// Save CD friendly menu
			$this->save_cd_menu( $this->menu_ID );
		}
	}

	public static function save_cd_menu( $menu_ID ) {

		$menu = [ ];

		// Default menu
		$default_menu = array(
			'capability' => 'read',
			'icon_url'   => 'dashicons-admin-generic'
		);

		// Default submenu
		$default_submenu = array(
			'capability' => 'read'
		);

		$menu_items = wp_get_nav_menu_items( $menu_ID );

		// Cycle through all existing items
		foreach ( $menu_items as $item ) {

			// When saving with "Save Menu", we need to grab custom meta from $_POST
			// Otherwise, just grab it from the already saved post meta
			if ( isset( $_POST['action'] ) && $_POST['action'] == 'update' ) {
				$icon = $_POST['menu-item-cd-icon'][ $item->ID ];
				$slug = $_POST['menu-item-cd-url'][ $item->ID ];
			} else {
				$icon = get_post_meta( $item->ID, 'cd-icon', true );
				$slug = get_post_meta( $item->ID, 'cd-url', true );
			}

			if ( $item->menu_item_parent == '0' ) {

				// If a parent item (has no parent)
				$menu[ $item->ID ] = wp_parse_args( array(
					'menu_title' => $item->post_title,
					'menu_slug'  => $slug,
					// TODO Get page title
					'page_title' => get_post_meta( $item->ID, 'cd-page-title', true ),
					'icon_url'   => $icon
				), $default_menu );

				// If this was originally a sub-menu, we need to fix the link (unless the slug is already
				// a hardlink
				if ( strpos( $slug, '.php' ) === false
				     && ( $parent_slug = get_post_meta( $item->ID, 'cd-submenu-parent', true ) )
				) {
					$menu[ $item->ID ]['menu_slug'] = "$parent_slug?page=$slug";
				}

			} else {
				// If a sub-menu item (has a parent)
				$menu[ $item->menu_item_parent ]['submenus'][] = wp_parse_args( array(
					'menu_title'  => $item->post_title,
					'menu_slug'   => $slug,
					// TODO Get page title
					'page_title'  => get_post_meta( $item->ID, 'cd-page-title', true ),
					'parent_slug' => $menu[ $item->menu_item_parent ]['menu_slug']
				), $default_submenu );
			}
		}

		// Re-order menus to be in indexed order
		$i = - 1;
		foreach ( $menu as $menu_position => $menu_item ) {
			$i ++;

			unset( $menu[ $menu_position ] );
			$menu[ $i ] = $menu_item;
		}

		// Save it!
		$menu_object = wp_get_nav_menu_object( $menu_ID );
		update_option( "{$menu_object->name}_modified", $menu );
	}

	/**
	 * Returns the available menu items.
	 *
	 * @since Client Dash 1.6
	 *
	 * @return array|bool The menu items, if they exist, otherwise false.
	 */
	public function get_available_items() {

		global $wp_meta_boxes, $ClientDash, $errors;

		// This establishes the meta boxes on the left side of the screen
		$this->available_items = array(
			'nav-menus' => array(
				'side' => array(
					'default' => array(
						'add-post-type'    => array(
							'id'       => 'add-post-types',
							'title'    => 'Post Types',
							'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'post_types' )
						),
						'add-wp-core'      => array(
							'id'       => 'add-wp-core',
							'title'    => 'WordPress',
							'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'wp_core' )
						),
						'add-plugin'       => array(
							'id'       => 'add-plugin',
							'title'    => 'Plugin / Theme',
							'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'plugin' )
						),
						'add-custom-links' => array(
							'id'       => 'add-custom-links',
							'title'    => 'Custom',
							'callback' => 'wp_nav_menu_item_link_meta_box'
						),
						'add-separator'    => array(
							'id'       => 'add-separator',
							'title'    => 'Separator',
							'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'separator' )
						)
					)
				)
			)
		);

		// Used by the do_accordion_sections()
		$wp_meta_boxes = $this->available_items;

		// Save the information in a transient and get it for faster page loads
		// REMOVE comment
//		$output = get_transient( "cd_adminmenu_output_$this->menu_ID" );
		if ( ! $output && is_nav_menu( $this->menu_ID ) ) {
			// Our modified walker class
			include_once( $ClientDash->path . '/core/tabs/settings/menus/walkerclass.php' );

			$menu_items  = wp_get_nav_menu_items( $this->menu_ID, array( 'post_status' => 'any' ) );
			$edit_markup = wp_get_nav_menu_to_edit( $this->menu_ID );

			$output = array(
				'menu_items'  => $menu_items,
				'edit_markup' => $edit_markup,
				'errors'      => $errors
			);

			// REMOVE comment
//			set_transient( "cd_adminmenu_output_$this->menu_ID", $output, DAY_IN_SECONDS );
		}

		return $output;
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.6
	 */
	public function block_output() {

		$menu_info = $this->get_available_items();

		extract( $menu_info );

		if ( is_wp_error( $edit_markup ) ) {
			$this->error_nag( array_shift( array_shift( $edit_markup->errors ) ) );

			return;
		}

		// Output any errors
		if ( ! empty( $errors ) ) {
			foreach ( $errors as $error ) {
				$this->error_nag( $error );
			}
		}

		// Get the role info (for name)
		$role_name = ucwords( str_replace( '_', ' ', $this->role ) );

		// If we're creating a menu via AJAX currently
		$creating = isset( $_POST['cd_create_admin_menu'] ) ? true : false;

		// From wp-admin/nav-menus.php. Modified for CD use.
		?>

		<?php
		// Only show select area if a menu has been created. Otherwise, this will be shown below
		if ( $this->menu_ID || $this->create_new ) :
			?>
			<div class="manage-menus<?php echo $creating ? ' disabled' : ''; ?>">
				<form method="get">
					<label for="menu" class="selected-menu"><?php _e( 'Select a menu to edit:' ); ?></label>

					<?php // Keep us on the same page! ?>
					<input type="hidden" name="page" value="cd_settings"/>
					<input type="hidden" name="tab" value="menus"/>

					<select id="menu" name="menu">
						<?php
						foreach ( get_editable_roles() as $role_ID => $role ) {
							?>
							<option
								value="<?php echo $this->all_menu_IDs[ $role_ID ] ? $this->all_menu_IDs[ $role_ID ] : $role_ID; ?>"
								<?php selected( $this->role, $role_ID ); ?>>
								<?php echo $role_ID == 'administrator' ? $role['name'] . ' (that\'s you!)' : $role['name']; ?>
							</option>
						<?php
						}
						?>
					</select>

					<span class="submit-btn">
						<input type="submit" class="button-secondary" value="Select"/>
					</span>
				</form>
			</div><!-- /manage-menus -->
		<?php endif; ?>

		<div id="nav-menus-frame">
		<div id="menu-settings-column"
		     class="metabox-holder<?php echo $this->menu_ID && ! $creating ? '' : ' metabox-holder-disabled'; ?>">

			<div class="clear"></div>

			<form id="nav-menu-meta" action="" class="nav-menu-meta" method="post" enctype="multipart/form-data">
				<input type="hidden" name="menu" id="nav-menu-meta-object-id"
				       value="<?php echo esc_attr( $this->menu_ID ); ?>"/>
				<input type="hidden" name="action" value="add-menu-item"/>
				<?php wp_nonce_field( 'add-menu_item', 'menu-settings-column-nonce' ); ?>
				<?php do_accordion_sections( 'nav-menus', 'side', null ); ?>
			</form>

		</div>
		<!-- /#menu-settings-column -->
		<div id="menu-management-liquid" <?php echo $creating ? 'class="disabled"' : ''; ?>>
			<div id="menu-management">
				<form id="update-nav-menu" action="" method="post" enctype="multipart/form-data">
					<div class="menu-edit">
						<?php
						wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
						wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
						wp_nonce_field( 'update-nav_menu', 'update-nav-menu-nonce' );
						?>
						<?php if ( $this->menu_ID ) : ?>
							<input type="hidden" name="action" value="update"/>
						<?php else : ?>
							<input type="hidden" name="action" value="create"/>
						<?php endif; ?>
						<input type="hidden" name="menu" id="menu"
						       value="<?php echo esc_attr( $this->menu_ID ); ?>"/>
						<input type="hidden" id="menu-name" value="cd_admin_menu"/>

						<div id="nav-menu-header">
							<div class="major-publishing-actions">
								<label class="menu-name-label howto open-label" for="menu-name">
									<?php
									// If the menu is set, show it, otherwise, allow user to select which
									// menu to create
									if ( $this->menu_ID ) :
										?>
										<span>Menu Name:</span>

										<span class="cd-nav-menu-title"><?php echo $role_name; ?></span>

										<?php
										// Output a spinner if creating the initial menu
										if ( $creating ) {
											echo '<span class="spinner"></span>';
										}
										?>
									<?php else : ?>
										<span>Choose which role to create a menu for:</span>
										<select name="cd_create_admin_menu">
											<?php
											foreach ( get_editable_roles() as $role_ID => $role ) {

												// Don't show if already created
												if ( $this->all_menu_IDs[ $role_ID ] ) {
													continue;
												}
												?>
												<option
													value="<?php echo $role_ID; ?>"
													<?php selected( $this->role, $role_ID ); ?>>
													<?php echo $role_ID == 'administrator' ? $role['name'] . ' (that\'s you!)' : $role['name']; ?>
												</option>
											<?php
											}
											?>
										</select>

										<?php // Needed for added spacing ?>
										&nbsp;

										<?php // Tells us whether to import items (default) or just start blank ?>
										<label for="import_items">
											<input type="checkbox" id="import_items" name="import_items" value="1"
											       checked/>
											Import role's existing menu items?
										</label>
									<?php endif; ?>
								</label>

								<div class="publishing-action">

									<?php
									// Outputs a toggle switch for quickly disabling / enabling the menu
									if ( $this->menu_ID ) {
										$this->toggle_switch(
											"cd_adminmenu_disabled_$this->menu_ID",
											'1',
											get_option( "cd_adminmenu_disabled_$this->menu_ID", '0' ),
											true,
											true,
											true,
											array(
												'title' => 'Temporarily disables this menu. The user with this role will get the default admin menu if this is set to off'
											)
										);
									}
									?>

									<?php
									$args = array(
										'id' => 'save_menu_header'
									);

									if ( $creating ) {
										$args['disabled'] = true;
									}
									submit_button(
										$this->menu_ID ? __( 'Save Menu' ) : __( 'Create Menu' ),
										'button-primary menu-save',
										'save_menu',
										false,
										$args
									);
									?>
								</div>
								<!-- END .publishing-action -->
							</div>
							<!-- END .major-publishing-actions -->
						</div>
						<!-- END .nav-menu-header -->
						<div id="post-body">
							<div id="post-body-content">

								<?php
								// Skip altogether and show a loading icon if loading the inital menu
								if ( $creating ) :
									?>

									<div class="creating-nav-menu">
										<p>The menu is being created. This may take some time.</p>

										<p><strong>Please do NOT leave this page.</strong></p>

										<p id="cd-creating-nav-menu-progress">0%</p>
									</div>

								<?php else : ?>
									<?php
									// If no menu ID is set, instruct to create a new menu from above
									if ( $this->menu_ID ) :
										?>
										<h3><?php _e( 'Menu Structure' ); ?></h3>

										<div class="drag-instructions post-body-plain"
											<?php echo isset( $menu_items ) && 0 == count( $menu_items ) ? 'style="display: none;"' : ''; ?>>
											<p>Drag and drop them in the order you like. Click on
												the arrows on each box to reveal more options.</p>
										</div>
										<?php
										if ( isset( $edit_markup ) && ! is_wp_error( $edit_markup ) ) {
											echo $edit_markup;
										} else {
											echo '<ul class="menu" id="menu-to-edit"></ul>';
										}
										?>
									<?php else : ?>
										<p class="post-body-plain">Select a role to create a menu for.</p>
										<p class="post-body-plain">The menu will be automatically populated with all
											visible admin menu items for the specified roles.</p>
									<?php endif; ?>

								<?php endif; ?>

							</div>
							<!-- /#post-body-content -->
						</div>
						<!-- /#post-body -->
						<div id="nav-menu-footer">
							<div class="major-publishing-actions">
								<?php if ( $this->menu_ID && ! $creating ) : ?>
									<span class="delete-action">
											<a class="submitdelete deletion menu-delete"
											   href="<?php echo esc_url( wp_nonce_url( add_query_arg( array(
												   'cd_delete_admin_menu' => $this->menu_ID,
												   admin_url()
											   ) ), 'delete-cd_nav_menu-' . $this->menu_ID ) ); ?>"><?php _e( 'Delete Menu' ); ?></a>
										</span><!-- END .delete-action -->
								<?php endif; ?>
								<div class="publishing-action">
									<?php
									$args = array(
										'id' => 'save_menu_footer'
									);

									if ( $creating ) {
										$args['disabled'] = true;
									}
									submit_button(
										$this->menu_ID ? __( 'Save Menu' ) : __( 'Create Menu' ),
										'button-primary menu-save',
										'save_menu',
										false,
										$args
									);
									?>
								</div>
								<!-- END .publishing-action -->
							</div>
							<!-- END .major-publishing-actions -->
						</div>
						<!-- /#nav-menu-footer -->
					</div>
					<!-- /.menu-edit -->
				</form>
				<!-- /#update-nav-menu -->
			</div>
			<!-- /#menu-management -->
		</div>
		<!-- /#menu-management-liquid -->
		</div><!-- /#nav-menus-frame -->
	<?php
	}
}