<?php
/**
 * Handles Client Dash upgrades.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class ClientDash_Upgrade
 *
 * Handles Client Dash upgrades.
 *
 * @since {{VERSION}}
 */
class ClientDash_Upgrade {

	/**
	 * ClientDash_Upgrade constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		$version = get_option( 'cd_version', 0 );

		if ( version_compare( $version, CLIENTDASH_VERSION, '<' ) ) {

//			$this->upgrade();
		}
	}

	/**
	 * Upgrades Client Dash to the latest version.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private function upgrade() {

		// Run initial install to mamke sure this runs from upgrade
		ClientDash_Install::install();

		$this->migrate_admin_menus();
		$this->migrate_dashboard_widgets();

//		update_option( 'cd_version', CLIENTDASH_VERSION );
	}

	/**
	 * Migrate admin menus.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private function migrate_admin_menus() {

		$menus = wp_get_nav_menus();

		foreach ( $menus as $menu ) {

			if ( substr( $menu->name, 0, 14 ) === 'cd_admin_menu_' ) {

				$role = substr( $menu->name, 14 );

				$new_menu    = array();
				$new_submenu = array();

				$items = wp_get_nav_menu_items( $menu->term_id );

				foreach ( $items as $item ) {

					$menu_item = array(
						'id'             => $item->url,
						'title'          => $item->title,
						'original_title' => get_post_meta( $item->db_id, '_menu_item_original_title', true ),
						'deleted'        => false,
					);

					if ( (int) $item->menu_item_parent > 0 ) {

						if ( ! isset( $new_submenu[ $new_menu[ $item->menu_item_parent ]['id'] ] ) ) {

							$new_submenu[ $new_menu[ $item->menu_item_parent ]['id'] ] = array();
						}

						$new_submenu[ $new_menu[ $item->menu_item_parent ]['id'] ][] = $menu_item;

						continue;
					}

					$menu_item['icon']          = get_post_meta( $item->db_id, '_menu_item_cd_icon', true );
					$menu_item['original_icon'] = '';
					$menu_item['type']          = 'menu_item';

					$new_menu[ $item->ID ] = $menu_item;
				}

				cd_update_role_customizations( $role, array(
					'menu'    => $new_menu,
					'submenu' => $new_submenu,
				) );

//				wp_delete_nav_menu( $menu->term_id );
			}
		}
	}

	private function migrate_dashboard_widgets() {

		$sidebars    = get_option( 'sidebars_widgets' );
		$new_widgets = array();

		if ( isset( $sidebars['cd-dashboard'] ) ) {

			foreach ( $sidebars['cd-dashboard'] as $ID ) {

				// Break apart the ID
				preg_match_all( "/(.*)(-\d+)/", $ID, $matches );
				$ID_base   = $matches[1][0];
				$ID_number = str_replace( '-', '', $matches[2][0] );

				// Get all widgets of this type
				$widgets = get_option( "widget_{$ID_base}" );

				// Get the current widget
				$widget = $widgets[ $ID_number ];

				// Set the ID
				$widget['ID'] = isset( $widget['_cd_extension'] ) && $widget['_cd_extension'] == '1' ? $ID : $ID_base;

				// Add it on
				$new_widgets[] = array(
					'id'             => $widget['ID'],
					'title'          => $widget['title'],
					'original_title' => $widget['_original_title'],
					'deleted'        => false,
				);
			}

			foreach ( get_editable_roles() as $role_ID => $role ) {

				cd_update_role_customizations( $role_ID, array(
					'dashboard' => $new_widgets,
				) );
			}

//			unset( $sidebars['cd-dashboard']);
//			update_option( 'sidebars_widgets', $sidebars );
		}
	}

	private function migrate_cd_core_pages() {

		$account_dashicon   = get_option( 'cd_dashicon_account', $this->option_defaults['dashicon_account'] );
		$reports_dashicon   = get_option( 'cd_dashicon_reports', $this->option_defaults['dashicon_reports'] );
		$help_dashicon      = get_option( 'cd_dashicon_help', $this->option_defaults['dashicon_help'] );
		$webmaster_dashicon = get_option( 'cd_dashicon_webmaster', $this->option_defaults['dashicon_webmaster'] );

		cd_update_role_customizations( $role_ID, array(
			'dashboard' => $new_widgets,
		) );
	}

	private function migrate_webmaster_page() {

	}
}