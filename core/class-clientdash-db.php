<?php
/**
 * Database functions.
 *
 * @since 2.0.0
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
 * @since 2.0.0
 */
class ClientDash_DB {

	/**
	 * Gets a set of customizations.
	 *
	 * @since 2.0.0
	 *
	 * @param string $role Role of the customization.
	 *
	 * @return array|null|object|void
	 */
	public static function get_customizations( $role ) {

		global $wpdb;

		$results = $wpdb->get_row(
			"
			SELECT menu,submenu,dashboard
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
		 * @since 2.0.0
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
	 * @since 2.0.0
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
		 * @since 2.0.0
		 */
		$menu = apply_filters( 'cd_role_menu', $results['menu'] );

		return $menu;
	}

	/**
	 * Gets the role's custom submenu, if set.
	 *
	 * @since 2.0.0
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
		 * @since 2.0.0
		 */
		$submenu = apply_filters( 'cd_role_submenu', $results['submenu'] );

		return $submenu;
	}

	/**
	 * Gets the role's custom dashboard, if set.
	 *
	 * @since 2.0.0
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
		 * @since 2.0.0
		 */
		$dashboard = apply_filters( 'cd_role_dashboard', $results['dashboard'] );

		return $dashboard;
	}

	/**
	 * Updates or adds a role customizations.
	 *
	 * @since 2.0.0
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
		 * @since 2.0.0
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
	 * @since 2.0.0
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

	/**
	 * Totally empty customizations table
	 *
	 * @since 2.0.0
	 */
	public static function delete_everything() {

		global $wpdb;

		$wpdb->query( "TRUNCATE TABLE `{$wpdb->prefix}cd_customizations`" );
	}
}

/**
 * Quick access to database class.
 *
 * @since 2.0.0
 */
function ClientDashDB() {

	return ClientDash()->db;
}