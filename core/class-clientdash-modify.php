<?php
/**
 * Modifies the admin from the customizations.
 *
 * @since 2.0.0
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
 * @since 2.0.0
 */
class ClientDash_Modify {

	/**
	 * The modified menu.
	 *
	 * @since 2.0.0
	 *
	 * @var array|null
	 */
	public $menu;

	/**
	 * The modified submenu.
	 *
	 * @since 2.0.0
	 *
	 * @var array|null
	 */
	public $submenu;

	/**
	 * The modified dashboard.
	 *
	 * @since 2.0.0
	 *
	 * @var array|null
	 */
	public $dashboard;

	/**
	 * ClientDash_Modify constructor.
	 *
	 * @since 2.0.0
	 */
	function __construct() {

		add_filter( 'custom_menu_order', array( $this, 'modify_menu' ), 99999 );
		add_action( 'wp_dashboard_setup', array( $this, 'modify_dashboard' ), 99999 );
	}

	/**
	 * Grabs the customizations, if any.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function get_customizations() {

		static $done = false;

		if ( $done ) {

			return;
		}

		$done = true;

		$current_user = wp_get_current_user();

		if ( ! $current_user ) {

			return;
		}

		if ( ! isset( $current_user->roles[0] ) ) {

			return;
		}

		$role = $current_user->roles[0];

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
		 * @since 2.0.0
		 */
		$customizations = apply_filters( 'cd_customizations', $customizations );

