<?php

// TODO See line 265
// TODO Saving menus only works for Admin menu

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
class ClientDash_Core_Page_Settings_Tab_Admin_Menu extends ClientDash {

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

		// Create the nav menus via cron
		add_action( 'cd_create_nav_menu', array( $this, 'cd_create_nav_menu'), 10, 1 );

		// Get the original admin menus
		add_action( 'admin_menu', array( $this, 'get_orig_admin_menu' ), 99995 );

		// Create/Get CD nav menu
		add_action( 'admin_menu', array( $this, 'get_cd_nav_menus' ), 99996 );

		// Only remove and add if we have one ready to go
		if ( get_option( 'cd_modified_admin_menu', false ) ) {

			// Remove the original admin menu
			add_action( 'admin_menu', array( $this, 'remove_orig_admin_menu' ), 99997 );

			// Add the new, modified admin menu
			add_action( 'admin_menu', array( $this, 'add_modified_admin_menu' ), 99999 );
		}

		// Hide the CD nav menu from the normal nav menu page
		add_filter( 'wp_get_nav_menus', array( $this, 'hide_cd_nav_menu' ) );

		// Use custom walker menu for displaying sortable menu items
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'return_new_walker_menu' ), 10, 2 );

		// Add the content
		$this->add_content_section( array(
			'name'     => 'Core Admin Menu Settings',
			'page'     => 'Settings',
			'tab'      => 'Admin Menu',
			'callback' => array( $this, 'block_output' )
		) );

		// Anything in here will ONLY apply to this particular settings page
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'cd_settings'
		     && isset( $_GET['tab'] ) && $_GET['tab'] == 'admin_menu'
		) {

			// Required functions
			include_once( ABSPATH . '/wp-admin/includes/nav-menu.php' );

			// Includes callbacks
			include_once( $ClientDash->path . '/core/includes/adminmenu-availableitems-callbacks.php' );
			include_once( $ClientDash->path . '/core/includes/adminmenu-editmarkup-callbacks.php' );

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
	 * Gets the menu being currently edited.
	 *
	 * @since Client Dash 1.6
	 */
	public function get_current_menu() {

		// If a menu isn't set, just take the first one. Otherwise, get it from the url
		if ( isset( $_GET['menu'] ) ) {
			$this->menu_ID = $_GET['menu'];
		} else {
			$this->menu_ID = reset( $this->all_menu_IDs );
		}

		// If this menu doesn't exist, return false
		$menu_object = wp_get_nav_menu_object( $this->menu_ID );
		if ( ! $menu_object ) {
			$this->menu_ID = false;
		}
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
		foreach ( $roles as $role_name => $role ) {

			$menu_object = wp_get_nav_menu_object( "cd_admin_menu_$role_name" );

			// If it doesn't exist, either create it or just return false
			if ( ! $menu_object ) {

				// Only create the menus if told so
				if ( isset( $_POST['cd-create-admin-menus'] ) ) {

					// TODO This is taking up waaay too much memory. Figure out how on earth to sort this out.
					$this->create_cd_nav_menu( $role_name );

					continue;
				}

				$this->all_menu_IDs[ $role_name ] = false;

				continue;
			}

			$this->all_menu_IDs[ $role_name ] = $menu_object->term_id;
		}

		$this->get_current_menu();
	}

	public function create_cd_nav_menu( $role_name ) {
		$this->all_menu_IDs[ $role_name ] = wp_create_nav_menu( "cd_admin_menu_$role_name" );

		// Create the default items
		$this->create_modified_menu( $this->all_menu_IDs[ $role_name], $role_name );

		// Save it
		$this->save_cd_menu( "cd_admin_menu_$role_name" );
	}

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

		// When being loaded via AJAX, we won't have the menu ID, so we need
		// to get it again.
		if ( ! isset( $this->menu_ID ) ) {

			// TODO Make this only get the current menu
			$this->get_cd_nav_menus();

			// Includes our modified walker class for when ajax-actions.php tries to call it
			include_once( $ClientDash->path . '/core/includes/adminmenu-walkerclass.php' );
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

			if ( $menu->name == 'cd_admin_menu' ) {
				unset( $menus[ $key ] );
			}
		}

		return $menus;
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

		// Re-order the menus and submenus into an indexed order
		$i = 0;
		ksort( $orig_menu );
		foreach ( $orig_menu as $menu_position => $menu_item ) {

			unset( $orig_menu[ $menu_position ] );
			$orig_menu[ $i ] = $menu_item;

			// Loop through all of the sub-menus IF they exist
			if ( ! empty( $menu_item['submenus'] ) && is_array( $menu_item['submenus'] ) ) {
				$i_sub = 0;
				foreach ( $menu_item['submenus'] as $submenu_position => $submenu_item ) {

					unset( $orig_menu[ $i ]['submenus'][ $submenu_position ] );
					$orig_menu[ $i ]['submenus'][ $i_sub ] = $submenu_item;

					$i_sub ++;
				}
			}

			$i ++;
		}

		$this->original_admin_menu = $orig_menu;
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

		// Re-order the menus and submenus into an indexed order
		$i = 0;
		foreach ( $orig_menu as $menu_position => $menu_item ) {

			unset( $orig_menu[ $menu_position ] );
			$orig_menu[ $i ] = $menu_item;

			// Loop through all of the sub-menus IF they exist
			if ( ! empty( $menu_item['submenus'] ) && is_array( $menu_item['submenus'] ) ) {
				$i_sub = 0;
				foreach ( $menu_item['submenus'] as $submenu_position => $submenu_item ) {

					unset( $orig_menu[ $i ]['submenus'][ $submenu_position ] );
					$orig_menu[ $i ]['submenus'][ $i_sub ] = $submenu_item;

					$i_sub ++;
				}
			}

			$i ++;
		}

		return $orig_menu;
	}

	public function create_modified_menu( $menu_ID, $role ) {

		// Cycle through each item and create the nav menu accordingly
		foreach ( $this->original_admin_menu as $position => $menu ) {

			// Pass over if current role doesn't have the capabilities
			// TODO Make sure this works well with parent -> child relationships
			$role_info = get_role( $role );
			if ( ! array_key_exists( $menu['capability'], $role_info->capabilities ) ) {
				continue;
			}

			// Skip links
			// TODO Figure out how to better deal with this
			if ( $menu['menu_title'] == 'Links' ) {
				continue;
			}

			// Deal with "Plugins" having an extra space
			$menu['menu_title'] = trim( $menu['menu_title'] );

			$sorted      = self::sort_original_admin_menu( $menu );
			$args        = $sorted[0];
			$custom_meta = $sorted[1];

			$ID = wp_update_nav_menu_item( $menu_ID, 0, $args );

			if ( ! is_wp_error( $ID ) ) {
				foreach ( $custom_meta as $meta_name => $meta_value ) {
					update_post_meta( $ID, $meta_name, $meta_value );
				}
			}

			// If there are submenus, cycle through them
			if ( isset( $menu['submenus'] ) && ! empty( $menu['submenus'] ) ) {

				foreach ( $menu['submenus'] as $submenu_item ) {

					// TODO Fix/remove this
					if ( $submenu_item['menu_title'] == 'Client Dash' ) {
						continue;
					}

					$sorted      = self::sort_original_admin_menu( $submenu_item, $menu );
					$args        = $sorted[0];
					$custom_meta = $sorted[1];

					// Make it a child
					$args['menu-item-parent-id'] = $ID;

					$submenu_ID = wp_update_nav_menu_item( $menu_ID, 0, $args );

					if ( ! is_wp_error( $submenu_ID ) ) {
						foreach ( $custom_meta as $meta_name => $meta_value ) {
							update_post_meta( $submenu_ID, $meta_name, $meta_value );
						}
					}
				}
			}
		}
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
			'cd-original-title'   => $menu['menu_title'],
			'cd-icon'             => $menu['icon_url'],
			'cd-separator-height' => 5,
			'cd-url'              => $menu['menu_slug']
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
		           || ( $is_submenu && array_key_exists(
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
	}

	/**
	 * Adds the new, modified admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function add_modified_admin_menu() {
		global $menu;

		// TODO Change back to modified once modified is in place
		$modified_menu = get_option( 'cd_modified_admin_menu', false );

		// If the modified menus are set, then add them
		if ( $modified_menu ) {
			foreach ( $modified_menu as $menu_position => $menu_item ) {

				// If a separator, do that instead
				if ( strpos( $menu_item['menu_slug'], 'separator' ) !== false ) {
					$height                 = $menu_item['separator-height'];
					$menu[ $menu_position ] = array(
						'',
						'read',
						$menu_item['menu_slug'],
						'',
						"wp-menu-separator height-$height"
					);
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
				}

				// Now for the sub-menus (if they exist)
				if ( ! empty( $menu_item['submenus'] ) ) {
					foreach ( $menu_item['submenus'] as $submenu_item ) {
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
			}
		}
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

		// Update existing menu
		$_menu_object = wp_get_nav_menu_object( $this->menu_ID );

		$menu_title = trim( esc_html( $_POST['menu-name'] ) );

		if ( ! is_wp_error( $_menu_object ) ) {
			$_nav_menu_selected_id   = wp_update_nav_menu_object( $this->menu_ID, array( 'menu-name' => $menu_title ) );
			$_menu_object            = wp_get_nav_menu_object( $_nav_menu_selected_id );
			$nav_menu_selected_title = $_menu_object->name;
		}

		// Update menu items
		if ( ! is_wp_error( $_menu_object ) ) {

			// Default WP nav menu save
			wp_nav_menu_update_menu_items( $this->menu_ID, $nav_menu_selected_title );

			// Save CD friendly menu
			$this->save_cd_menu();
		}
	}

	public function save_cd_menu( $menu_name = false ) {

		// TODO Make sure this works with "Save Menu", otherwise, fix it

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

		$menu_items = wp_get_nav_menu_items( $menu_name ? $this->all_menu_IDs[ $menu_name ] : $this->menu_ID );

		// Cycle through all existing items
		foreach ( $menu_items as $item ) {

			// When saving with "Save Menu", we need to grab custom meta from $_POST
			// Otherwise, just grab it from the already saved post meta
			if ( isset( $_POST['action'] ) && $_POST['action'] == 'update' ) {
				$icon             = $_POST['menu-item-cd-icon'][ $item->ID ];
				$slug             = $_POST['menu-item-cd-url'][ $item->ID ];
				$separator_height = $_POST['menu-item-cd-separator-height'][ $item->ID ];
			} else {
				$icon             = get_post_meta( $item->ID, 'cd-icon', true );
				$slug             = get_post_meta( $item->ID, 'cd-url', true );
				$separator_height = get_post_meta( $item->ID, 'cd-separator-height', true );
			}

			if ( $item->menu_item_parent == '0' ) {
				$menu[ $item->ID ] = wp_parse_args( array(
					'menu_title' => $item->post_title,
					'menu_slug'  => $slug,
					// TODO Get page title
					'page_title' => '',
					'icon_url'   => $icon
				), $default_menu );

				// For the separator

				// TODO Get separator height to work without JS
				if ( $item->post_title == 'Separator' ) {
					$menu[ $item->ID ]['separator-height'] = $separator_height;
				}
			} else {
				$menu[ $item->menu_item_parent ]['submenus'][] = wp_parse_args( array(
					'menu_title'  => $item->post_title,
					'menu_slug'   => $slug,
					// TODO Get page title
					'page_title'  => '',
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

		update_option( "{$menu_name}_modified", $menu );
	}

	/**
	 * Returns the available menu items.
	 *
	 * @since Client Dash 1.6
	 *
	 * @return array|bool The menu items, if they exist, otherwise false.
	 */
	public function get_available_items() {

		global $wp_meta_boxes, $ClientDash;

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
						'add-custom-links' => array(
							'id'       => 'add-custom-links',
							'title'    => 'Custom',
							'callback' => 'wp_nav_menu_item_link_meta_box'
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
						)
					)
				)
			)
		);

		// Used by the do_accordion_sections()
		$wp_meta_boxes = $this->available_items;

		if ( is_nav_menu( $this->menu_ID ) ) {
			// Our modified walker class
			include_once( $ClientDash->path . '/core/includes/adminmenu-walkerclass.php' );

			$menu_items  = wp_get_nav_menu_items( $this->menu_ID, array( 'post_status' => 'any' ) );
			$edit_markup = wp_get_nav_menu_to_edit( $this->menu_ID );

			return array( $menu_items, $edit_markup );
		}

		return false;
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.6
	 */
	public function block_output() {

		global $ClientDash;

		$menu_info = $this->get_available_items();

		$menu_items  = $menu_info[0];
		$edit_markup = $menu_info[1];

		// TODO Make spinner on first load

		// If the menus have not yet been loaded
		if ( ! $this->menu_ID ) {
			?>
			<form action="" method="post">
				<input type="hidden" name="cd-create-admin-menus" value="1"/>

				<p>Client Dash offers the ability to customize the Admin Menu (that's the vertical menu on the left). If
					you would like to take advantage of this, click <input type="submit" class="button" value="here"/>
					to load in the corresponding menus. Please note that it will take some time.</p>
			</form>
			<?php

			// Don't show the rest of the markup because we haven't created a menu yet
			return;
		}

		// From wp-admin/nav-menus.php. Modified for CD use.
		?>
		<div class="manage-menus">
			<form method="get">
				<input type="hidden" name="action" value="edit" />
				<label for="menu" class="selected-menu"><?php _e( 'Select a menu to edit:' ); ?></label>
				<select name="menu" id="menu">
					<?php foreach ( $nav_menus as $nav_menu ) : ?>
						<option value="<?php echo esc_attr( $nav_menu->term_id ); ?>"
							<?php selected( $nav_menu->term_id, $this->menu_ID ); ?>>
							<?php echo esc_html( $nav_menu->truncated_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
				<span class="submit-btn">
					<input type="submit"
					       class="button-secondary"
					       value="<?php esc_attr_e( 'Select' ); ?>" />
				</span>
			</form>
		</div><!-- /manage-menus -->

		<div id="nav-menus-frame">
			<div id="menu-settings-column" class="metabox-holder">

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
			<div id="menu-management-liquid">
				<div id="menu-management">
					<form id="update-nav-menu" action="" method="post" enctype="multipart/form-data">
						<div class="menu-edit">
							<?php
							wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
							wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
							wp_nonce_field( 'update-nav_menu', 'update-nav-menu-nonce' );
							?>
							<input type="hidden" name="action" value="update"/>
							<input type="hidden" name="menu" id="menu"
							       value="<?php echo esc_attr( $this->menu_ID ); ?>"/>

							<div id="nav-menu-header">
								<div class="major-publishing-actions">
									<label class="menu-name-label howto open-label" for="menu-name">
										<span>Menu Name</span>
										<span>Admin Menu</span>
										<input name="menu-name" id="menu-name" type="hidden"
										       class="menu-name regular-text menu-item-textbox input-with-default-title"
										       value="cd_admin_menu"/>
									</label>

									<div class="publishing-action">
										<?php submit_button( __( 'Save Menu' ), 'button-primary menu-save', 'save_menu', false, array( 'id' => 'save_menu_header' ) ); ?>
									</div>
									<!-- END .publishing-action -->
								</div>
								<!-- END .major-publishing-actions -->
							</div>
							<!-- END .nav-menu-header -->
							<div id="post-body">
								<div id="post-body-content">
									<h3><?php _e( 'Menu Structure' ); ?></h3>

									<div class="drag-instructions post-body-plain"
									     <?php if (isset( $menu_items ) && 0 == count( $menu_items )) { ?>style="display: none;"<?php } ?>>
										<p>Drag and drop them in the order you like. Click on
											the arrows on each box to reveal more options.</p>
									</div>
									<?php
									if ( isset( $edit_markup ) && ! is_wp_error( $edit_markup ) ) {
										echo $edit_markup;
									} else {
										?>
										<ul class="menu" id="menu-to-edit"></ul>
									<?php } ?>
								</div>
								<!-- /#post-body-content -->
							</div>
							<!-- /#post-body -->
							<div id="nav-menu-footer">
								<div class="major-publishing-actions">
									<div class="publishing-action">
										<?php submit_button( __( 'Save Menu' ), 'button-primary menu-save', 'save_menu', false, array( 'id' => 'save_menu_header' ) ); ?>
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