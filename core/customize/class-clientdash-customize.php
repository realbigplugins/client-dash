<?php
/**
 * The Client Dash Admin Customizer.
 *
 * @since 2.0.0
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
 * @since 2.0.0
 */
class ClientDash_Customize {

	/**
	 * ClientDash_Customize constructor.
	 *
	 * @since 2.0.0
	 */
	function __construct() {

		add_action( 'rest_api_init', array( $this, 'rest_add_fields' ) );

		if ( self::is_customizing() ) {

			add_action( 'template_redirect', array( $this, 'load' ), 0 );

			add_filter( 'show_admin_bar', '__return_false', 999 );

			add_action( 'cd_customize_header', array( $this, 'template_header' ) );
			add_action( 'cd_customize_body', array( $this, 'template_body' ) );
			add_action( 'cd_customize_footer', array( $this, 'template_footer' ) );
		}

		// If in the customizer, modify the role
		if ( self::in_customizer() ) {

			add_action( 'set_current_user', array( $this, 'modify_current_user' ), 99999 );
			add_action( 'admin_enqueue_scripts', array( $this, 'preview_scripts' ), 1 );
		}

		// Save role settings on first role load
		if ( self::is_saving_role() ) {

			add_filter( 'custom_menu_order', array(
				$this,
				'save_menu_preview'
			), 99998 ); // Priority just after modifying

			add_filter( 'wp_dashboard_widgets', array(
				$this,
				'save_dashboard_preview'
			), 100000 ); // Priority just after modifying
		}
	}

	/**
	 * Tells if Client Dash is in the customize view.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if customizing, false otherwise.
	 */
	public static function is_customizing() {

		return isset( $_REQUEST['clientdash_customize'] ) && $_REQUEST['clientdash_customize'] == '1';
	}

	/**
	 * Tells if the current page IS the customizer preview.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if in customizer, false otherwise.
	 */
	public static function in_customizer() {

		return isset( $_REQUEST['cd_customizing'] ) && $_REQUEST['cd_customizing'] == '1';
	}

	/**
	 * Tells if current in customizer window should save current role settings.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if should save role, false otherwise.
	 */
	public static function is_saving_role() {

		return self::in_customizer() && isset( $_REQUEST['cd_save_role'] ) && $_REQUEST['cd_save_role'] == '1';
	}

