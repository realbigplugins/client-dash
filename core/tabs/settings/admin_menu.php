<?php

// TODO Construct mimicking page for menus
// TODO Use admin menu items data to output on menu page
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
	 * The post-modified admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public $modified_admin_menu;

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.6
	 */
	function __construct() {

		// Defaults
		$this->modified_admin_menu = get_option( 'cd_modified_admin_menu', $this->option_defaults['modified_admin_menu'] );

		// Get the original admin menus
		add_action( 'admin_menu', array( $this, 'get_orig_admin_menu' ), 99997 );

		// Remove the original admin menu
		add_action( 'admin_menu', array( $this, 'remove_orig_admin_menu' ), 99998 );

		// Add the new, modified admin menu
		add_action( 'admin_menu', array( $this, 'add_modified_admin_menu' ), 99999 );

		// Add the content
		$this->add_content_section( array(
			'name'     => 'Core Admin Menu Settings',
			'page'     => 'Settings',
			'tab'      => 'Admin Menu',
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * Get the original, un-modified admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function get_orig_admin_menu() {
		global $menu, $submenu;

		$orig_menu = array();

		foreach ( $menu as $menu_location => $menu_item ) {

			// TODO Find a way to get the callback... (maybe not necessary)
			// TODO Remove unnecessary keys
			$menu_array = array(
				'menu_title' => $menu_item[0],
				'capability' => $menu_item[1],
				'menu_slug'  => $menu_item[2],
				'page_title' => $menu_item[3],
				'position'   => $menu_location,
				'hookname'   => $menu_item[5],
				'icon_url'   => $menu_item[6]
			);


			$orig_menu[ $menu_location ] = $menu_array;

			foreach ( $submenu[ $menu_array['menu_slug'] ] as $submenu_location => $submenu_item ) {

				// TODO Find a way to get the callback here too... (maybe not necessary)
				// TODO Remove unnecessary keys
				$submenu_array = array(
					'menu_title'  => $submenu_item[0],
					'capability'  => $submenu_item[1],
					'menu_slug'   => $submenu_item[2],
					'page_title'  => $submenu_item[3],
					'parent_slug' => $menu_array['menu_slug']
				);

				$orig_menu[ $menu_location ]['submenus'][ $submenu_location ] = $submenu_array;
			}
		}

		// Re-order the menus and submenus into an indexed order
		$i = 0;
		foreach ( $orig_menu as $menu_position => $menu_item ) {

			unset( $orig_menu[ $menu_position ] );
			$orig_menu[ $i ] = $menu_item;

			$i_sub = 0;
			foreach ( $menu_item['submenus'] as $submenu_position => $submenu_item ) {

				unset( $orig_menu[ $i ]['submenus'][ $submenu_position ] );
				$orig_menu[ $i ]['submenus'][ $i_sub ] = $submenu_item;

				$i_sub ++;
			}

			$i ++;
		}

		$this->original_admin_menus = $orig_menu;
	}

	/**
	 * Removes the original admin menu.
	 *
	 * @since Client Dash 1.6
	 */
	public function remove_orig_admin_menu() {

		// If the menus have been set, remove all of them
		if ( $this->original_admin_menu ) {
			foreach ( $this->original_admin_menu as $menu_item ) {
				remove_menu_page( $menu_item['menu_slug'] );

				foreach ( $menu_item['submenus'] as $submenu_item ) {
					remove_submenu_page( $menu_item['menu_slug'], $submenu_item['menu_slug'] );
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
//		$modified_menu = $this->modified_admin_menu;
		$modified_menu = $this->original_admin_menu;

		// If the modified menus are set, then add them
		if ( $modified_menu ) {
			foreach ( $modified_menu as $menu_position => $menu_item ) {

				// If a separator, do that instead
				if ( strpos( $menu_item['menu_slug'], 'separator' ) !== false ) {
					$menu[ $menu_position ] = array('', 'read', $menu_item['menu_slug'], '', 'wp-menu-separator' );
				} else {
					add_menu_page(
						$menu_item['page_title'],
						$menu_item['menu_title'],
						$menu_item['capability'],
						$menu_item['menu_slug'],
						$menu_item['callback'],
						! empty( $menu_item['icon_url'] ) ? $menu_item['icon_url'] : 'none',
						$menu_position
					);
				}

				// Now for the sub-menus
				foreach ( $menu_item['submenus'] as $submenu_item ) {
					add_submenu_page(
						$menu_item['menu_slug'],
						$submenu_item['page_title'],
						$submenu_item['menu_title'],
						$submenu_item['capability'],
						$submenu_item['menu_slug'],
						$submenu_item['callback']
					);
				}
			}
		}
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.6
	 */
	public function block_output() {

		// TODO Reference wp-admin/nav-menus.php to copy the HTML and integrate it with CD
	}
}