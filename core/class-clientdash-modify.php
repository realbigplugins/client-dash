<?php
/**
 * Modifies the admin from the customizations.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Class ClientDash_Modify
 *
 * Modifies the admin from the customizations.
 *
 * @since {{VERSION}}
 */
class ClientDash_Modify {

	/**
	 * The modified menu.
	 *
	 * @since {{VERSION}}
	 *
	 * @var array|null
	 */
	public $menu;

	/**
	 * The modified submenu.
	 *
	 * @since {{VERSION}}
	 *
	 * @var array|null
	 */
	public $submenu;

	/**
	 * The modified dashboard.
	 *
	 * @since {{VERSION}}
	 *
	 * @var array|null
	 */
	public $dashboard;

	/**
	 * The customized pages.
	 *
	 * @since {{VERSION}}
	 *
	 * @var array|null
	 */
	public $cdpages;

	/**
	 * ClientDash_Modify constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_filter( 'custom_menu_order', array( $this, 'modify_menu' ), 99999 );
		add_action( 'wp_dashboard_setup', array( $this, 'modify_dashboard' ), 99999 );
		add_filter( 'cd_core_pages', array( $this, 'modify_cd_pages' ), 99999 );
	}

	/**
	 * Grabs the customizations, if any.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private function get_customizations() {

		static $done = false;

		if ( $done ) {

			return;
		}

		$done = true;

		if ( ! ( $user = wp_get_current_user() ) ) {

			return;
		}

		if ( ! isset( $user->roles[0] ) ) {

			return;
		}

		$role = $user->roles[0];

		// If loading in the previewer, use temp data
		if ( isset( $_GET['cd_customizing'] ) ) {

			$role = "preview_$role";
		}

		if ( ! ( $customizations = ClientDash_DB::get_customizations( $role ) ) ) {

			return;
		}

		$this->menu      = $customizations['menu'];
		$this->submenu   = $customizations['submenu'];
		$this->dashboard = $customizations['dashboard'];
		$this->cdpages   = $customizations['cdpages'];
	}

	/**
	 * Modifies the menu.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function modify_menu( $bool ) {

		global $menu, $submenu;

		$this->get_customizations();

		if ( ! $this->menu && ! $this->submenu ) {

			return;
		}

		$uncustomized_menu = array();
		$new_menu          = array();

		foreach ( $menu as $i => $menu_item ) {

			$customized_menu_item_key = cd_array_get_index_by_key( $this->menu, 'id', $menu_item[2] );

			if ( $customized_menu_item_key === false) {

				$uncustomized_menu[] = $menu_item;
				continue;
			}

			$customized_menu_item = $this->menu[ $customized_menu_item_key ];

			// Deleted item
			if ( $customized_menu_item['deleted'] ) {

				continue;
			}

			// Modify item
			$new_menu[ $customized_menu_item_key ] = array(
				$customized_menu_item['title'] ? $customized_menu_item['title'] : $menu_item[0],
				$menu_item[1],
				$menu_item[2],
				$customized_menu_item['title'] ? $customized_menu_item['title'] : $menu_item[3],
				$menu_item[4],
				$menu_item[5],
				$customized_menu_item['icon'] ? $customized_menu_item['icon'] : $menu_item[6],
			);
		}

		ksort( $new_menu );
		$new_menu = array_merge( $new_menu, $uncustomized_menu );

		$new_submenu = array();

		foreach ( $submenu as $menu_parent => $submenu_items ) {

			$menu_parent_i = cd_array_get_index_by_key( $menu, 2, $menu_parent );

			// Parent has been deleted
			if ( $menu_parent_i === false ) {

				continue;
			}

			$uncustomized_submenu        = array();
			$new_submenu[ $menu_parent ] = array();

			foreach ( $submenu_items as $i => $submenu_item ) {

				$customized_submenu_item_key = cd_array_get_index_by_key(
					$this->submenu[ $menu_parent ],
					'id',
					$submenu_item[2]
				);

				if ( $customized_submenu_item_key === false ) {

					$uncustomized_submenu[] = $submenu_item;
					continue;
				}

				$customized_submenu_item = $this->submenu[ $menu_parent ][ $customized_submenu_item_key ];

				// Deleted item
				if ( $customized_submenu_item['deleted'] ) {

					continue;
				}

				// Modify item
				$new_submenu[ $menu_parent ][ $customized_submenu_item_key ] = array(
					$customized_submenu_item['title'] ? $customized_submenu_item['title'] : $submenu_item[0],
					$submenu_item[1],
					$submenu_item[2],
					$customized_submenu_item['title'] ? $customized_submenu_item['title'] : $submenu_item[3],
				);
			}

			ksort( $new_submenu[ $menu_parent ] );
			$new_submenu[ $menu_parent ] = array_merge( $new_submenu[ $menu_parent ], $uncustomized_submenu );
		}

		$menu    = $new_menu;
		$submenu = $new_submenu;

		return $bool;
	}

	/**
	 * Modifies the dashboard.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function modify_dashboard() {

		global $wp_meta_boxes;

		$this->get_customizations();

		if ( ! $this->dashboard ) {

			return;
		}

		if ( isset( $wp_meta_boxes['dashboard'] ) ) {

			foreach ( $wp_meta_boxes['dashboard'] as $position => $priorities ) {

				foreach ( $priorities as $priority => $widgets ) {

					foreach ( $widgets as $i => $widget ) {

						// No modification
						if ( ( $custom_widget = cd_array_search_by_key( $this->dashboard, 'id', $widget['id'] ) ) === false ) {

							continue;
						}

						// Modify
						if ( isset( $custom_widget['deleted'] ) && $custom_widget['deleted'] ) {

							unset( $wp_meta_boxes['dashboard'][ $position ][ $priority ][ $i ]['title'] );
							continue;
						}

						if ( isset( $custom_widget['title'] ) && $custom_widget['title'] ) {

							$wp_meta_boxes['dashboard'][ $position ][ $priority ][ $i ]['title'] = $custom_widget['title'];
						}
					}
				}
			}
		}
	}

	/**
	 * Modifies core CD pages.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @param array $pages
	 *
	 * @return array
	 */
	function modify_cd_pages( $pages ) {

		$this->get_customizations();

		if ( ! $this->cdpages ) {

			return $pages;
		}

		foreach ( $pages as $i => $page ) {

			$custom_page = cd_array_search_by_key( $this->cdpages, 'id', $page['id'] );

			if ( ! $custom_page ) {

				continue;
			}

			if ( $custom_page['deleted'] ) {

				unset( $pages[ $i ] );
				continue;
			}

			$pages[ $i ] = wp_parse_args( $custom_page, $page );
		}

		// Re-index
		$pages = array_values( $pages );

		return $pages;
	}
}