	/**
	 * Loads the customizer.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function load() {

		add_action( 'template_redirect', array( $this, 'unload_wordpress' ), 9998 );
		add_action( 'template_redirect', array( $this, 'load_actions' ), 9999 );
		add_action( 'cd_customize_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		nocache_headers();
	}

	/**
	 * Prevents WordPress from loading the frontend so that we can load our customizer.
	 *
	 * @since 2.0.0
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
	 * @since 2.0.0
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
	 * @since 2.0.0
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
	 * Loads actions after WordPress has been unloaded.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function load_actions() {

		add_action( 'wp_head', array( $this, 'title_tag' ) );
	}

	/**
	 * Enqueues the Customizer assets.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function enqueue_assets() {

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'clientdash-customize' );
		wp_enqueue_script( 'clientdash-customize' );
	}

	/**
	 * Localizes data for JS.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function localize_data() {

		global $menu, $submenu, $wp_roles;

		$roles  = array();

		foreach ( $wp_roles->role_names as $role_ID => $role_name ) {

			$roles[] = array(
				'text'  => $role_name,
				'value' => $role_ID,
			);
		}

		wp_localize_script( 'clientdash-customize', 'ClientdashCustomize_Data', array(
			'roles'           => $roles,
			'adminurl'        => admin_url(),
			'domain'          => get_bloginfo( 'url' ),
			'dashicons'       => json_decode( file_get_contents( CLIENTDASH_DIR . 'core/dashicons.json' ) ),
			'api_nonce'       => wp_create_nonce( 'wp_rest' ),
			'current_user_id' => get_current_user_id(),
			'load_tutorial'   => get_user_meta( get_current_user_id(), 'clientdash_hide_customize_tutorial', true ) !== 'yes',
			'tutorial_panels' => $this->get_tutorial_panels(),
			'widgets'         => self::get_custom_dashboard_widgets(),
			'l10n'            => array(
				'role_switcher_label'               => __( 'Customizing:', 'client-dash' ),
				'panel_text_menu'                   => __( 'Menu', 'client-dash' ),
				'panel_text_dashboard'              => __( 'Dashboard', 'client-dash' ),
				'panel_text_cd_pages'               => __( 'Pages', 'client-dash' ),
				'panel_actions_title_menu'          => __( 'Editing: Menu', 'client-dash' ),
				'panel_actions_title_submenu'       => __( 'Editing: Sub-Menu', 'client-dash' ),
				'panel_actions_title_menu_add'      => __( 'Adding: Menu Items', 'client-dash' ),
				'panel_actions_title_submenu_add'   => __( 'Adding: Sub-Menu Items', 'client-dash' ),
				'panel_actions_title_dashboard'     => __( 'Editing: Dashboard', 'client-dash' ),
				'panel_actions_title_dashboard_add' => __( 'Adding: Widgets', 'client-dash' ),
				'panel_actions_title_cdpages'       => __( 'Editing: Pages', 'client-dash' ),
				'panel_actions_title_cdpages_add'   => __( 'Adding: Pages', 'client-dash' ),
				'action_button_back'                => __( 'Back', 'client-dash' ),
				'action_button_add_items'           => __( 'Add Items', 'client-dash' ),
				'choose_something_to_customize'     => __( 'Choose something to customize.', 'client-dash' ),
				'yes_understand'                    => __( 'Yes, I understand.', 'client-dash' ),
				'nevermind'                         => __( 'Nevermind.', 'client-dash' ),
				'show_controls'                     => __( 'Show Controls', 'client-dash' ),
				'title'                             => __( 'Title', 'client-dash' ),
				'original_title'                    => __( 'Original title:', 'client-dash' ),
				'original_icon'                     => __( 'Original icon:', 'client-dash' ),
				'icon'                              => __( 'Icon', 'client-dash' ),
				'link'                              => __( 'Link', 'client-dash' ),
				'no_items_added'                    => __( 'No items added yet. Click the "Add Items" button to add your first item.', 'client-dash' ),
				'no_items_available'                => __( 'No items available.', 'client-dash' ),
				'separator'                         => __( 'Separator', 'client-dash' ),
				'custom_link'                       => __( 'Custom Link', 'client-dash' ),
				'click_to_move'                     => __( 'Click to move', 'client-dash' ),
				'edit'                              => __( 'Edit', 'client-dash' ),
				'edit_submenu'                      => __( 'Edit submenu', 'client-dash' ),
				'submenu'                           => __( 'Submenu', 'client-dash' ),
				'delete'                            => __( 'Delete', 'client-dash' ),
				'leave_confirmation'                => __( 'Are you sure you want to leave? Any unsaved changes will be lost.', 'client-dash' ),
				'save'                              => __( 'Save', 'client-dash' ),
				'saved'                             => __( 'Saved', 'client-dash' ),
				'saved_and_live'                    => __( 'Changes saved and live!', 'client-dash' ),
				'role_reset'                        => __( 'Role successfully reset!', 'client-dash' ),
				'close'                             => __( 'Close', 'client-dash' ),
				'cancel'                            => __( 'Cancel', 'client-dash' ),
				'confirm'                           => __( 'Confirm', 'client-dash' ),
				'none'                              => __( 'None', 'client-dash' ),
				'reset_role'                        => __( 'Reset Role', 'client-dash' ),
				'up_do_date'                        => __( 'Up to date', 'client-dash' ),
				'confirm_role_reset'                => __( 'Are you sure you want to reset all customizations for this role? This can not be undone.', 'client-dash' ),
				'cannot_submit_form'                => __( 'Preview only. Cannot do that. Sorry!', 'client-dash' ),
				'cannot_view_link'                  => __( 'Only administrative links can be viewed.', 'client-dash' ),
				'current_location'                  => __( 'Current location', 'client-dash' ),
				'toplevel'                          => __( 'Top Level', 'client-dash' ),
				'new_items'                         => __( 'New Items', 'client-dash' ),
				'new'                               => __( 'New', 'client-dash' ),
				'next'                              => __( 'Next', 'client-dash' ),
				'previous'                          => __( 'Previous', 'client-dash' ),
				'finish'                            => __( 'Finish', 'client-dash' ),
				'missing'                           => __( 'Missing', 'client-dash' ),
			),
		) );
	}

	/**
	 * Adds custom REST API fields.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function rest_add_fields() {

		register_rest_field( 'user', 'clientdash_hide_customize_tutorial', array(
			'update_callback' => array( $this, 'rest_update_user_field' ),
			'schema'          => array(
				'context' => array(
					'edit',
				),
			),
		) );
	}

	/**
	 * Updates a user meta field.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param mixed $value The value of the field
	 * @param object $object The object from the response
	 * @param string $field_name Name of field
	 *
	 * @return mixed
	 */
	function rest_update_user_field( $value, $object, $field_name ) {

		return update_user_meta( $object->ID, $field_name, strip_tags( $value ) );
	}

