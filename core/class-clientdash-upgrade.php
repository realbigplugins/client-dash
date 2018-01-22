<?php
/**
 * Handles Client Dash upgrades.
 *
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || die();

/**
 * Class ClientDash_Upgrade
 *
 * Handles Client Dash upgrades.
 *
 * @since 2.0.0
 */
class ClientDash_Upgrade {

	/**
	 * ClientDash_Upgrade constructor.
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if needs to upgrade, false if does not.
	 */
	function __construct() {

		$version = get_option( 'cd_version', 0 );

		if ( version_compare( $version, '2.0', '<=' ) ) {

			add_action( 'admin_notices', array( $this, 'show_upgrade_nag' ) );
		}

		if ( isset( $_GET['clientdash_upgrade'] ) ) {

			add_action( 'admin_menu', array( $this, 'init_upgrade' ), 999999 );
		}

		if ( isset( $_GET['clientdash_upgraded'] ) ) {

			add_action( 'admin_notices', array( $this, 'show_upgraded_nag' ) );
		}
	}

	/**
	 * Returns if Client Dash needs to update.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	function needs_update() {

		return version_compare( get_option( 'cd_version', 0 ), '2.0', '<=' );
	}

	/**
	 * Initializes the upgrade so we can hook it after admin menu has loaded.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function init_upgrade() {

		$this->upgrade();

		if ( $_GET['clientdash_upgrade'] === '1' ) {

			$this->migrate();
		}

		wp_redirect( add_query_arg(
			'clientdash_upgraded',
			$_GET['clientdash_upgrade'],
			remove_query_arg( 'clientdash_upgrade' )
		) );
		exit();
	}

	/**
	 * Determines if the plugin has "migrate-able" settings or not.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return bool
	 */
	private function has_old_settings() {

		$settings = array_filter( array(
			$this->get_old_nav_menus(),
			$this->get_old_widgets(),
			$this->get_old_helper_pages(),
			$this->get_old_admin_page(),
		) );

		return ! empty( $settings );
	}

