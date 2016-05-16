<?php

/**
 * Class ClientDash_Core_Page_Settings_Tab_Menus
 *
 * Adds the core content section for Settings -> Admin Menu.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Menus
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

	/**
	 * Counts the total menu items. Used for when importing via AJAX.
	 *
	 * @since Client Dash 1.6
	 */
	public $total_menu_items;

	/**
	 * The ID's for all of the CD nav menus (one for each role).
	 *
	 * @since Client Dash 1.6
	 */
	public $all_menu_IDs;

	/**
	 * A list of available menu item options and their defaults. Only items placed here will actually
	 * be saved to the db.
	 *
	 * @since Client Dash 1.6
	 */
	public static $menu_item_defaults = array(
		'db-id'             => 0,
		'parent-id'         => 0,
		'position'          => 0,
		'title'             => '',
		'original-title'    => '',
		'url'               => '',
		'classes'           => '',
		//
		// Added by CD
		'cd-type'           => '',
		'cd-icon'           => '',
		'cd-page-title'     => '',
		'cd-submenu-parent' => '',
		'cd-params'         => '',
	);

	/**
	 * A list of all urls (from each menu item) that match the current page.
	 *
	 * @since Client Dash 1.6
	 */
	public $matching_urls = array();

	/**
	 * All WordPress core nav menu items (aside from post types).
	 *
	 * Unfortunately, there's no good way to get this dynamically BECAUSE I can't tell the
	 * difference between an item added by WP Core and an item added by a plugin. So my
	 * work-around is to define WP Core items and have the rest automatically fall into the
	 * plugin / theme category. This is fine, we just need to make sure this array is always
	 * up to date.
	 *
	 * @since Client Dash 1.6
	 */
	public static $wp_core = array(
		'Dashboard'  => array(
			'url'        => 'index.php',
			'icon'       => 'dashicons-dashboard',
			'capability' => 'read',
			'submenus'   => array(
				'Home'     => array(
					'url'        => 'index.php',
					'capability' => 'read',
				),
				'My Sites' => array(
					'url'        => 'my-sites.php',
					'capability' => 'read',
				),
				'Updates'  => array(
					'url'        => 'update-core.php',
					'capability' => 'update_core',
				),
			),
		),
		'Posts'      => array(
			'url'        => 'edit.php',
			'icon'       => 'dashicons-admin-post',
			'capability' => 'edit_posts',
			'submenus'   => array(
				'All Posts'  => array(
					'url'        => 'edit.php',
					'capability' => 'edit_posts',
				),
				'Add New'    => array(
					'url'        => 'post-new.php',
					'capability' => 'edit_posts',
				),
				'Categories' => array(
					'url'        => 'edit-tags.php?taxonomy=category',
					'capability' => 'manage_categories',
				),
				'Tags'       => array(
					'url'        => 'edit-tags.php?taxonomy=post_tag',
					'capability' => 'manage_categories',
				),
			)
		),
		'Media'      => array(
			'url'        => 'upload.php',
			'icon'       => 'dashicons-admin-media',
			'capability' => 'upload_files',
			'submenus'   => array(
				'Library' => array(
					'url'        => 'upload.php',
					'capability' => 'upload_files',
				),
				'Add New' => array(
					'url'        => 'media-new.php',
					'capability' => 'upload_files',
				),
			),
		),
		'Links'      => array(
			'url'        => 'link-manager.php',
			'icon'       => 'dashicons-admin-links',
			'capability' => 'manage_links',
			'submenus'   => array(
				'All Links' => array(
					'url'        => 'link-manager.php',
					'capability' => 'manage_links',
				),
				'Add New' => array(
					'url'        => 'link-add.php',
					'capability' => 'manage_links',
				),
				'Link Categories' => array(
					'url'        => 'edit-tags.php?taxonomy=link_category',
					'capability' => 'manage_categories',
				),
			),
		),
		'Pages'      => array(
			'url'        => 'edit.php?post_type=page',
			'icon'       => 'dashicons-admin-pages',
			'capability' => 'edit_pages',
			'submenus'   => array(
				'All Pages' => array(
					'url'        => 'edit.php?post_type=page',
					'capability' => 'edit_pages',
				),
				'Add New'   => array(
					'url'        => 'post-new.php?post_type=page',
					'capability' => 'edit_pages',
				),
			),
		),
		'Comments'   => array(
			'url'        => 'edit-comments.php',
			'icon'       => 'dashicons-admin-comments',
			'capability' => 'edit_posts',
			'submenus'   => array(
				'All Comments' => array(
					'url'        => 'index.php',
					'capability' => 'edit_posts',
				),
			),
		),
		'Appearance' => array(
			'url'        => 'themes.php',
			'icon'       => 'dashicons-admin-appearance',
			'capability' => 'switch_themes',
			'submenus'   => array(
				'Themes'    => array(
					'url'        => 'themes.php',
					'capability' => 'switch_themes',
				),
				'Customize' => array(
					'url'        => 'customize.php',
					'capability' => 'customize',
				),
				'Widgets'   => array(
					'url'        => 'widgets.php',
					'capability' => 'edit_theme_options',
				),
				'Menus'     => array(
					'url'        => 'nav-menus.php',
					'capability' => 'edit_theme_options',
				),
				'Editor'    => array(
					'url'        => 'theme-editor.php',
					'capability' => 'edit_files',
				),
			),
		),
		'Plugins'    => array(
			'url'        => 'plugins.php',
			'icon'       => 'dashicons-admin-plugins',
			'capability' => 'activate_plugins',
			'submenus'   => array(
				'Installed Plugins' => array(
					'url'        => 'plugins.php',
					'capability' => 'activate_plugins',
				),
				'Add New'           => array(
					'url'        => 'plugin-install.php',
					'capability' => 'install_plugins',
				),
				'Editor'            => array(
					'url'        => 'plugin-editor.php',
					'capability' => 'edit_files',
				),
			),
		),
		'Users'      => array(
			'url'        => 'users.php',
			'icon'       => 'dashicons-admin-users',
			'capability' => 'list_users',
			'submenus'   => array(
				'All Users'    => array(
					'url'        => 'users.php',
					'capability' => 'list_users',
				),
				'Add New'      => array(
					'url'        => 'user-new.php',
					'capability' => 'create_users',
				),
				'Your Profile' => array(
					'url'        => 'profile.php',
					'capability' => 'read',
				),
			),
		),
		'Tools'      => array(
			'url'        => 'tools.php',
			'icon'       => 'dashicons-admin-tools',
			'capability' => 'edit_posts',
			'submenus'   => array(
				'Available Tools' => array(
					'url'        => 'tools.php',
					'capability' => 'edit_posts',
				),
				'Import'          => array(
					'url'        => 'import.php',
					'capability' => 'import',
				),
				'Export'          => array(
					'url'        => 'export.php',
					'capability' => 'export',
				),
				'Delete Site'     => array(
					'url'        => 'ms-delete-site.php',
					'capability' => 'manage_options',
				),
			),
		),
		'Settings'   => array(
			'url'        => 'options-general.php',
			'icon'       => 'dashicons-admin-settings',
			'capability' => 'manage_options',
			'submenus'   => array(
				'General'    => array(
					'url'        => 'options-general.php',
					'capability' => 'manage_options',
				),
				'Writing'    => array(
					'url'        => 'options-writing.php',
					'capability' => 'manage_options',
				),
				'Reading'    => array(
					'url'        => 'options-reading.php',
					'capability' => 'manage_options',
				),
				'Discussion' => array(
					'url'        => 'options-discussion.php',
					'capability' => 'manage_options',
				),
				'Media'      => array(
					'url'        => 'options-media.php',
					'capability' => 'manage_options',
				),
				'Permalinks' => array(
					'url'        => 'options-permalink.php',
					'capability' => 'manage_options',
				),
			),
		),
	);

	/**
	 * These are what populate the side sortables area on the left.
	 *
	 * @since Client Dash 1.6
	 */
	public $side_sortables = array(
		'add-post-type'    => array(
			'id'       => 'add-post-types',
			'title'    => 'Post Types',
			'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'post_types' ),
		),
		'add-wp-core'      => array(
			'id'       => 'add-wp-core',
			'title'    => 'WordPress',
			'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'wp_core' ),
		),
		'add-plugin'       => array(
			'id'       => 'add-plugin',
			'title'    => 'Plugin / Theme',
			'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'plugin' ),
		),
		'add-cd-core'      => array(
			'id'       => 'add-cd-core',
			'title'    => 'Client Dash',
			'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'cd_core' ),
		),
		'add-custom-links' => array(
			'id'       => 'add-custom-links',
			'title'    => 'Custom Link',
			'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'custom_link' ),
		),
		'add-separator'    => array(
			'id'       => 'add-separator',
			'title'    => 'Separator',
			'callback' => array( 'CD_AdminMenu_AvailableItems_Callbacks', 'separator' ),
		),
	);

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.6
	 */
	function __construct() {

		$this->filter_data();

		global $ClientDash;

		// For when getting a specific role's admin menu
		if ( isset( $_POST['cd_role_menu_items'] ) ) {

			// Make WP think the current role is the one we're creating (and then reset it)
			add_action( 'init', array( $this, 'modify_role' ), 0.0001 );

			add_action( 'admin_menu', array( $this, 'get_orig_admin_menu' ), 999998 );

			add_action( 'admin_menu', array( $this, 'get_role_menu_items' ), 999999 );

			return;
		}

		// Get the original admin menus
		add_action( 'admin_menu', array( $this, 'get_orig_admin_menu' ), 99990 );

		// Delete the menu if told so from POST
		if ( isset( $_GET['cd_delete_admin_menu'] ) ) {
			add_action( 'admin_menu', array( $this, 'delete_nav_menu' ), 99995 );
		}

		// Create if told so from GET
		if ( isset( $_GET['cd_create_admin_menu'] ) ) {
			add_action( 'admin_menu', array( $this, 'create_nav_menu' ), 99996 );
		}

		// Create/Get CD nav menu
		add_action( 'admin_menu', array( $this, 'get_cd_nav_menus' ), 99990 );

		// Remove the original admin menu
		add_action( 'admin_head', array( $this, 'remove_orig_admin_menu' ), 99999 );

		// Add the modified menu (note the hook is different, this is because I'm calling the admin menu output again
		// and this is the action that fires immediately after the original menu is output)
		add_action( 'adminmenu', array( $this, 'add_modified_admin_menu' ), 99999 );

		// Hide the CD nav menu from the normal nav menu page
		add_filter( 'wp_get_nav_menus', array( $this, 'hide_cd_nav_menu' ) );

		// Use custom walker menu for displaying sortable menu items
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'return_new_walker_menu' ), 10, 2 );

		// Filters the modified menu item when returned
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'modify_menu_item' ) );

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
			add_filter( 'admin_body_class', array( $this, 'add_nav_menu_class' ), 99999 );

			// Save menus (only when updating)
			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' ) {
				add_action( 'admin_menu', array( $this, 'save_menu' ), 99998 );
			}

			// Make sure we include the WP nav menu script for our custom CD page
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_nav_menu' ) );
		}
	}

	/**
	 * Gets properties of the class.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $property The property to retrieve.
	 *
	 * @return mixed The property.
	 */
	public static function _get( $property ) {

		if ( property_exists( __CLASS__, $property ) ) {

			$vars = get_class_vars( __CLASS__ );

			return $vars[ $property ];
		}
	}

	/**
	 * Allows filtering of the object's properties.
	 *
	 * @since Client Dash 1.6
	 */
	private function filter_data() {

		/**
		 * Add to this array in order to associate more custom data with each nav menu item. Default
		 * menu item properties can be set here as well.
		 *
		 * @since Client Dash 1.6
		 */
		self::$menu_item_defaults = apply_filters( 'cd_nav_menu_item_defaults', self::$menu_item_defaults );

		/**
		 * Add or remove items from the CD default WP Core menu item list.
		 *
		 * @since Client Dash 1.6
		 */
		self::$wp_core = apply_filters( 'cd_nav_menu_wp_core_items', self::$wp_core );

		/**
		 * Allows extensions to add to the available side sortables.
		 *
		 * @since Client Dash 1.6.5
		 */
		$this->side_sortables = apply_filters( 'cd_menus_side_sortables', $this->side_sortables );
	}

	/**
	 * Adds the WP Core script for the nav menu page.
	 *
	 * @since Client Dash 1.6
	 */
	public function enqueue_nav_menu() {

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

	/**
	 * Get the original, un-modified admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function get_orig_admin_menu() {

		global $menu, $submenu, $wp_filter;

		// $menu
		// 0 => Menu Title, 1 => Capability, 2 => Slug, 3 => Page Title, 4 => Classes, 5 => Hookname, 6 => Icon

		// $submenu
		// 0 => Menu Title, 1 => Capability, 2 => Slug, 3 => Page Title

		foreach ( $menu as $menu_location => $menu_item ) {

			// Skip links IF the link manager is not enabled
			// Links are disabled as of WP 3.5, and only enabled with the presence of this filter being true. So if this
			// filter is set, we can pretty safely assume it's set to true.
			if ( $menu_item[0] == 'Links' && isset( $wp_filter['pre_option_link_manager_enabled'] ) ) {
				continue;
			}

			$menu_array = array(
				'menu_title' => isset( $menu_item[0] ) ? $menu_item[0] : null,
				'capability' => isset( $menu_item[1] ) ? $menu_item[1] : null,
				'menu_slug'  => isset( $menu_item[2] ) ? $menu_item[2] : null,
				'page_title' => isset( $menu_item[3] ) ? $menu_item[3] : null,
				'position'   => $menu_location,
				'hookname'   => isset( $menu_item[5] ) ? $menu_item[5] : null,
				'icon_url'   => isset( $menu_item[6] ) && $menu_item[6] != 'dashicons-admin-generic' ? $menu_item[6] : null
			);

			$orig_menu[ $menu_location ] = $menu_array;

			// Loop through all of the sub-menus IF they exist
			if ( ! empty( $submenu[ $menu_array['menu_slug'] ] ) && is_array( $submenu[ $menu_array['menu_slug'] ] ) ) {
				foreach ( $submenu[ $menu_array['menu_slug'] ] as $submenu_location => $submenu_item ) {

					$submenu_array = array(
						'menu_title'  => isset( $submenu_item[0] ) ? $submenu_item[0] : null,
						'capability'  => isset( $submenu_item[1] ) ? $submenu_item[1] : null,
						'menu_slug'   => isset( $submenu_item[2] ) ? $submenu_item[2] : null,
						'page_title'  => isset( $submenu_item[3] ) ? $submenu_item[3] : null,
						'parent_slug' => $menu_array['menu_slug']
					);

					$orig_menu[ $menu_location ]['submenus'][ $submenu_location ] = $submenu_array;
				}
			}
		}

		// Sort the menus, then re-index them by their position
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
	 * Used when importing a role's default admin menu items.
	 *
	 * Tricks WP into thinking the current user's role is whatever role we're currently
	 * importing admin menu items for. This way, the admin menu is properly populated with
	 * that role's default menu items. This function also loads in the menu.php file that
	 * creates the WP default admin menu (because it's not loaded during AJAX calls).
	 *
	 * @since Client Dash 1.6
	 */
	public function modify_role() {

		global $current_user, $wp_roles, $super_admins, $menu, $submenu;

		// Don't bother for admin
		if ( $_POST['cd_create_admin_menu'] == 'administrator' ) {
			return;
		}

		// If we're changing the role to something that's not an administrator, we need to make sure
		// that we make WP think the current user is NOT super admin, because that overrides all
		// capabilities
		if ( is_super_admin( $current_user->ID ) ) {
			$super_admins = array();
		}

		$new_role = $_POST['cd_create_admin_menu'];

		// Otherwise modify the current user object
		$current_user->allcaps  = $wp_roles->roles[ $new_role ]['capabilities'];
		$current_user->roles[0] = strtolower( $new_role );
		unset( $current_user->caps[ self::get_user_role() ] );
		$current_user->caps[ $new_role ] = true;
	}

	/**
	 * Now that the role has been modified to whatever role we're building a menu for, let's
	 * get store that role's menu items for populating the nav menu on the next page.
	 *
	 * @since Client Dash 1.6
	 */
	public function get_role_menu_items() {

		// Cycle through each item and create the nav menu accordingly
		foreach ( $this->original_admin_menu as $position => $menu ) {

			// Prepare AJAX data to send
			$menu_items[] = array(
				'menu_item'          => $menu,
				'menu_item_position' => $position
			);
		}

		set_transient( 'cd_role_menu_items', isset( $menu_items ) ? $menu_items : '', 60 );

		wp_redirect(
			add_query_arg(
				array(
					'cd_create_admin_menu' => $_POST['cd_create_admin_menu'],
					'import_items'         => $_POST['import_items'],
				),
				remove_query_arg( 'menu' )
			)
		);
		exit;
	}

	/**
	 * Get's the current role (that is, the role for which we are importing menu item's for) default
	 * admin menu and sends it back to AJAX.
	 *
	 * @since Client Dash 1.6
	 */
	public function get_role_admin_menu() {

		// Get the freshly populated admin menu
		$this->get_orig_admin_menu();

		// Cycle through each item and create the nav menu accordingly
		foreach ( $this->original_admin_menu as $position => $menu ) {

			// Prepare AJAX data to send
			$AJAX_output['menu_items'][] = array(
				'menu_item'          => $menu,
				'menu_item_position' => $position
			);
		}

		// Send the data back
		wp_send_json( isset( $AJAX_output ) ? $AJAX_output : array( 'no_items' => true ) );
	}

	/**
	 * Creates each role's nav menu for the first time.
	 *
	 * @since Client Dash 1.6
	 */
	public function create_nav_menu() {

		// The role name to create for
		$role_name = $_GET['cd_create_admin_menu'];

		// Bail if it exists (fail-safe)
		$menu_object = wp_get_nav_menu_object( "cd_admin_menu_$role_name" );
		if ( ! empty( $menu_object ) ) {
			wp_redirect( remove_query_arg( array( 'import_items', 'cd_create_admin_menu' ) ) );
			exit;
		}

		// Create the nav menu
		$this->menu_ID = wp_create_nav_menu( "cd_admin_menu_$role_name" );

		// Only import and save the existing menu items IF the checkbox is checked (default)
		if ( $_GET['import_items'] == '1' ) {

			// Now populate it with menu items (this is a hefty memory toll)
			$this->populate_nav_menu( $role_name );

			$this->get_current_menu();
		} else {

			// If creating a blank menu and an admin, make sure the CD page is there
			if ( $role_name == 'administrator' ) {
				$this->add_client_dash_menu_item();
			}

			wp_redirect( add_query_arg( 'menu', $this->menu_ID ) );
			exit();
		}
	}

	/**
	 * Adds the Client Dash menu item. Used when creating a blank menu for the admin.
	 *
	 * @since Client Dash 1.6
	 */
	public function add_client_dash_menu_item() {

		self::update_menu_item( $this->menu_ID, 0, array(
			'title'             => 'Client Dash',
			'url'               => 'cd_settings',
			'cd-type'           => 'cd_core',
			'cd-icon'           => $this->option_defaults['dashicon_settings'],
			'cd-submenu-parent' => 'options-general.php',
			'original-title'    => 'Client Dash ORIG',
		) );
	}

	/**
	 * Prepares AJAX data with information from the original admin menu to be sent off
	 * and create the new menu.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $role The role to create a menu for.
	 */
	public function populate_nav_menu( $role ) {

		global $ClientDash;

		$AJAX_output = array();

		$AJAX_output['menu_ID'] = $this->menu_ID;
		$AJAX_output['role']    = $role;
		$AJAX_output['total']   = $this->total_menu_items;

		// Build the URL to be sent back
		$AJAX_output['url'] = add_query_arg(
			'menu',
			$this->menu_ID,
			remove_query_arg( array( 'cd_create_admin_menu', 'import_items' ) )
		);

		$AJAX_output['menu_items'] = get_transient( 'cd_role_menu_items' );

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

		if ( ! function_exists( 'get_editable_roles' ) ) {
			return false;
		}

		// Get the nav menu for each role
		$roles    = get_editable_roles();
		$no_menus = true;
		foreach ( $roles as $role_ID => $role ) {

			$menu_object = wp_get_nav_menu_object( "cd_admin_menu_$role_ID" );

			// If it doesn't exist return false, otherwise return the menu ID
			if ( ! $menu_object ) {
				$this->all_menu_IDs[ $role_ID ] = false;
			} else {
				$no_menus                       = false;
				$this->all_menu_IDs[ $role_ID ] = $menu_object->term_id;
			}
		}

		// If no menus exist
		if ( $no_menus ) {
			$this->menu_ID = false;

			// If no menus exist and the url contains an int for a menu, redirect and remove the menu param
			if ( isset( $_GET['menu'] ) && ( strcspn( $_GET['menu'], '0123456789' ) != strlen( $_GET['menu'] ) ) ) {
				wp_redirect( remove_query_arg( 'menu' ) );
			}

			return;
		}

		$this->get_current_menu();
	}

	/**
	 * Gets the menu being currently edited. Also gets current role.
	 *
	 * @since Client Dash 1.6
	 */
	public function get_current_menu() {

		global $cd_current_menu_id, $cd_current_menu_role;

		// If creating, things are different
		if ( isset( $_GET['cd_create_admin_menu'] ) ) {
			$this->menu_ID = false;
			$this->role = $_GET['cd_create_admin_menu'];
			return;
		}

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

		// Globalize the menu ID and role
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

			// Globalize the menu ID and role
			$cd_current_menu_id   = $this->menu_ID;
			$cd_current_menu_role = $this->role;

			return;
		}

		// Now figure out which role this menu is for
		foreach ( $this->all_menu_IDs as $role => $menu ) {
			if ( $this->menu_ID == $menu ) {
				$this->role = $role;

				// Globalize the menu role
				$cd_current_menu_role = $role;

				return;
			}
		}
	}

	/**
	 * Filters the walker class used on the CD admin menu page.
	 *
	 * @return string The new Walker class.
	 */
	public function return_new_walker_menu( $walker, $menu ) {

		if ( ! $menu ) {
			return $walker;
		}

		// Get our active menus
		if ( ! isset( $this->all_menu_IDs ) || empty( $this->all_menu_IDs ) ) {
			$this->get_cd_nav_menus();
		}

		// If not a CD nav menu, get out of here!
		if ( ! in_array( $menu, $this->all_menu_IDs ) ) {
			return $walker;
		}

		// Needed to get the plugin path
		global $ClientDash;

		// Includes our modified walker class for when ajax-actions.php tries to call it
		include_once( $ClientDash->path . '/core/tabs/settings/menus/walkerclass.php' );

		if ( isset( $_POST['menu-item'] ) ) {
			$menu_item = reset( $_POST['menu-item'] );
		}

		if ( $this->menu_ID == $menu || isset( $menu_item['custom-meta-cd-type'] ) ) {

			/**
			 * Change this value to use a custom walker menu for the menu structure output.
			 *
			 * @since Client Dash 1.6
			 *
			 * @param int $menu_ID The current CD menu ID.
			 */
			return apply_filters( 'cd_nav_menu_walker', 'Walker_Nav_Menu_Edit_CD', $this->menu_ID );
		}

		return $walker;
	}

	/**
	 * The nav menu system uses a modified menu object (adding some params). Here I customize that further
	 * by adding some CD specific properties and removing properties that are no longer used (cuz why not?)
	 *
	 * @since Client Dash 1.6
	 *
	 * @param object $menu_item The old menu item.
	 *
	 * @return mixed The new menu item.
	 */
	public function modify_menu_item( $menu_item ) {

		if ( $menu_item->type != 'cd_nav_menu' ) {
			return $menu_item;
		}

		// Unset unnecessary properties
		$remove = array(
			'object',
			'target',
			'attr_title',
			'description',
			'xfn',
		);
		foreach ( $remove as $property ) {
			unset( $menu_item->$property );
		}

		// Add new properties
		$add = array(
			'original_title'    => get_post_meta( $menu_item->ID, '_menu_item_original_title', true ),
			'cd_type'           => get_post_meta( $menu_item->ID, '_menu_item_cd_type', true ),
			'cd_icon'           => get_post_meta( $menu_item->ID, '_menu_item_cd_icon', true ),
			'cd_page_title'     => get_post_meta( $menu_item->ID, '_menu_item_cd_page_title', true ),
			'cd_submenu_parent' => get_post_meta( $menu_item->ID, '_menu_item_cd_submenu_parent', true ),
			'cd_params'         => get_post_meta( $menu_item->ID, '_menu_item_cd_params', true ),
		);
		foreach ( $add as $property_name => $property ) {
			$menu_item->$property_name = $property;
		}

		// Return the modified menu item object
		return $menu_item;
	}

	/**
	 * Filters the returned nav menus on the nav menu edit screen to remove the CD admin nav menu from the list.
	 *
	 * @param array $menus The supplied available nav menus.
	 *
	 * @return mixed The filtered nav menus.
	 */
	public function hide_cd_nav_menu( $menus ) {

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

	/**
	 * Takes the given menu item and determines basic properties about it.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param array $menu The menu item to sort.
	 * @param bool|array $is_submenu Optional. The array of the PARENT menu item.
	 *
	 * @return array The sorted menu item properties.
	 */
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
			'title'         => $menu['menu_title'],
			'url'           => $menu['menu_slug'],
			'cd-icon'       => isset( $menu['icon_url'] ) ? $menu['icon_url'] : '',
			'cd-page-title' => $menu['page_title'],
		);

		// Figure out what we're dealing with
		if ( strpos( $menu['menu_slug'], 'separator' ) !== false ) {

			// Separator
			$args['title']   = 'Separator';
			$args['cd-type'] = 'separator';

		} elseif ( self::strposa( $menu['menu_slug'], array(
				'edit.php',
				'post-new.php',
				'media-new.php',
				'upload.php',
				'link_category'
			) ) !== false
		) {

			// Posts (and the weird link thing...)
			$args['cd-type'] = 'post_type';

		} elseif ( self::strposa( $menu['menu_slug'], array(
				'edit-tags.php'
			) ) !== false
		) {

			// Taxonomy
			$args['cd-type'] = 'taxonomy';

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
			$args['cd-type'] = 'wp_core';
		} elseif ( strpos( $menu['menu_slug'], 'cd_' ) !== false ) {

			// CD Core
			$args['cd-type'] = 'cd_core';
		} else {

			// The catchall for everything else (defaults to plugin)
			$args['cd-type'] = 'plugin';
		}

		return $args;
	}

	/**
	 * Determines if the current role's CD admin menu is active or not.
	 *
	 * @since Client Dash 1.6.4
	 *
	 * @return bool True if current CD admin menu is active, false otherwise.
	 */
	private function is_current_adminmenu_active() {

		// Don't replace the default admin menu if the current user's role does NOT have
		// a menu ready, OR if the menu is disabled, OR if it has no items
		$current_role = $this->get_user_role();
		$menu_items   = wp_get_nav_menu_items( $this->all_menu_IDs[ $current_role ] );
		if ( ! $this->all_menu_IDs[ $current_role ]
		     || get_option( "cd_adminmenu_disabled_{$this->all_menu_IDs[$current_role]}" )
		     || empty( $menu_items )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Removes the original admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function remove_orig_admin_menu() {

		if ( ! self::is_current_adminmenu_active() ) {
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
	}

	/**
	 * Adds the new, modified admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function add_modified_admin_menu() {

		global $menu, $submenu, $self, $pagenow, $submenu_file, $parent_file, $cd_parent_file, $_registered_pages, $plugin_page, $cd_submenu_file, $ClientDash, $admin_page_hooks;

		if ( ! self::is_current_adminmenu_active() ) {
			return;
		}

		// This is a strange little hack. When moving a sub-menu page to a top-level page, there are some
		// caveats. One being, WordPress doesn't know what the heck to do!... You will get a permissions
		// denied error without this line because of the hook names being different than normal. This simply
		// ensures that it will not be un-reachable.
		if ( ! empty( $plugin_page ) ) {
			$_registered_pages["admin_page_$plugin_page"] = true;
		}

		// Get current role
		$current_role = $this->get_user_role();

		// Get menu items and then index items by db ID
		$unsorted_menu_items = wp_get_nav_menu_items(
			$this->all_menu_IDs[ $current_role ],
			array(
				'orderby'    => 'ID',
				'output'     => ARRAY_A,
				'output_key' => 'ID',
			)
		);

		// Bail if no items
		if ( empty( $unsorted_menu_items ) ) {
			return;
		}

		$menu_items = array();

		foreach ( $unsorted_menu_items as $_item ) {
			$menu_items[ $_item->db_id ] = $_item;
		}

		// If menu not empty, cycle through all items and add them as either menus or sub-menus
		if ( ! empty( $menu_items ) && ! is_wp_error( $menu_items ) ) {
			foreach ( $menu_items as $dbID => $menu_item ) {

				// If this item is a CD Core page and isn't currently enabled for this role (no content), then
				// don't add it (and also remove it from the list for making sure none of its sub-menus load)
				if ( $menu_item->cd_type == 'cd_core'
				     && ! isset( $ClientDash->content_sections[ str_replace( 'cd_', '', $menu_item->url ) ] )
				) {
					unset( $menu_items[ $dbID ] );
					continue;
				}

				// If webmaster page, change the title
				if ( $menu_item->url == 'cd_webmaster' ) {
					$menu_item->title         = get_option( 'cd_webmaster_name', $ClientDash->option_defaults['webmaster_name'] );
					$menu_item->cd_page_title = get_option( 'cd_webmaster_name', $ClientDash->option_defaults['webmaster_name'] );
				}

				// If the comments page, get the html generated mimicked from WP core (/wp-admin/menus.php:~94)
				if ( $menu_item->title == 'Comments' ) {

					$awaiting_mod     = wp_count_comments();
					$awaiting_mod     = $awaiting_mod->moderated;
					$menu_item->title = sprintf( __( 'Comments %s' ), "<span class='awaiting-mod count-$awaiting_mod'><span class='pending-count'>" . number_format_i18n( $awaiting_mod ) . "</span></span>" );
				}

				if ( strpos( $menu_item->url, 'separator' ) !== false ) {

					// If a separator
					$menu[ $menu_item->menu_order ] = array(
						'',
						'read',
						$menu_item->url,
						'',
						'wp-menu-separator'
					);
				} elseif ( $menu_item->menu_item_parent == 0 ) {

					// If a parent

					// If this was originally a sub-menu, we need to fix the link (unless the slug is already
					// a hardlink)
					if ( strpos( $menu_item->url, '.php' ) === false
					     && ( $parent_slug = ! empty( $menu_item->cd_submenu_parent ) ? $menu_item->cd_submenu_parent : false )
					) {
						$menu_item->url = $parent_slug . ( strpos( $parent_slug, '?' ) !== false ? '&' : '?' ) . "page=$menu_item->url";
					}

					// If extra parameters are set, add them on
					if ( ! empty( $menu_item->cd_params ) ) {
						$menu_item->url .= $menu_item->cd_params;
					}

					// Allowed query params for when filtering the url
					$args = array(
						'page'      => true,
						'post_type' => true,
						'taxonomy'  => true
					);

					// If this page has added extra to the url
					if ( ! empty( $menu_item->cd_params ) ) {
						$params = explode( '&', $menu_item->cd_params );

						foreach ( $params as $param ) {
							if ( ! empty( $param ) ) {
								preg_match( '/.*(?==)/', $param, $matches );
								$args[ $matches[0] ] = true;
							}
						}
					}

					// Get the filtered url
					$url = $this->get_cleaned_url( $args );

					// If the url matches, add it to an array storing all matching urls
					if ( $url == $menu_item->url ) {
						$this->matching_urls[] = array(
							'parent'  => $menu_item->url,
							'submenu' => false,
						);
					}

					$hookname = get_plugin_page_hookname( $menu_item->url, '' );

					if ( empty( $menu_item->cd_icon ) ) {
						$icon_url = 'dashicons-admin-generic';
						$icon_class = 'menu-icon-generic ';
					} else {
						$icon_url = set_url_scheme( $menu_item->cd_icon );
						$icon_class = '';
					}

					$menu[ $menu_item->menu_order ] = array(
						$menu_item->title,
						'read',
						$menu_item->url,
						$menu_item->cd_page_title,
						'menu-top ' . $icon_class . $hookname . ' ' . esc_attr( implode( ' ', $menu_item->classes ) ),
						$hookname,
						$icon_url
					);

					// Here's the deal... When we add the menu page here, we've already done it once (from other plugins
					// and such), so the callbacks are already set. BUT, the callbacks are used by adding an action, the
					// action hookname is based off of the menu title and the menu slug. SO, it's okay that we don't add
					// the callback here again (because it's already been added before we removed it), but the problem is,
					// if we change the title then the hookname that WP is now looking for doesn't match the one that was
					// already created. SO, if the title was changed, we need to modify the global $admin_page_hooks so
					// that the action name matches the ORIGINAL title, not the new title. WHEW!
					if ( $menu_item->original_title != $menu_item->title ) {
						$admin_page_hooks[ $menu_item->url ] = strtolower( $menu_item->original_title );
					}
				} else {

					// If a sub-menu

					// If the parent has been unset, then don't add the sub-menu
					if ( ! isset( $menu_items[ (int) $menu_item->menu_item_parent ] ) ) {
						continue;
					}

					// If extra parameters are set, add them on
					if ( ! empty( $menu_item->cd_params ) ) {
						$menu_item->url .= $menu_item->cd_params;
					}

					// Allowed query params for when filtering the url
					$args = array(
						'page'      => true,
						'post_type' => true,
						'taxonomy'  => true
					);

					// If this page has added extra to the url
					if ( ! empty( $menu_item->cd_params ) ) {
						$params = explode( '&', $menu_item->cd_params );

						foreach ( $params as $param ) {
							if ( ! empty( $param ) ) {
								preg_match( '/.*(?==)/', $param, $matches );
								$args[ $matches[0] ] = true;
							}
						}
					}

					// Get the filtered url
					$url = $this->get_cleaned_url( $args );

					// If the url matches, add it to an array storing all matching urls
					if ( $url == $menu_item->url ) {
						$this->matching_urls[] = array(
							'parent'  => $menu_items[ (int) $menu_item->menu_item_parent ]->url,
							'submenu' => $menu_item->url,
						);
					}

					$submenu[ $menu_items[ $menu_item->menu_item_parent ]->url ][ $menu_item->menu_order ] = array(
						$menu_item->title,
						'read',
						$menu_item->url,
						$menu_item->cd_page_title,
					);
				}
			}
		}

		// Sort the menus and the sub-menus by array_key so they are in proper order
		ksort( $menu );
		foreach ( $submenu as $menu_parent => $sub_menus ) {
			ksort( $submenu[ $menu_parent ] );
		}

		// In the case of a sub-menu item being moved to a parent item, WordPress will be confused
		// about which menu item is active. So I compensate for this by overriding the "self" and
		// "parent_file" globals with the new (previously sub-menu) slug. This corrects the issue.

		// Get the most specific url (the biggest value). Only proceed if there is at least one matching url.
		if ( ! empty( $this->matching_urls ) ) {

			$url = max( $this->matching_urls );

			// Set the self (or what WP thinks we're viewing) to the ENTIRE slug, not just the parent.
			$self        = $url['parent'];
			$pagenow     = $url['parent'];
			$plugin_page = $url['parent'];
			$parent_file = $url['parent'];

			// Tell WP what our new submenu file is (because it's custom), otherwise, default to
			// the parent
			$submenu_file = ! empty( $url['submenu'] ) ? $url['submenu'] : $url['parent'];
		}

		// Instead of allowing WP to add its menu with this function, I've emptied the global $menu and $submenu variables
		// when this is initially called. And then IMMEDIATELY after there is a hook "adminmenu" that I call the exact
		// same function again, though I'm adding the 3rd param as false (normally true). This means that the parent
		// menu items do NOT have to link to where the first sub-menu item goes to, which is normaly functionality.
		// This is a private function, sorry WP!
		_wp_menu_output( $menu, $submenu, false );
	}

	/**
	 * Returns the current url without the WP base and stripped of all query args, except
	 * those specified.
	 *
	 * @since Client Dash 1.6
	 *
	 * @return string The filtered current url.
	 */
	public function get_cleaned_url( $allowed_args = null ) {
		// We compare the REQUEST_URI (minus all extra query args), but we allow the query args
		// that will be found within the slug
		$url = remove_query_arg( array_keys( array_diff_key( $_GET, $allowed_args ) ) );
		if ( $url === false ) {
			// If false, there were no extra query args, so just use the REQUEST_URI
			$url = $_SERVER['REQUEST_URI'];
		}

		// Filter out the WP base url (from wp-admin/menu-header.php:~16)
		$url = preg_replace( '|^.*/wp-admin/network/|i', '', $url );
		$url = preg_replace( '|^.*/wp-admin/|i', '', $url );
		$url = preg_replace( '|^.*/plugins/|i', '', $url );
		$url = preg_replace( '|^.*/mu-plugins/|i', '', $url );

		return $url;
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
	 * @since Client Dash 1.6
	 *
	 * @return mixed The parent file.
	 */
	public function modify_self() {
		global $self, $pagenow, $plugin_page, $cd_parent_file, $submenu_file, $cd_submenu_file;

		// Set the self (or what WP thinks we're viewing) to the ENTIRE slug, not just the parent.
		$self = $cd_parent_file;
		$pagenow = $cd_parent_file;
		$plugin_page = $cd_parent_file;

		// Tell WP what our new submenu file is (because it's custom), otherwise, default to
		// the parent
		$submenu_file = ! empty( $cd_submenu_file ) ? $cd_submenu_file : $cd_parent_file;

		return $cd_parent_file;
	}

	/**
	 * Adds the nav-menu body class (which is normally present on the nav-menus page and necessary).
	 *
	 * @since Client Dash 1.6
	 */
	public function add_nav_menu_class( $classes ) {
		return $classes . ' nav-menus-php cd-nav-menu';
	}

	/**
	 * Saves the admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function save_menu() {

		// Remove the transient so it resets
		delete_transient( "cd_adminmenu_output_$this->menu_ID" );

		// Update the disabled option
		if ( isset( $_POST["cd_adminmenu_disabled_$this->menu_ID"] ) ) {
			update_option( "cd_adminmenu_disabled_$this->menu_ID", '1' );
		} else {
			delete_option( "cd_adminmenu_disabled_$this->menu_ID" );
		}

		// Save the menu items
		$this->update_menu_items( $this->menu_ID );
	}

	/**
	 * Replaces wp_save_nav_menu_items() (wp-admin/includes/nav-menu.php:~1045).
	 *
	 * @param int $menu_id
	 * @param array $menu_data
	 *
	 * @return array
	 */
	public static function save_menu_items( $menu_id = 0, $menu_data = array() ) {

		// Initialize some vars
		$menu_id     = (int) $menu_id;
		$items_saved = array();

		// Loop through all the menu items' POST values.
		foreach ( (array) $menu_data as $_possible_db_id => $_item_object_data ) {

			// If this possible menu item doesn't actually have a menu database ID yet.
			$_actual_db_id = 0;

			// Setup args from POST data, or use default
			foreach ( self::$menu_item_defaults as $field => $default ) {
				$args[ $field ] = isset( $_item_object_data["menu-item-$field"] ) ? $_item_object_data["menu-item-$field"] : $default;
			}

			// Update that crap
			$items_saved[] = self::update_menu_item( $menu_id, $_actual_db_id, $args );
		}

		return $items_saved;
	}

	/**
	 * Saves the current nav menu.
	 *
	 * Taken and modified for Client Dash needs from wp-admin/includes/nav-menu.php:~1257
	 *
	 * @param int $menu_ID The ID of the nav menu to save to.
	 *
	 * @since Client Dash 1.6
	 */
	public static function update_menu_items( $menu_ID ) {

		// Get our old menu items
		$unsorted_menu_items = wp_get_nav_menu_items(
			$menu_ID,
			array(
				'orderby'    => 'ID',
				'output'     => ARRAY_A,
				'output_key' => 'ID'
			)
		);

		// Index menu items by db ID
		$menu_items = array();
		foreach ( $unsorted_menu_items as $_item ) {
			$menu_items[ $_item->db_id ] = $_item;
		}

		wp_defer_term_counting( true );

		// Loop through all the menu items' POST variables
		if ( ! empty( $_POST['menu-item-db-id'] ) ) {
			foreach ( (array) $_POST['menu-item-db-id'] as $db_ID ) {

				// Setup args
				$args = array();
				foreach ( self::$menu_item_defaults as $field => $default ) {

					// Skip some static properties
					if ( in_array( $field, array(
						'original-title',
						'cd-type',
						'cd-page-title',
						'cd-submenu-parent',
						'db-id',
					) ) ) {
						continue;
					}

					$args[ $field ] = isset( $_POST["menu-item-$field"][ $db_ID ] ) ? $_POST["menu-item-$field"][ $db_ID ] : '';
				}

				$menu_item_db_id = self::update_menu_item( $menu_ID, $_POST['menu-item-db-id'][ $db_ID ] != $db_ID ? 0 : $db_ID, $args );

				// Saved it, now remove it (for comparison later) and move on
				unset( $menu_items[ $menu_item_db_id ] );
			}
		}

		// Remove menu items from the menu that weren't in $_POST
		if ( ! empty( $menu_items ) ) {
			foreach ( array_keys( $menu_items ) as $menu_item_id ) {
				if ( is_nav_menu_item( $menu_item_id ) ) {
					wp_delete_post( $menu_item_id );
				}
			}
		}

		wp_defer_term_counting( false );
	}

	/**
	 * Save's a nav menu item.
	 *
	 * Taken and modified for Client Dash needs from wp-includes/nav-menu.php:~311
	 *
	 * @since Client Dash 1.6
	 *
	 * @param int $menu_ID The menu to save to.
	 * @param int $menu_item_db_id The existing nav menu item db ID.
	 * @param array $menu_item_data Args sent from the POST save.
	 *
	 * @return int|WP_Error The menu item's db ID on success. WP_Error on failure.
	 */
	public static function update_menu_item( $menu_ID, $menu_item_db_id = 0, $menu_item_data = array() ) {

		// If item db ID is 0, it's a new item, otherwise we're updating
		$update = 0 != $menu_item_db_id;

		// Defaults
		$defaults          = self::$menu_item_defaults;
		$defaults['db-id'] = $menu_item_db_id;

		$args = wp_parse_args( $menu_item_data, $defaults );

		// Some defaults for when creating
		if ( ! $update && empty( $args['original-title'] ) ) {
			$args['original-title'] = $args['title'];
		}

		// Get the position of the end of the menu. Used for when adding items via AJAX
		if ( 0 == (int) $args['position'] ) {
			$menu_items       = (array) wp_get_nav_menu_items( $menu_ID, array( 'post_status' => 'publish,draft' ) );
			$last_item        = array_pop( $menu_items );
			$args['position'] = ( $last_item && isset( $last_item->menu_order ) ) ? 1 + $last_item->menu_order : count( $menu_items );
		}

		$original_parent = 0 < $menu_item_db_id ? get_post_field( 'post_parent', $menu_item_db_id ) : 0;

		// Populate the menu item object
		$post = array(
			'menu_order'  => $args['position'],
			'ping_status' => 0,
			'post_parent' => $original_parent,
			'post_title'  => $args['title'],
			'post_type'   => 'nav_menu_item',
			'post_status' => 'publish',
		);

		// Create the new post item
		if ( ! $update ) {
			unset( $post['ID'] );
			$menu_item_db_id = wp_insert_post( $post, true );
			$menu_item_db_id = (int) $menu_item_db_id;

			// Set all of the post meta
			update_post_meta( $menu_item_db_id, '_menu_item_type', 'cd_nav_menu' );
			update_post_meta( $menu_item_db_id, '_menu_item_original_title', $args['original-title'] );
			update_post_meta( $menu_item_db_id, '_menu_item_object', 'custom' );
			update_post_meta( $menu_item_db_id, '_menu_item_object_id', strval( (int) $menu_item_db_id ) );
			update_post_meta( $menu_item_db_id, '_menu_item_cd_type', $args['cd-type'] );
			update_post_meta( $menu_item_db_id, '_menu_item_cd_page_title', $args['cd-page-title'] );
			update_post_meta( $menu_item_db_id, '_menu_item_cd_submenu_parent', $args['cd-submenu-parent'] );
			update_post_meta( $menu_item_db_id, '_menu_item_menu_item_parent', strval( (int) $args['parent-id'] ) );
			update_post_meta( $menu_item_db_id, '_menu_item_url', $args['url'] );
			update_post_meta( $menu_item_db_id, '_menu_item_cd_icon', $args['cd-icon'] );
			update_post_meta( $menu_item_db_id, '_menu_item_cd_params', $args['cd-params'] );
			update_post_meta( $menu_item_db_id, '_menu_item_classes', $args['classes'] );
		} else {

			// Else update it
			$post['ID']          = $menu_item_db_id;
			$post['post_status'] = 'publish';
			wp_update_post( $post );

			// Update specific post meta
			update_post_meta( $menu_item_db_id, '_menu_item_menu_item_parent', strval( (int) $args['parent-id'] ) );
			update_post_meta( $menu_item_db_id, '_menu_item_url', $args['url'] );
			update_post_meta( $menu_item_db_id, '_menu_item_cd_params', $args['cd-params'] );
			update_post_meta( $menu_item_db_id, '_menu_item_classes', $args['classes'] );
			update_post_meta( $menu_item_db_id, '_menu_item_cd_icon', $args['cd-icon'] );
		}

		/**
		 * Fires immediately after all nav menu item data is updated. Use this if you want to update
		 * more custom data.
		 *
		 * @since Client Dash 1.6
		 */
		do_action( 'cd_nav_menu_update_item' );

		// Associate the menu item with the menu term
		// Only set the menu term if it isn't set to avoid unnecessary wp_get_object_terms()
		if ( ! $update || ! is_object_in_term( $menu_item_db_id, 'nav_menu', $menu_ID ) ) {
			wp_set_object_terms( $menu_item_db_id, array( (int) $menu_ID ), 'nav_menu' );
		}

		return $menu_item_db_id;
	}

	/**
	 * Populates the global $wp_meta_boxes variable which is used when populating the
	 * side sortables area (on the left).
	 *
	 * @since Client Dash 1.6
	 */
	public function populate_side_sortables() {

		global $wp_meta_boxes;

		/**
		 * This is what populates the side sortables on the nav menu screen.
		 *
		 * This is what do_accordion_sections() uses to populate the side sortables section. Each
		 * array key isn't important, but the 3 params are. The ID must be unique, the title is what
		 * the users will see, and the callback is a function to output the content of the block.
		 *
		 * @since Client Dash 1.6
		 *
		 * @param int $this ->menu_ID The currently being edited menu ID (false if no menu).
		 */
		$side_sortables = apply_filters( 'cd_nav_menu_side_sortables', $this->side_sortables, $this->menu_ID );

		$wp_meta_boxes = array(
			'nav-menus' => array(
				'side' => array(
					'default' => $side_sortables
				)
			)
		);
	}

	/**
	 * Returns the available menu items.
	 *
	 * @since Client Dash 1.6
	 *
	 * @return array|bool The menu items, if they exist, otherwise false.
	 */
	public function get_current_menu_items() {

		global $ClientDash, $errors;

		/**
		 * Whether or not to use transients in getting the menu output. (big time saver)
		 *
		 * @since Client Dash 1.6
		 */
		$use_transients = apply_filters( 'cd_nav_menu_transients', true );

		// Save the information in a transient and get it for faster page loads
		// Only use a transient when debugging is off
		if ( ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) && $use_transients ) {
			$output = get_transient( "cd_adminmenu_output_$this->menu_ID" );
		} else {
			$output = false;
		}
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

			// Save the transient data if not disabled
			if ( $use_transients ) {
				set_transient( "cd_adminmenu_output_$this->menu_ID", $output, DAY_IN_SECONDS );
			}
		}

		return $output;
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.6
	 */
	public function block_output() {

		// Populate the side sortables area
		$this->populate_side_sortables();

		// If we're creating a menu via AJAX currently
		$creating = isset( $_GET['cd_create_admin_menu'] ) ? true : false;

		// Skip all this garbage if we're creating
		if ( ! $creating ) {

			// Get our menu items!
			$menu_info = $this->get_current_menu_items();

			$edit_markup = $menu_info['edit_markup'];
			$errors      = $menu_info['errors'];
			$menu_items  = $menu_info['menu_items'];

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
		}

		// Get the role info (for name)
		$role_name = ucwords( str_replace( '_', ' ', $this->role ) );

		// From wp-admin/nav-menus.php. Modified for CD use.
		?>

		<?php
		// Only show select area if a menu has been created. Otherwise, this will be shown below
		if ( $this->menu_ID && ! $creating ) :
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

			<div class="clear"></div>

			<?php
			// Only show if menus exist
			if ( $this->menu_ID ) :
				?>
				<div id="cd-nav-menu-statuses" class="accordion-container">
					<div class="control-section accordion-section  open add-post-types" id="add-post-types">
						<h3 class="accordion-section-title">
							Menu Statuses
						</h3>

						<div class="accordion-section-content ">
							<div class="inside">
								<table class="cd-nav-menu-statuses-table">
									<tr>
										<th>Menu</th>
										<th>Active</th>
									</tr>
									<?php
									// Cycle through all role menus and show them
									foreach ( $this->all_menu_IDs as $role => $menu_ID ) {

										// Skip if no menu ID present
										if ( ! $menu_ID ) {
											continue;
										}

										$on_off = get_option( 'cd_adminmenu_disabled_' . $this->all_menu_IDs[ $role ], false ) ? 'off' : 'on';

										?>
										<tr>
											<td>
												<?php echo $this->translate_id_to_name( $role ); ?>
											</td>
											<td>
												<span class="cd-nav-menu-status <?php echo $on_off; ?>"></span>
											</td>
										</tr>
									<?php
									}
									?>
								</table>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

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
							if ( $this->menu_ID || $creating ) :
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

								<input type="hidden" name="cd_role_menu_items" value="1"/>

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
									<input type="hidden" name="import_items" value="0"/>
									<input type="checkbox" id="import_items" name="import_items" value="1"
									       checked/>
									Import role's existing menu items?
								</label>
							<?php endif; ?>
						</label>

						<div class="publishing-action">

							<?php
							// Outputs a toggle switch for quickly disabling / enabling the menu
							if ( $this->menu_ID && ! $creating ) {
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

								<div class="cd-progress-bar">
									<div class="cd-progress-bar-inner"></div>
									<span class="cd-progress-bar-percent">0%</span>
								</div>
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