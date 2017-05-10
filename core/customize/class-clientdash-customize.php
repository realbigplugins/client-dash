<?php
/**
 * The Client Dash Admin Customizer.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Class ClientDash_Customize
 *
 * The Client Dash Admin Customizer.
 *
 * @since {{VERSION}}
 */
class ClientDash_Customize {

	/**
	 * ClientDash_Customize constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		if ( self::is_customizing() ) {

			add_action( 'template_redirect', array( $this, 'load' ), 0 );

			add_filter( 'show_admin_bar', '__return_false' );

			add_action( 'cd_customize_header', array( $this, 'template_header' ) );
			add_action( 'cd_customize_body', array( $this, 'template_body' ) );
			add_action( 'cd_customize_footer', array( $this, 'template_footer' ) );
		}

		// If in the customizer, modify the role
		if ( isset( $_GET['cd_customizing'] ) ) {

			add_action( 'set_current_user', array( $this, 'modify_current_user' ), 99999 );
			add_action( 'admin_enqueue_scripts', array( $this, 'preview_scripts' ), 1 );
		}

		// Save role settings on first role load
		if ( isset( $_GET['cd_save_role'] ) ) {

			add_filter( 'custom_menu_order', array(
				$this,
				'save_menu_preview'
			), 99998 ); // Priority just before modifying

			add_filter( 'wp_dashboard_widgets', array(
				$this,
				'save_dashboard_preview'
			), 99998 ); // Priority just before modifying

			add_filter( 'cd_core_pages', array(
				$this,
				'save_cd_pages'
			), 99998 ); // Priority just before modifying
		}
	}

	/**
	 * Tells if Client Dash is in the customize view.
	 *
	 * @since {{VERSION}}
	 *
	 * @return bool True if customizing, false otherwise.
	 */
	public static function is_customizing() {

		return isset( $_REQUEST['clientdash_customize'] ) && $_REQUEST['clientdash_customize'] == '1';
	}