		$this->menu      = $customizations['menu'];
		$this->submenu   = $customizations['submenu'];
		$this->dashboard = $customizations['dashboard'];
	}

	/**
	 * Modifies the menu.
	 *
	 * @since 2.0.0
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

			// Make sure all 6 indices exist.
			$menu_item = array_pad( $menu_item, 7, '');

			$processed_menu_item_id = $this->get_processed_menu_item_id( $menu_item[2] );

			$customized_menu_item_key = cd_array_get_index_by_key( $this->menu, 'id', $processed_menu_item_id );

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

		// Add custom links and separators.
		foreach ( $this->menu as $i => $menu_item ) {

			$type = isset( $menu_item['type'] ) ? $menu_item['type'] : 'default';

			switch ( $type ) {

				case 'custom_link':
					$new_menu[ $i ] = array(
						isset( $menu_item['title'] ) && $menu_item['title'] ? $menu_item['title'] : $menu_item['original_title'],
						'read',
						$menu_item['link'] ? $menu_item['link'] : '#',
						isset( $menu_item['title'] ) && $menu_item['title'] ? $menu_item['title'] : $menu_item['original_title'],
						'menu-top menu-custom-link',
						'menu-custom-link',
						$menu_item['icon'],
					);
					break;

				case 'separator':
					$new_menu[ $i ] = array(
						'',
						'read',
						$menu_item['id'],
						'',
						'wp-menu-separator',
					);
					break;
			}
		}

		// Sort and re-index
		ksort( $new_menu );
		$new_menu = array_values( $new_menu );

		/**
		 * The new, customized admin menu for the current role.
		 *
		 * @since 2.0.0
		 */
		$new_menu = apply_filters( 'cd_customized_menu', $new_menu, $menu );

		// Enforce that Client Dash menu always exist for admins
		if ( current_user_can( 'manage_options' ) &&
		     cd_array_get_index_by_key( $new_menu, 2, 'clientdash' ) === false
		) {

			$new_menu[] = $menu[ cd_array_get_index_by_key( $menu, 2, 'clientdash' ) ];
		}

		$new_submenu = array();

		// Links generated for customizer. Taken from /wp-admin/menu.php:160-173 as of WP version 4.8.0
		$customize_url            = esc_url( add_query_arg( 'return', urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ), 'customize.php' ) );
		$customize_header_url     = esc_url( add_query_arg( array( 'autofocus' => array( 'control' => 'header_image' ) ), $customize_url ) );
		$customize_background_url = esc_url( add_query_arg( array( 'autofocus' => array( 'control' => 'background_image' ) ), $customize_url ) );

		foreach ( $submenu as $menu_parent => $submenu_items ) {

			$menu_parent_i = cd_array_get_index_by_key( $menu, 2, $menu_parent );

			// Parent has been deleted
			if ( $menu_parent_i === false ) {

				continue;
			}

			$new_submenu[ $menu_parent ] = array();

			foreach ( $submenu_items as $i => $submenu_item ) {

				// If not customized, continue
				if ( ! isset( $this->submenu[ $menu_parent ] ) ) {

					continue;
				}

				// Make sure all 4 indices exist.
				$submenu_item = array_pad( $submenu_item, 4, '');

				$processed_submenu_item_id = $this->get_processed_submenu_item_id( $submenu_item[2], $menu_parent );

				$customized_submenu_item_key = cd_array_get_index_by_key(
					$this->submenu[ $menu_parent ],
					'id',
					$processed_submenu_item_id
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

			// Add custom links
			if ( isset( $this->submenu[ $menu_parent ] ) ) {

				foreach ( $this->submenu[ $menu_parent ] as $i => $submenu_item ) {

					$type = isset( $submenu_item['type'] ) ? $submenu_item['type'] : 'default';

					switch ( $type ) {

						case 'custom_link':
							$new_submenu[ $menu_parent ][ $i ] = array(
								isset( $submenu_item['title'] ) && $submenu_item['title'] ? $submenu_item['title'] : $submenu_item['original_title'],
								'read',
								$submenu_item['link'] ? $submenu_item['link'] : '#',
								isset( $submenu_item['title'] ) && $submenu_item['title'] ? $submenu_item['title'] : $submenu_item['original_title'],
								'submenu-custom-link',
								'submenu-custom-link'
							);
							break;
					}
				}
			}

			ksort( $new_submenu[ $menu_parent ] );

			$process_submenu             = $new_submenu[ $menu_parent ];
			$new_submenu[ $menu_parent ] = array();

			// Process submenu
			foreach ( $process_submenu as $i => $submenu_item ) {

				// Edge-case: Header is in there twice under "Appearance" and then one is hidden via CSS. So, I remove
				// one here, and then add it back on submenu modify. This way, they stay together and the user doesn't
				// see 2 of them when customizing.
				if ( $submenu_item[2] === $customize_header_url ) {

					$new_submenu[ $menu_parent ][] = $submenu_item;

					// Now duplicate, but with the proper link
					$submenu_item[2]               = 'custom-header';
					$new_submenu[ $menu_parent ][] = $submenu_item;
					continue;
				}

				$new_submenu[ $menu_parent ][] = $submenu_item;
			}
		}

		/**
		 * The new, customized admin submenu for the current role.
		 *
		 * @since 2.0.0
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
	 * @since 2.0.0
	 * @access private
	 */
	function modify_dashboard() {

		global $wp_meta_boxes;

		$this->get_customizations();

		if ( ! $this->dashboard ) {

			return;
		}

		// Add any custom widgets
		$custom_widgets = ClientDash_Customize::get_custom_dashboard_widgets();

		foreach ( $this->dashboard as $widget ) {

			$type = isset( $widget['type']) ? $widget['type'] : 'default';

			// Only concerned with non-default (custom) widgets
			if ( $type === 'default' ) {

				continue;
			}

			foreach ( $custom_widgets as $custom_widget ) {

				if ( $type === $custom_widget['id'] ) {

					wp_add_dashboard_widget(
						$widget['id'],
						$widget['title'] ? $widget['title'] : $widget['original_title'],
						array( 'ClientDash_Customize', 'custom_widget_callback' ),
						null,
						$widget
					);
				}
			}
		}

		if ( isset( $wp_meta_boxes['dashboard'] ) ) {

			foreach ( $wp_meta_boxes['dashboard'] as $position => &$priorities ) {

				foreach ( $priorities as $priority => &$widgets ) {

					foreach ( $widgets as $i => &$widget ) {

						$processed_widget_id = $this->get_processed_dashboard_item_id( $widget['id'] );

						$custom_widget = cd_array_search_by_key( $this->dashboard, 'id', $processed_widget_id );

						// No custom widget? remove it.
						if ( $custom_widget === false ||
						     ( isset( $custom_widget['deleted'] ) && $custom_widget['deleted'] )
						) {

							unset( $wp_meta_boxes['dashboard'][$position][$priority][$i] );
							continue;
						}

						// Customized title
						if ( isset( $custom_widget['title'] ) && $custom_widget['title'] ) {

							$widget['title'] = $custom_widget['title'];
						}

						// Other customizations
						$widget['settings'] = isset( $custom_widget['settings'] ) ? $custom_widget['settings'] : array();
					}
				}
			}
		}
	}

	/**
	 * Gets a processed menu ID from the Customize tool.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param string $item Menu item ID
	 *
	 * @return string Processed menu item ID.
	 */
	private function get_processed_menu_item_id( $ID ) {

		/**
		 * Processed menu item ID for menu lookup.
		 *
		 * @since 2.0.0
		 */
		$ID = apply_filters( 'cd_customize_get_processed_menu_id', $ID );

		return $ID;
	}

	/**
	 * Gets a processed submenu ID from the Customize tool.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param string $item Submenu item ID
	 * @param string $menu_ID Parent menu item ID.
	 *
	 * @return string Processed submenu item ID.
	 */
	private function get_processed_submenu_item_id( $ID, $menu_ID ) {

		// Links generated for customizer. Taken from /wp-admin/menu.php:160-173 as of WP version 4.8.0
		$customize_url            = esc_url( add_query_arg( 'return', urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ), 'customize.php' ) );
		$customize_header_url     = esc_url( add_query_arg( array( 'autofocus' => array( 'control' => 'header_image' ) ), $customize_url ) );
		$customize_background_url = esc_url( add_query_arg( array( 'autofocus' => array( 'control' => 'background_image' ) ), $customize_url ) );

		switch ( $ID ) {

			case $customize_url:

				$ID = 'wp_customize';
				break;

			case $customize_header_url:

				$ID = 'wp_customize_header';
				break;

			case $customize_background_url:

				$ID = 'wp_customize_background';
				break;
		}

		/**
		 * Processed submenu item ID for submenu lookup.
		 *
		 * @since 2.0.0
		 */
		$ID = apply_filters( 'cd_customize_get_processed_submenu_id', $ID, $menu_ID );

		return $ID;
	}

	/**
	 * Gets a processed dashboard ID from the Customize tool.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param string $item Dashboard item ID
	 *
	 * @return string Processed dashboard item ID.
	 */
	private function get_processed_dashboard_item_id( $ID ) {

		/**
		 * Processed dashboard item ID for dashboard lookup.
		 *
		 * @since 2.0.0
		 */
		$ID = apply_filters( 'cd_customize_get_processed_dashboard_id', $ID );

		return $ID;
	}
}