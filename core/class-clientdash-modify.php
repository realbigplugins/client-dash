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
	 * ClientDash_Modify constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_filter( 'custom_menu_order', array( $this, 'modify_menu' ), 99999 );
		add_action( 'wp_dashboard_setup', array( $this, 'modify_dashboard' ), 99999 );
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

		// If loading in the previewer, use temp data EXCEPT on initial role loading
		if ( ClientDash_Customize::in_customizer() && ! ClientDash_Customize::is_saving_role() ) {

			$role = "preview_$role";
		}

		if ( ! ( $customizations = ClientDash_DB::get_customizations( $role ) ) ) {

			return;
		}

		/**
		 * The current user's customizations.
		 *
		 * @since {{VERSION}}
		 */
		$customizations = apply_filters( 'cd_customizations', $customizations );

		$this->menu      = $customizations['menu'];
		$this->submenu   = $customizations['submenu'];
		$this->dashboard = $customizations['dashboard'];
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

		$new_menu = array();

		foreach ( $menu as $i => $menu_item ) {

			$customized_menu_item_key = cd_array_get_index_by_key( $this->menu, 'id', $menu_item[2] );

			if ( $customized_menu_item_key === false ) {

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

		// Sort and re-index
		ksort( $new_menu );
		$new_menu = array_values( $new_menu );

		/**
		 * The new, customized admin menu for the current role.
		 *
		 * @since {{VERSION}}
		 */
		$new_menu = apply_filters( 'cd_customized_menu', $new_menu, $menu );

		// Enforce that Client Dash menu always exist for admins
		if ( current_user_can( 'manage_options' ) &&
		     cd_array_get_index_by_key( $new_menu, 2, 'clientdash' ) === false
		) {

			$new_menu[] = $menu[ cd_array_get_index_by_key( $menu, 2, 'clientdash' ) ];
		}

		$new_submenu = array();

		foreach ( $submenu as $menu_parent => $submenu_items ) {

			$menu_parent_i = cd_array_get_index_by_key( $menu, 2, $menu_parent );

			// Parent has been deleted
			if ( $menu_parent_i === false ) {

				continue;
			}

			$new_submenu[ $menu_parent ] = array();

			foreach ( $submenu_items as $i => $submenu_item ) {

				$customized_submenu_item_key = cd_array_get_index_by_key(
					$this->submenu[ $menu_parent ],
					'id',
					$submenu_item[2]
				);

				if ( $customized_submenu_item_key === false ) {

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
		}

		/**
		 * The new, customized admin submenu for the current role.
		 *
		 * @since {{VERSION}}
		 */
		$new_submenu = apply_filters( 'cd_customized_submenu', $new_submenu, $submenu );

		// Enforce that Client Dash submenu always exist for admins
		if ( current_user_can( 'manage_options' ) &&
		     ( ! isset( $new_submenu['clientdash'] ) || empty( $new_submenu['clientdash'] ) )
		) {

			$new_submenu['clientdash'] = $submenu['clientdash'];
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
}