	/**
	 * Loads the customizer.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function load() {

		add_action( 'template_redirect', array( $this, 'unload_wordpress' ), 9999 );
		add_action( 'cd_customize_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		nocache_headers();
	}

	/**
	 * Prevents WordPress from loading the frontend so that we can load our customizer.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function unload_wordpress() {

		// Override Theme
		add_filter( 'template_include', array( $this, 'inject_template' ), 999 );

		// Remove ALL actions to strip 3rd party plugins and unwanted WP functions
		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_head_scripts' );

		// Add back WP native actions that we need
		add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
		add_action( 'wp_head', 'wp_print_styles', 8 );
		add_action( 'wp_head', 'wp_print_head_scripts', 9 );

		// Strip all scripts and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'strip_enqueues' ), 999999 );

		// Footer
		remove_all_actions( 'wp_footer' );

		add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );
		add_action( 'wp_footer', 'wp_admin_bar_render', 1000 );

		// Add some more custom actions
		add_action( 'wp_footer', array( $this, 'localize_data' ), 1 );
	}

	/**
	 * Remove all enqueue actions as early as possible.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	public function strip_enqueues() {

		global $wp_scripts, $wp_styles;

		$wp_scripts->queue = array();
		$wp_styles->queue  = array();

		do_action( 'cd_customize_enqueue_scripts' );
	}

	/**
	 * Reset the style and script registries in case anything is still registered
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	public function reset_enqueues() {

		global $wp_styles;
		global $wp_scripts;

		$wp_styles  = new WP_Styles();
		$wp_scripts = new WP_Scripts();

		do_action( 'wp_enqueue_scripts_clean' );
	}

	/**
	 * Enqueues the Customizer assets.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function enqueue_assets() {

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'clientdash-fontawesome' );
		wp_enqueue_style( 'clientdash-customize' );
		wp_enqueue_script( 'clientdash-customize' );
	}

	/**
	 * Localizes data for JS.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function localize_data() {

		global $menu, $submenu;

		if ( ! function_exists( 'get_editable_roles' ) ) {

			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$_roles = get_editable_roles();
		$roles  = array();

		foreach ( $_roles as $role_ID => $role ) {

			$roles[] = array(
				'text'  => $role['name'],
				'value' => $role_ID,
			);
		}

		wp_localize_script( 'clientdash-customize', 'ClientdashCustomize_Data', array(
			'roles'     => $roles,
			'adminurl'  => admin_url(),
			'domain'    => get_bloginfo( 'url' ),
			'dashicons' => json_decode( file_get_contents( CLIENTDASH_DIR . 'core/dashicons.json' ) ),
			'cd_pages'  => ClientDash_Core_Pages::get_pages(),
			'api_nonce' => wp_create_nonce( 'wp_rest' ),
			'l10n'      => array(
				'role_switcher_label'               => __( 'Modifying for:', 'clientdash' ),
				'panel_text_menu'                   => __( 'Menu', 'clientdash' ),
				'panel_text_dashboard'              => __( 'Dashboard', 'clientdash' ),
				'panel_text_cd_pages'               => __( 'Pages', 'clientdash' ),
				'panel_actions_title_menu'          => __( 'Editing: Menu', 'clientdash' ),
				'panel_actions_title_submenu'       => __( 'Editing: Sub-Menu', 'clientdash' ),
				'panel_actions_title_menu_add'      => __( 'Adding: Menu Items', 'clientdash' ),
				'panel_actions_title_submenu_add'   => __( 'Adding: Sub-Menu Items', 'clientdash' ),
				'panel_actions_title_dashboard'     => __( 'Editing: Dashboard', 'clientdash' ),
				'panel_actions_title_dashboard_add' => __( 'Adding: Widgets', 'clientdash' ),
				'panel_actions_title_cdpages'       => __( 'Editing: Pages', 'clientdash' ),
				'panel_actions_title_cdpages_add'   => __( 'Adding: Pages', 'clientdash' ),
				'action_button_back'                => __( 'Back', 'clientdash' ),
				'action_button_add_items'           => __( 'Add Items', 'clientdash' ),
				'show_controls'                     => __( 'Show Controls', 'clientdash' ),
				'title'                             => __( 'Title', 'clientdash' ),
				'original_title'                    => __( 'Original title:', 'clientdash' ),
				'original_icon'                     => __( 'Original icon:', 'clientdash' ),
				'icon'                              => __( 'Icon', 'clientdash' ),
				'link'                              => __( 'Link', 'clientdash' ),
				'no_items_added'                    => __( 'No items added yet. Click the add items "+" button to add your first item.', 'clientdash' ),
				'no_items_available'                => __( 'No items available.', 'clientdash' ),
				'separator'                         => __( 'Separator', 'clientdash' ),
				'custom_link'                       => __( 'Custom Link', 'clientdash' ),
				'click_to_move'                     => __( 'Click to move', 'clientdash' ),
				'edit'                              => __( 'Edit', 'clientdash' ),
				'edit_submenu'                      => __( 'Edit submenu', 'clientdash' ),
				'delete'                            => __( 'Delete', 'clientdash' ),
				'leave_confirmation'                => __( 'Are you sure you want to leave? Any unsaved changes will be lost.', 'clientdash' ),
				'save'                              => __( 'Save', 'clientdash' ),
				'saved'                             => __( 'Changes saved and live!', 'clientdash' ),
				'role_reset'                        => __( 'Role successfully reset!', 'clientdash' ),
				'close'                             => __( 'Close', 'clientdash' ),
				'cancel'                            => __( 'Cancel', 'clientdash' ),
				'confirm'                           => __( 'Confirm', 'clientdash' ),
				'none'                              => __( 'None', 'clientdash' ),
				'reset_role'                        => __( 'Reset Role', 'clientdash' ),
				'up_do_date'                        => __( 'Up to date', 'clientdash' ),
				'confirm_role_reset'                => __( 'Are you sure you want to reset all customizations for this role? This can not be undone.', 'clientdash' ),
				'cannot_submit_form'                => __( 'Preview only. Cannot do that. Sorry!', 'clientdash' ),
				'cannot_view_link'                  => __( 'Only administrative links can be viewed.', 'clientdash' ),
				'current_location'                  => __( 'Current location', 'clientdash' ),
				'toplevel'                          => __( 'Top Level', 'clientdash' ),
			),
		) );
	}

	/**
	 * Loads up the templates.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function inject_template() {

		/**
		 * Loads the customize header.
		 *
		 * @since {{VERSION}}
		 *
		 * @hooked ClientDash_Customize->template_header() 10
		 */
		do_action( 'cd_customize_header' );