	/**
	 * Extra dashboard widgets.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	public static function get_custom_dashboard_widgets() {

		/**
		 * Extra, re-usable dashboard widgets.
		 *
		 * @since 2.0.0
		 */
		$widgets = apply_filters( 'cd_customize_dashboard_widgets', array(
			array(
				'id'       => 'text',
				'label'    => __( 'Text', 'client-dash' ),
				'settings' => array(
					array(
						'name'  => 'text',
						'label' => __( 'Text or HTML', 'client-dash' ),
						'type'  => 'textarea',
					),
				),
				'callback' => 'clientdash_custom_widget_text',
			),
		) );

		return $widgets;
	}

	/**
	 * Callback for displaying custom widgets.
	 *
	 * @since 2.0.0
	 *
	 * @param array $object
	 * @param array $box
	 */
	public static function custom_widget_callback( $object, $box ) {

		$custom_widgets = ClientDash_Customize::get_custom_dashboard_widgets();

		foreach ( $custom_widgets as $custom_widget ) {

			if ( $box['args']['type'] === $custom_widget['id'] ) {

				$settings = isset( $box['args']['settings'] ) ? $box['args']['settings'] : array();
				$output   = call_user_func( $custom_widget['callback'], $settings, $box['args'] );

				/**
				 * Output for a custom widget.
				 *
				 * @since 2.0.0
				 *
				 * @param string $output Widget output.
				 * @param array $args Current widget args.
				 * @param array $custom_widget Custom widget global args.
				 *
				 * @return string Widget output.
				 */
				$output = apply_filters(
					"clientdash_custom_widget_{$custom_widget['id']}_output",
					$output,
					$box['args'],
					$custom_widget
				);

				echo $output;
			}
		}
	}

	/**
	 * Gets the tutorial panels.
	 *
	 * Each panel should have a unique key as the ID, a title, and an array of content where each item is a new line.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function get_tutorial_panels() {

		$panels = array(
			'intro'             => array(
				'title'        => __( 'Welcome.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'This tutorial will quickly walk you through how to use the Client Dash Customize Admin tool.', 'client-dash' ),
					),
					array(
						'text' => __( 'The Customize Admin tool is designed to help you customize the "Admin Experience" for each role on this website. You can change how each role views their "Admin Menu" and "Dashboard" in order to give them the best possible experience.', 'client-dash' ),
					),
					array(
						'text'    => __( 'You can click the "X" to the right of this pop-up at any point to close this tutorial. Once you finish or close this tutorial, it will not show again. If you would like to view it again, please visit the Client Dash Settings page to turn it back on.', 'client-dash' ),
						'classes' => 'footnote',
					),
				),
				'editor_panel' => 'primary',
			),
			'editor'            => array(
				'title'        => __( 'The Editor.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'The Editor is the pane on the left side of the screen where all of the Customize controls are. This is where you can make all customizations.', 'client-dash' ),
					),
					array(
						'text' => __( 'Any edits you make will appear live in the Preview Screen, which appears to the right of the Editor (behind this tutorial).', 'client-dash' ),
					),
					array(
						'text' => __( 'To get started editing, you can select any of the items in the list.', 'client-dash' ),
					),
				),
				'highlights'   => array(
					array(
						'selector' => 'cd-editor-panels',
						'position' => 'right',
						'size'     => 'large',
					),
				),
				'editor_panel' => 'primary',
			),
			'close_save'        => array(
				'title'        => __( 'Close/Save.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'None of your changes will be saved for the role you are editing until you click "Save". You will be warned before leaving the page if you have any unsaved changes.', 'client-dash' ),
					),
					array(
						'text' => __( 'Click the "X" to exit the Customize tool.', 'client-dash' ),
					),
				),
				'highlights'   => array(
					array(
						'selector' => 'cd-editor-primary-actions',
						'position' => 'bottom',
					),
				),
				'editor_panel' => 'primary',
			),
			'role_switcher'     => array(
				'title'        => __( 'Role Switcher.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'By default you will be editing the Admin Screen for the "Administrator" role. You can change which role you are editing the experience for by using the "Role Switcher" drop-down.', 'client-dash' ),
					),
					array(
						'text' => __( 'All changes you make will only apply to the role you are editing.', 'client-dash' ),
					),
				),
				'highlights'   => array(
					array(
						'selector' => 'cd-editor-role-switcher',
						'position' => 'top',
					),
				),
				'editor_panel' => 'primary',
			),
			'hide_editor'       => array(
				'title'        => __( 'Hide the Editor.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'You can hide the Editor by clicking the arrow button. Click the button again to un-hide the Editor.', 'client-dash' ),
					),
				),
				'highlights'   => array(
					array(
						'selector' => 'cd-editor-hide',
						'position' => 'top',
					),
				),
				'editor_panel' => 'primary',
			),
			'menu'              => array(
				'title'        => __( 'The Menu.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'The Menu editor is what you use to edit the Admin Menu (in other words, the tall menu on the left side of the screen).', 'client-dash' ),
					),
					array(
						'text' => __( 'You can drag and drop items, delete them, add any new items, rename them, and more!', 'client-dash' ),
					),
					array(
						'text' => __( 'To edit each sub-menu, click on the sub-menu edit button that appears when editing a menu item. Once there, you can edit the sub-menu in the same way you edit the menu.', 'client-dash' ),
					),
					array(
						'text'    => __( 'IMPORTANT: Once you save customizations for a role\'s menu, no menu items will be added automatically in the future. You will need to come back here and add any new items (which will be highlighted as "New"). Normally activating new plugins or themes might add menu items, but now you will need to add them manually for any roles with customizations.', 'client-dash' ),
						'classes' => 'footnote',
					),
				),
				'highlights'   => array(
					array(
						'selector' => 'cd-editor-panels',
						'position' => 'right',
						'size'     => 'large',
					),
				),
				'editor_panel' => 'menu',
			),
			'secondary_actions' => array(
				'title'        => __( 'Action Buttons.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'When editing the Menu or the Dashboard, you can go back to the previous panel with the back arrow.', 'client-dash' ),
					),
					array(
						'text' => __( 'You can add items by clicking the "Add Items" button.', 'client-dash' ),
					),
				),
				'highlights'   => array(
					array(
						'selector' => 'cd-editor-sub-header',
						'position' => 'bottom',
					),
				),
				'editor_panel' => 'menu',
			),
			'dashboard'         => array(
				'title'        => __( 'The Dashboard.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'The Dashboard editor is what you use to edit the Dashboard (the primary screen users see when logging into the website).', 'client-dash' ),
					),
					array(
						'text' => __( 'Each "box" on the Dashboard is called a "Dashboard Widget", and each item in this list represents a widget. You can delete them, add new ones, or edit the title of them.', 'client-dash' ),
					),
					array(
						'text'    => __( 'IMPORTANT: Once you save customizations for a role\'s dashboard, no widgets will be added automatically in the future. You will need to come back here and add any new items (which will be highlighted as "New"). Normally activating new plugins or themes might add widgets, but now you will need to add them manually for any roles with customizations.', 'client-dash' ),
						'classes' => 'footnote',
					),
				),
				'highlights'   => array(
					array(
						'selector' => 'cd-editor-panels',
						'position' => 'right',
						'size'     => 'large',
					),
				),
				'editor_panel' => 'dashboard',
			),
			'reset_role'        => array(
				'title'        => __( 'Reset a Role.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'If you want to completely erase all customizations for the selected role, click the "Reset role customizations" button. This will reset the role customizations back to the original, un-modified state.', 'client-dash' ),
					),
					array(
						'text'    => __( 'WARNING: Deleting a role\'s customizations cannot be undone.', 'client-dash' ),
						'classes' => 'footnote',
					),
				),
				'highlights'   => array(
					array(
						'selector' => 'cd-editor-panels',
						'position' => 'right',
						'size'     => 'large',
					),
				),
				'editor_panel' => 'primary',
			),
			'finish'            => array(
				'title'        => __( 'That\'s All.', 'client-dash' ),
				'content'      => array(
					array(
						'text' => __( 'Hopefully you found this useful!', 'client-dash' ),
					),
					array(
						'text' => __( 'If you need any more help or information for Client Dash, be sure to check out our documentation.', 'client-dash' ),
					),
					array(
						'type' => 'link',
						'text' => __( 'View Documentation', 'client-dash' ),
						'link' => 'https://realbigplugins.com/docs/client-dash/',
					),
					array(
						'text' => __( 'Click the "Finish" button to end this tutorial.', 'client-dash' ),
					),
				),
				'editor_panel' => 'primary',
			),
		);

		/**
		 * The Customize tutorial panels.
		 *
		 * @since 2.0.0
		 */
		$panels = apply_filters( 'cd_customize_tutorial_panels', $panels );

		return $panels;
	}

	/**
	 * Loads up the templates.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function inject_template() {

		/**
		 * Loads the customize header.
		 *
		 * @since 2.0.0
		 *
		 * @hooked ClientDash_Customize->template_header() 10
		 */
		do_action( 'cd_customize_header' );

		/**
		 * Loads the customize body.
		 *
		 * @since 2.0.0
		 *
		 * @hooked ClientDash_Customize->template_body() 10
		 */
		do_action( 'cd_customize_body' );

		/**
		 * Loads the customize footer.
		 *
		 * @since 2.0.0
		 *
		 * @hooked ClientDash_Customize->template_footer() 10
		 */
		do_action( 'cd_customize_footer' );
	}

	/**
	 * Outputs the Customize title tag.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function title_tag() {

		/* translators: Customize page title */
		$title = __( 'Customize Admin', 'client-dash' );

		/**
		 * The main page title tag for the Customize tool.
		 *
		 * @since 2.0.0
		 */
		$title = apply_filters( 'cd_customize_page_title', $title );

		echo '<title>' . esc_attr( $title ) . ': ' . get_bloginfo( 'title' ) . '</title>';
	}

	/**
	 * Loads the header template.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function template_header() {

		include_once CLIENTDASH_DIR . 'core/customize/views/customize-header.php';
	}

	/**
	 * Loads the customize body.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function template_body() {

		include_once CLIENTDASH_DIR . 'core/customize/views/customize-body.php';
	}

	/**
	 * Loads the footer template.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function template_footer() {

		include_once CLIENTDASH_DIR . 'core/customize/views/customize-footer.php';
	}

	/**
	 * Retrieves and stores the current role.
	 *
	 * @since 2.0.0
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
	 * @since 2.0.0
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
	 * @since 2.0.0
	 * @access private
	 */
	function preview_scripts() {

		wp_enqueue_script( 'clientdash-customize-inpreview' );
		wp_enqueue_style( 'clientdash-customize-inpreview' );
	}

	/**
	 * Initially saves a role's menu preview for the customizer.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function save_menu_preview( $bool ) {

		global $menu, $submenu;

		$role = $_GET['role'];

		$customizations = cd_get_customizations( $role );

		if ( $customizations && ! empty( $customizations['menu'] ) ) {

			$customized_menu = $customizations['menu'];
			$save_menu       = $customized_menu;

			// Set all to "missing" by default
			foreach ( $save_menu as $i => $menu_item ) {

				$save_menu[ $i ]['missing'] = true;
			}

		} else {

			$save_menu = array();
		}

		foreach ( $menu as $menu_item ) {

			$customized_menu_item_key = cd_array_get_index_by_key( $save_menu, 'id', $menu_item[2] );

			if ( $customized_menu_item_key !== false ) {

				$save_menu[ $customized_menu_item_key ]['missing'] = false;
				continue;
			}

			$type = 'menu_item';

			if ( strpos( $menu_item[4], 'wp-menu-separator' ) !== false ) {

				// If we already have a customized menu, don't add new separators.
				if ( isset( $customized_menu ) ) {
					continue;
				}

				$type = 'separator';
			}

			if ( $menu_item[2] == 'clientdash' ) {

				$type = 'clientdash';
			}

			// If menu was previously customized, and this item does not exist in that customized menu, and it isn't
			// the client dash item, we can assume it is new.
			$new = ( isset( $customized_menu ) && $type !== 'clientdash' ) || false;

			$save_menu[] = array(
				'id'             => $menu_item[2],
				'title'          => '',
				'original_title' => $menu_item[0],
				'icon'           => '',
				'original_icon'  => isset( $menu_item[6] ) ? $menu_item[6] : '',
				'type'           => $type,
				'deleted'        => $new,
				'new'            => $new,
			);
		}

		foreach ( $save_menu as $i => $save_menu_item ) {
			$save_menu[ $i ] = $this->process_menu_item( $save_menu_item );
		}

		ksort( $save_menu );

		if ( $customizations && ! empty( $customizations['submenu'] ) ) {

			$customized_submenu = $customizations['submenu'];
			$save_submenu       = $customized_submenu;

			// Set all to "missing" by default
			foreach ( $save_submenu as $i => $submenu_item ) {

				$save_submenu[ $i ]['missing'] = true;
			}

		} else {

			$save_submenu = array();
		}

		foreach ( $submenu as $menu_ID => $submenu_items ) {

			foreach ( $submenu_items as $i => $submenu_item ) {

				if ( isset( $save_submenu[ $menu_ID ] ) ) {

					$customized_submenu_item_key = cd_array_get_index_by_key( $save_submenu[ $menu_ID ], 'id', $submenu_item[2] );

					if ( $customized_submenu_item_key !== false ) {

						$save_submenu[ $customized_submenu_item_key ]['missing'] = false;
						continue;
					}
				}

				$type = 'menu_item';

				// If menu was previously customized, and this item does not exist in that customized menu, and it isn't
				// the client dash item, we can assume it is new.
				$new = isset( $customized_submenu );

				$save_submenu[ $menu_ID ][] = array(
					'id'             => $submenu_item[2],
					'title'          => '',
					'original_title' => $submenu_item[0],
					'type'           => $type,
					'deleted'        => $new,
					'new'            => $new,
				);
			}

			// Process submenu
			foreach ( $save_submenu[ $menu_ID ] as $i => $save_submenu_item ) {

				$save_submenu[ $menu_ID ][ $i ] = $this->process_submenu_item( $save_submenu_item, $menu_ID );

				// Edge-case: Header is in there twice under "Appearance" and then one is hidden via CSS. So, I remove
				// one here, and then add it back on submenu modify. This way, they stay together and the user doesn't
				// see 2 of them when customizing.
				if ( $save_submenu_item['id'] === 'custom-header' ) {
					unset( $save_submenu[ $menu_ID ][ $i ] );
				}
			}

			ksort( $save_submenu[ $menu_ID ] );
			$save_submenu[ $menu_ID ] = array_values( $save_submenu[ $menu_ID ] );
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
	 * @since 2.0.0
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

						$widget_title = isset( $widget['title'] ) ? $widget['title'] : '';

						// Sometimes title can be in args
						if ( is_array( $widget['args'] ) && isset( $widget['args']['__widget_basename'] ) ) {

							$widget_title = $widget['args']['__widget_basename'];
						}

						// Determine if CD Helper page widget
						$helper_pages          = array_keys( ClientDash_Helper_Pages::get_pages() );
						$is_helper_page_widget = in_array( substr( $widget['id'], 3 ), $helper_pages );

						$customized_widget = cd_array_search_by_key( $customized_dashboard, 'id', $widget['id'] );

						if ( $customized_widget ) {

							$save_dashboard[] = $this->process_dashboard_item( wp_parse_args( $customized_widget, array(
								'id'             => $widget['id'],
								'title'          => '',
								'original_title' => $widget_title,
								'new'            => false,
								'deleted'        => $customized_widget['deleted'] ? $customized_widget['deleted'] : false,
								'type'           => 'default',
							) ) );

						} else {

							$save_dashboard[] = $this->process_dashboard_item( array(
								'id'             => $widget['id'],
								'title'          => '',
								'original_title' => $widget_title,
								'new'            => ! empty( $customized_dashboard ) && ! $is_helper_page_widget,
								'deleted'        => ! empty( $customized_dashboard ),
								'type'           => 'default',
							) );
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
	 * Processes a menu item before inserting into the Customize tool.
	 *
	 * @since 2.0.0
	 *
	 * @param array $item Menu item array.
	 *
	 * @return array Processed menu item array.
	 */
	public static function process_menu_item( $item ) {

		switch ( $item['id'] ) {

			case 'plugins.php';
				$item['original_title'] = __( 'Plugins', 'client-dash' );
				break;

			case 'edit-comments.php';
				$item['original_title'] = __( 'Comments', 'client-dash' );
				break;
		}

		/**
		 * Processed menu item for insertion into the Customize tool.
		 *
		 * @since 2.0.0
		 */
		$item = apply_filters( 'cd_customize_process_menu_item', $item );

		return $item;
	}

	/**
	 * Processes a submenu item before inserting into the Customize tool.
	 *
	 * @since 2.0.0
	 *
	 * @param array $item Submenu item array.
	 * @param string $menu_ID Parent menu item ID.
	 *
	 * @return array Processed submenu item array.
	 */
	public static function process_submenu_item( $item, $menu_ID ) {

		// Links generated for customizer. Taken from /wp-admin/menu.php:160-173 as of WP version 4.8.0
		$customize_url            = esc_url( add_query_arg( 'return', urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ), 'customize.php' ) );
		$customize_header_url     = esc_url( add_query_arg( array( 'autofocus' => array( 'control' => 'header_image' ) ), $customize_url ) );
		$customize_background_url = esc_url( add_query_arg( array( 'autofocus' => array( 'control' => 'background_image' ) ), $customize_url ) );

		switch ( $item['id'] ) {

			case 'update-core.php';
				$item['original_title'] = __( 'Updates', 'client-dash' );
				break;

			case $customize_url:

				$item['id'] = 'wp_customize';
				break;

			case $customize_header_url:

				$item['id'] = 'wp_customize_header';
				break;

			case $customize_background_url:

				$item['id'] = 'wp_customize_background';
				break;
		}

		/**
		 * Processed submenu item for insertion into the Customize tool.
		 *
		 * @since 2.0.0
		 */
		$item = apply_filters( 'cd_customize_process_submenu_item', $item, $menu_ID );

		return $item;
	}

	/**
	 * Processes a dashboard item before inserting into the Customize tool.
	 *
	 * @since 2.0.0
	 *
	 * @param array $item Dashboard item array.
	 *
	 * @return array Processed dashboard item array.
	 */
	public static function process_dashboard_item( $item ) {

		switch ( $item['id'] ) {

			case 'dashboard_quick_press':
				$item['original_title'] = __( 'Quick Draft', 'client-dash' );
				break;
		}

		/**
		 * Processed dashboard item for insertion into the Customize tool.
		 *
		 * @since 2.0.0
		 */
		$item = apply_filters( 'cd_customize_process_dashboard_item', $item );

		return $item;
	}
}