	/**
	 * Displays the database upgrade nag.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function show_upgrade_nag() {

		if ( ! $this->has_old_settings() ) {

			$this->show_upgrade_nag_no_migration();

			return;
		}

		$confirm_text_1 = sprintf(
		/* translators: %s is current Client Dash version */
			__( 'Important: If you have made any customizations since updating Client Dash to %s, all customizations will be overwritten by previous Client Dash customizations. It is recommended to backup your website first.', 'client-dash' ),
			CLIENTDASH_VERSION
		);

		$confirm_text_2 = __( 'Are you sure you want to upgrade the database WITHOUT migrating previous customizations? You will be starting from scratch.', 'client-dash' );
		?>
        <div class="notice notice-warning">
            <p>
				<?php
				printf(
				    /* translators: Both %s are HTML for <strong> */
					__( 'In order to use %sClient Dash%s, your database needs to be upgraded and your previous customizations need to be migrated. It is highly advised to backup your database first.', 'client-dash' ),
					'<strong>',
					'</strong>'
				);
				?>
            </p>
            <p>
                <a href="<?php echo add_query_arg( 'clientdash_upgrade', '1' ); ?>" class="button button-primary"
                   onclick="return confirm('<?php echo $confirm_text_1; ?>');">
					<?php _e( 'Upgrade and Migrate', 'client-dash' ); ?>
                </a>
                &nbsp;
                <a href="<?php echo add_query_arg( 'clientdash_upgrade', '2' ); ?>"
                   onclick="return confirm('<?php echo $confirm_text_2; ?>');">
					<?php _e( 'or upgrade database but do not migrate previous customizations.', 'client-dash' ); ?>
                </a>
            </p>
            <p>
                <strong>
					<?php
					_e( 'IMPORTANT: None of your previous Client Dash customizations have been migrated yet. If you want to keep your previous customizations, you must first perform this database upgrade.', 'client-dash' );
					?>
                </strong>
            </p>
        </div>
		<?php
	}

	/**
	 * Displays update nag for no migration.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function show_upgrade_nag_no_migration() {

		?>
        <div class="notice notice-warning">
            <p>
				<?php
				printf(
				    /* translators: Both %s are HTML for <strong> */
					__( 'In order to use %sClient Dash%s, your database needs to be upgraded.', 'client-dash' ),
					'<strong>',
					'</strong>'
				);
				?>
                <a href="<?php echo add_query_arg( 'clientdash_upgrade', '2' ); ?>" class="button button-primary">
					<?php _e( 'Upgrade', 'client-dash' ); ?>
                </a>
            </p>
        </div>
		<?php
	}

	/**
	 * Displays to notify the user the ugprade is done.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function show_upgraded_nag() {

		if ( $_GET['clientdash_upgraded'] === '1' ) {

			$message = __( 'Client Dash has successfully upgraded the database and migrated your previous settings!', 'client-dash' );

		} else {

			$message = __( 'Client Dash has successfully upgraded the database!', 'client-dash' );
		}
		?>
        <div class="notice notice-success is-dismissible">
            <p>
				<?php echo $message; ?>
            </p>
        </div>
		<?php
	}

	/**
	 * Upgrades Client Dash to the latest version.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function upgrade() {

		// Run initial install to make sure this runs from upgrade
		ClientDash_Install::install();

		update_option( 'cd_version', CLIENTDASH_VERSION );
	}

	/**
	 * Migrates previousy (pre-2.0) Client Dash settings.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function migrate() {

		$this->migrate_admin_menus();
		$this->migrate_dashboard_widgets();
		$this->migrate_helper_pages();
		$this->migrate_admin_page();
	}

	/**
	 * Returns any old CD Nav menus.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_old_nav_menus() {

		$cd_menus  = array();
		$nav_menus = wp_get_nav_menus();

		foreach ( $nav_menus as $nav_menu ) {

			if ( substr( $nav_menu->name, 0, 14 ) === 'cd_admin_menu_' ) {

				$cd_menus[] = $nav_menu;
			}
		}

		return $cd_menus;
	}

	/**
	 * Returns any old CD widgets.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_old_widgets() {

		$cd_widgets = array();

		$sidebars = get_option( 'sidebars_widgets' );

		if ( isset( $sidebars['cd-dashboard'] ) ) {

			$cd_widgets = $sidebars['cd-dashboard'];
		}

		return $cd_widgets;
	}

	/**
	 * Returns old helper pages settings.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_old_helper_pages() {

		$cd_helper_pages = array();

		$icons = array_filter( array(
			'account'   => get_option( 'cd_dashicon_account' ),
			'reports'   => get_option( 'cd_dashicon_reports' ),
			'help'      => get_option( 'cd_dashicon_help' ),
			'webmaster' => get_option( 'cd_dashicon_webmaster' ),
		) );

		$roles = get_option( 'cd_content_sections_roles' );

		if ( $icons || $roles ) {

			$cd_helper_pages = array(
				'icons' => $icons,
				'roles' => $roles,
			);
		}

		return $cd_helper_pages;
	}

	/**
	 * Returns old admin page settings.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_old_admin_page() {

		$cd_admin_page = array_filter( array(
			'content'    => get_option( 'cd_webmaster_main_tab_content' ),
			'feed_url'   => get_option( 'cd_webmaster_feed_url' ),
			'feed_count' => get_option( 'cd_webmaster_feed_count' ),
		) );

		return $cd_admin_page;
	}

	/**
	 * Migrate admin menus.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function migrate_admin_menus() {

		global $menu, $submenu;

		$nav_menus = $this->get_old_nav_menus();

		foreach ( $nav_menus as $nav_menu ) {

			$role = substr( $nav_menu->name, 14 );

			$new_menu    = array();
			$new_submenu = array();

			$items = wp_get_nav_menu_items( $nav_menu->term_id );

			foreach ( $items as $item ) {

				$original_title = get_post_meta( $item->db_id, '_menu_item_original_title', true );

				$menu_item = array(
					'id'             => $item->url,
					'title'          => $item->title !== $original_title ? $item->title : '',
					'original_title' => $original_title,
					'deleted'        => false,
					'new'            => false,
				);

				if ( (int) $item->menu_item_parent > 0 ) {

					// Edge case: Webmaster is now Admin Page. Change ID
					if ( $menu_item['id'] === 'cd_webmaster' ) {

						$menu_item['id'] = 'cd_admin_page';
					}

					// Check for presence of submenu item in original submenu. If it doesn't exist, it was moved to
					// a different parent, which is no longer allowed.
					if ( cd_array_get_index_by_key(
						     $submenu[ $new_menu[ $item->menu_item_parent ]['id'] ],
						     2,
						     $menu_item['id'] ) === false
					) {

						continue;
					}

					if ( ! isset( $new_submenu[ $new_menu[ $item->menu_item_parent ]['id'] ] ) ) {

						$new_submenu[ $new_menu[ $item->menu_item_parent ]['id'] ] = array();
					}

					$new_submenu[ $new_menu[ $item->menu_item_parent ]['id'] ][] = $menu_item;

					continue;
				}

				$menu_item['icon']          = get_post_meta( $item->db_id, '_menu_item_cd_icon', true );
				$menu_item['original_icon'] = '';

				$item_type = get_post_meta( $item->db_id, '_menu_item_cd_type', true );

				switch ( $item_type ) {

                    case 'link':

                        $menu_item['link'] = $item->url;
                        $menu_item['type'] = 'custom_link';
                        break;

					case 'separator':

						$menu_item['type'] = 'separator';
						break;

					default:

						$menu_item['type'] = 'default';
						break;
				}

				$new_menu[ $item->ID ] = $menu_item;
			}

			$new_menu = array_values( $new_menu );

			// Add existing menu items as deleted
			foreach ( $menu as $i => $menu_item ) {

				if ( cd_array_get_index_by_key( $new_menu, 'id', $menu_item[2] ) === false ) {

					$type = 'default';

					if ( strpos( $menu_item[4], 'wp-menu-separator' ) !== false ) {

						$type = 'separator';
					}

					if ( $menu_item[2] == 'clientdash' ) {

						$type = 'clientdash';
					}

					$new_menu[] = array(
						'id'             => $menu_item[2],
						'title'          => '',
						'original_title' => $menu_item[0],
						'icon'           => '',
						'original_icon'  => isset( $menu_item[6] ) ? $menu_item[6] : '',
						'deleted'        => $type !== 'clientdash' || false,
						'type'           => $type,
						'new'            => false,
					);
				}
			}

			// Add existing submenu items as deleted
			foreach ( $submenu as $menu_ID => $submenu_items ) {

				foreach ( $submenu_items as $i => $submenu_item ) {

					if ( ! isset( $new_submenu[ $menu_ID ] ) ||
					     cd_array_get_index_by_key( $new_submenu[ $menu_ID ], 'id', $submenu_item[2] ) === false
					) {

						$type = 'submenu_item';

						if ( cd_is_core_page( $submenu_item[2] ) ) {

							$type = 'cd_page';
						}

						$save_submenu[ $menu_ID ][] = array(
							'id'             => $submenu_item[2],
							'title'          => '',
							'original_title' => $submenu_item[0],
							'deleted'        => true,
							'type'           => $type,
							'new'            => false,
						);
					}
				}
			}

			cd_update_role_customizations( $role, array(
				'menu'    => $new_menu,
				'submenu' => $new_submenu,
			) );

			wp_delete_nav_menu( $nav_menu->term_id );
		}
	}

	/**
	 * Migrates dashboard widgets.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function migrate_dashboard_widgets() {

		$sidebars = $this->get_old_widgets();

		if ( ! $sidebars ) {

			return;
		}

		$new_widgets = array();

		foreach ( $sidebars as $ID ) {

			// Break apart the ID
			preg_match_all( "/(.*)(-\d+)/", $ID, $matches );
			$ID_base   = $matches[1][0];
			$ID_number = str_replace( '-', '', $matches[2][0] );

			// Previous versions of Client Dash allowed multiple widgets of the same type. This is nonsense. If a
			// widget of this type has been added, skip it.
			if ( in_array( $ID_base, wp_list_pluck( $new_widgets, 'id' ) ) ) {

				continue;
			}

			// Get all widgets of this type
			$widgets = get_option( "widget_{$ID_base}" );

			// Get the current widget
			$widget = $widgets[ $ID_number ];

			// Edge-case
			if ( ! $widget ) {

				continue;
			}

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

		$sidebars = get_option( 'sidebars_widgets' );
		unset( $sidebars['cd-dashboard'] );
		update_option( 'sidebars_widgets', $sidebars );
	}

	/**
	 * Migrates any Helper Pages (previously CD Core Pages) customizations.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function migrate_helper_pages() {

		$helper_pages = $this->get_old_helper_pages();

		// No customizations saved yet, don't bother.
		if ( ! $helper_pages ) {

			return;
		}

		$pages = ClientDash_Helper_Pages::get_pages();
		foreach ( $pages as $page_ID => &$page ) {

		    if ( $page_ID === 'admin_page' ) {

		        $page_ID = 'webmaster';
            }

			if ( $helper_pages['icons'] && isset( $helper_pages['icons'][ $page_ID ] ) ) {

				$page['icon'] = $helper_pages['icons'][ $page_ID ];
			}

			if ( $helper_pages['roles'] && isset( $helper_pages['roles'][ $page_ID ] ) ) {

				foreach ( $helper_pages['roles'][ $page_ID ] as $tab => $content_blocks ) {

					// One renamed tab
					if ( $tab === 'about_you' ) {

						$tab = 'about';
					}

					if ( ! isset( $page['tabs'][ $tab ] ) ) {

						continue;
					}

					// We will only be focusing on tabs now; so the stop/gap is to just use the first content block
					$content_block = array_shift( $content_blocks );

					$page['tabs'][ $tab ]['roles'] = array_keys( $content_block, 'visible' );
				}
			}

			// Admin Page (previously "Webmaster") customizations
			if ( $page_ID === 'webmaster' ) {

				$admin_page_title = get_option( 'cd_webmaster_name' );

				if ( $admin_page_title ) {

					$page['title'] = $admin_page_title;
					delete_option( 'cd_webmaster_name' );
				}

				$admin_page_main_tab_title = get_option( 'cd_webmaster_main_tab_name' );

				if ( $admin_page_main_tab_title ) {

					$page['tabs']['main']['title'] = $admin_page_main_tab_title;
					delete_option( 'cd_webmaster_main_tab_name' );
				}

				$disable_feed_tab = get_option( 'cd_webmaster_feed' );

				if ( $disable_feed_tab ) {

					$page['tabs']['feed']['roles'] = array();
					delete_option( 'cd_webmaster_feed' );
				}
			}
		}

		update_option( 'cd_helper_pages', $pages );

		delete_option( 'cd_dashicon_account' );
		delete_option( 'cd_dashicon_reports' );
		delete_option( 'cd_dashicon_help' );
		delete_option( 'cd_dashicon_webmaster' );
		delete_option( 'cd_content_sections_roles' );
	}

	/**
	 * Migrates the Admin Page (previously Webmaster page).
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function migrate_admin_page() {

		$admin_page = $this->get_old_admin_page();

		if ( $admin_page['content'] ) {

			update_option( 'cd_adminpage_content', $admin_page['content'] );
			delete_option( 'cd_webmaster_main_tab_content' );
		}

		if ( $admin_page['feed_url'] ) {

			update_option( 'cd_adminpage_feed_url', $admin_page['feed_url'] );
			delete_option( 'cd_webmaster_feed_url' );
		}

		if ( $admin_page['feed_count'] ) {

			update_option( 'cd_adminpage_feed_count', $admin_page['feed_count'] );
			delete_option( 'cd_webmaster_feed_count' );
		}
	}
}