		/**
		 * Loads the customize body.
		 *
		 * @since {{VERSION}}
		 *
		 * @hooked ClientDash_Customize->template_body() 10
		 */
		do_action( 'cd_customize_body' );

		/**
		 * Loads the customize footer.
		 *
		 * @since {{VERSION}}
		 *
		 * @hooked ClientDash_Customize->template_footer() 10
		 */
		do_action( 'cd_customize_footer' );
	}

	/**
	 * Loads the header template.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function template_header() {

		include_once CLIENTDASH_DIR . 'core/customize/views/customize-header.php';
	}

	/**
	 * Loads the customize body.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function template_body() {

		include_once CLIENTDASH_DIR . 'core/customize/views/customize-body.php';
	}

	/**
	 * Loads the footer template.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function template_footer() {

		include_once CLIENTDASH_DIR . 'core/customize/views/customize-footer.php';
	}

	/**
	 * Retrieves and stores the current role.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @return bool|null|WP_Role
	 */
	private function get_role() {

		static $role = false;

		if ( $role === false ) {

			$role = get_role( $_GET['role'] );
		}

		if ( $role === null ) {

			return false;
		}

		return $role;
	}

	/**
	 * Modifies the current user to set the role.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function modify_current_user() {

		global $current_user;

		if ( ! ( $role = $this->get_role() ) ) {

			return;
		}

		$current_user->roles   = array( $role->name );
		$current_user->caps    = array( $role->name => true );
		$current_user->allcaps = $role->capabilities;
	}

	/**
	 * Loads scripts and styles for inside the preview iframe.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function preview_scripts() {

		wp_enqueue_script( 'clientdash-customize-inpreview' );
		wp_enqueue_style( 'clientdash-customize-inpreview' );
	}

	/**
	 * Initially saves a role's menu preview for the customizer.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function save_menu_preview( $bool ) {

		global $menu, $submenu;

		$role = $_GET['role'];

		$customizations = cd_get_customizations( $role );

		// Get original menu merged with customizations
		ksort( $menu );

		$save_menu       = array();
		$save_menu_new   = array();
		$customized_menu = isset( $customizations['menu'] ) ? $customizations['menu'] : array();

		foreach ( $menu as $menu_item ) {

			$customized_menu_item_key = cd_array_get_index_by_key( $customized_menu, 'id', $menu_item[2] );
			$customized_menu_item     = $customized_menu_item_key !== false ?
				$customized_menu[ $customized_menu_item_key ] : false;

			$type = 'menu_item';

			if ( strpos( $menu_item[4], 'wp-menu-separator' ) !== false ) {

				$type = 'separator';
			}

			if ( $menu_item[2] == 'clientdash' ) {

				$type = 'clientdash';
			}

			if ( $customized_menu_item ) {

				$save_menu[ $customized_menu_item_key ] = array(
					'id'             => $menu_item[2],
					'title'          => $customized_menu_item['title'],
					'original_title' => $menu_item[0],
					'icon'           => $customized_menu_item['icon'],
					'original_icon'  => isset( $menu_item[6] ) ? $menu_item[6] : '',
					'deleted'        => $customized_menu_item['deleted'],
					'type'           => $customized_menu_item['type'],
				);

			} else {

				$save_menu_new[] = array(
					'id'             => $menu_item[2],
					'title'          => '',
					'original_title' => $menu_item[0],
					'icon'           => '',
					'original_icon'  => isset( $menu_item[6] ) ? $menu_item[6] : '',
					'deleted'        => false,
					'type'           => $type,
				);
			}
		}

		ksort( $save_menu );
		$save_menu = array_merge( $save_menu, $save_menu_new );

		// Get original submenu merged with customizations
		$save_submenu       = array();
		$customized_submenu = isset( $customizations['submenu'] ) ? $customizations['submenu'] : array();

		foreach ( $submenu as $menu_slug => $submenu_items ) {

			ksort( $submenu_items );

			$save_submenu[ $menu_slug ] = array();

			foreach ( $submenu_items as $submenu_item ) {

				if ( isset( $customized_submenu[ $menu_slug ] ) ) {

					$customized_submenu_item = cd_array_search_by_key( $customized_submenu[ $menu_slug ], 'id', $submenu_item[2] );
					$customized_submenu_item = $customized_submenu_item ? $customized_submenu_item : array();

				} else {

					$customized_submenu_item = array();
				}

				$type = 'submenu_item';

				if ( cd_is_core_page( $submenu_item[2] ) ) {

					$type = 'cd_page';
				}

				$save_submenu[ $menu_slug ][] = wp_parse_args( $customized_submenu_item, array(
					'id'             => $submenu_item[2],
					'title'          => '',
					'original_title' => $submenu_item[0],
					'deleted'        => false,
					'type'           => $type,
				) );
			}
		}

		// Set current role menu
		cd_update_role_customizations( "preview_$role", array(
			'menu'    => $save_menu,
			'submenu' => $save_submenu,
		) );

		return $bool;
	}

	/**
	 * Initially saves a role's dashboard preview.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param array $dashboard_widgets
	 *
	 * @return array
	 */
	function save_dashboard_preview( $dashboard_widgets ) {

		global $wp_meta_boxes;

		$role = $_GET['role'];

		$customizations = cd_get_customizations( $role );

		$save_dashboard       = array();
		$customized_dashboard = isset( $customizations['dashboard'] ) ? $customizations['dashboard'] : array();

		if ( isset( $wp_meta_boxes['dashboard'] ) ) {

			foreach ( $wp_meta_boxes['dashboard'] as $priorities ) {

				foreach ( $priorities as $widgets ) {

					foreach ( $widgets as $widget ) {

						$customized_widget = cd_array_search_by_key( $customized_dashboard, 'id', $widget['id'] );

						if ( $customized_widget ) {

							$save_dashboard[] = wp_parse_args( $customized_widget, array(
								'id'             => $widget['id'],
								'title'          => '',
								'original_title' => $widget['title'],
								'deleted'        => false,
							) );

						} else {

							$save_dashboard[] = array(
								'id'             => $widget['id'],
								'title'          => '',
								'original_title' => $widget['title'],
								'deleted'        => false,
							);
						}
					}
				}
			}
		}

		// Set current role dashboard
		cd_update_role_customizations( "preview_$role", array(
			'dashboard' => $save_dashboard,
		) );

		return $dashboard_widgets;
	}

	/**
	 * Initially save's the cd core pages.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param array $pages
	 *
	 * @return array
	 */
	function save_cd_pages( $pages ) {

		$role = $_GET['role'];

		$customizations = cd_get_customizations( $role );

		$save_pages = $pages;

		if ( $customizations['cdpages'] ) {

			foreach ( $save_pages as $i => $page ) {

				$custom_page = cd_array_search_by_key( $customizations['cdpages'], 'id', $page['id'] );

				$save_pages[ $i ] = wp_parse_args( $custom_page, $page );
			}
		}

		// Set current role cd core pages
		cd_update_role_customizations( "preview_$role", array(
			'cdpages' => $save_pages,
		) );

		return $pages;
	}
}