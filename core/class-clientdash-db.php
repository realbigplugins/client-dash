<?php
/**
 * Database functions.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Class ClientDash_DB
 *
 * Database functions.
 *
 * @since {{VERSION}}
 */
class ClientDash_DB {

	/**
	 * Gets a set of customizations.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $role Role of the customization.
	 *
	 * @return array|null|object|void
	 */
	public static function get_customizations( $role ) {

		global $wpdb;

		$results = $wpdb->get_row(
			"
			SELECT menu,submenu,dashboard,cdpages
			FROM {$wpdb->prefix}cd_customizations
			WHERE role = '$role'
			",
			ARRAY_A );

		if ( $results ) {

			foreach ( $results as &$item ) {

				$item = maybe_unserialize( $item );

				if ( $item === null ) {

					$item = array();
				}
			}
		}

		/**
		 * Filters the get_customizations results.
		 *
		 * @since {{VERSION}}
		 */
		$results = apply_filters(
			'cd_db_get_customizations_by_role',
			$results,
			$role
		);

		return $results;
	}

	/**
	 * Gets the role's custom menu, if set.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $role Role to get menu for.
	 *
	 * @return array|bool|mixed|void
	 */
	public static function get_role_menu( $role ) {

		if ( ! ( $results = self::get_customizations( $role ) ) ) {

			return false;
		}

		if ( ! isset( $results['menu'] ) ) {

			return array();
		}

		/**
		 * Filters the role's custom menu, if set.
		 *
		 * @since {{VERSION}}
		 */
		$menu = apply_filters( 'cd_role_menu', $results['menu'] );

		return $menu;
	}

	/**
	 * Gets the role's custom submenu, if set.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $role Role to get menu for.
	 *
	 * @return array|bool|mixed|void
	 */
	public static function get_role_submenu( $role ) {

		if ( ! ( $results = self::get_customizations( $role ) ) ) {

			return false;
		}

		if ( ! isset( $results['submenu'] ) ) {

			return array();
		}

		/**
		 * Filters the role's custom submenu, if set.
		 *
		 * @since {{VERSION}}
		 */
		$submenu = apply_filters( 'cd_role_submenu', $results['submenu'] );

		return $submenu;
	}

	/**
	 * Gets the role's custom dashboard, if set.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $role Role to get dashboard for.
	 *
	 * @return array|bool|mixed|void
	 */
	public static function get_role_dashboard( $role ) {

		if ( ! ( $results = self::get_customizations( $role ) ) ) {

			return false;
		}

		if ( ! isset( $results['dashboard'] ) ) {

			return array();
		}

		/**
		 * Filters the role's custom dashboard, if set.
		 *
		 * @since {{VERSION}}
		 */
		$dashboard = apply_filters( 'cd_role_dashboard', $results['dashboard'] );

		return $dashboard;
	}

	/**
	 * Gets the role's custom pages, if set.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $role Role to get pages for.
	 *
	 * @return array|bool|mixed|void
	 */
	public static function get_role_pages( $role ) {

		if ( ! ( $results = self::get_customizations( $role ) ) ) {

			return false;
		}

		if ( ! isset( $results['pages'] ) ) {

			return array();
		}

		/**
		 * Filters the role's custom pages, if set.
		 *
		 * @since {{VERSION}}
		 */
		$pages = apply_filters( 'cd_role_pages', $results['pages'] );

		return $pages;
	}

	/**
	 * Updates or adds a role customizations.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $role
	 * @param array $customizations
	 *
	 * @return array|null|object|void
	 */
	public static function update_customizations( $role, $customizations ) {

		global $wpdb;

		/**
		 * Filters the customizations to update/add.
		 *
		 * @since {{VERSION}}
		 */
		$customizations = apply_filters( 'cd_db_update_role_customizations', $customizations, $role );

		$formats = array();

		foreach ( $customizations as &$item ) {

			$item = maybe_serialize( $item );

			if ( is_string( $item ) ) {

				$formats[] = '%s';
			}
			if ( is_int( $item ) ) {

				$formats[] = '%d';
			}
		}

		// Update if exists
		if ( self::get_customizations( $role ) ) {

			$result = $wpdb->update(
				"{$wpdb->prefix}cd_customizations",
				$customizations,
				array(
					'role' => $role,
				),
				$formats,
				array(
					'%s',
				)
			);

		} else {

			$customizations['role'] = $role;
			array_unshift( $formats, '%s' );

			$result = $wpdb->insert(
				"{$wpdb->prefix}cd_customizations",
				$customizations,
				$formats
			);
		}

		if ( $result === 1 ) {

			return $wpdb->insert_id;

		} else {

			return $result;
		}
	}

	/**
	 * Deletes a role customizations.
	 *
	 * @since {{VERSION}}
	 *
	 * @param string $role
	 *
	 * @return array|null|object False or WP_Error on failure, null on nothing to delete, 1 on success.
	 */
	public static function delete_customizations( $role ) {

		global $wpdb;

		if ( ! self::get_customizations( $role ) ) {

			return null;
		}

		$result = $wpdb->delete( "{$wpdb->prefix}cd_customizations", array(
			'role' => $role,
		) );

		if ( $result === 1 ) {

			return true;

		} else {

			return $result;
		}
	}
}

/**
 * Quick access to database class.
 *
 * @since {{VERSION}}
 */
function ClientDashDB() {

	return ClientDash()->db;
}