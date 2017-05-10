<?php
/**
 * Installs the plugin.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Class ClientDash_Install
 *
 * Installs the plugin.
 *
 * @since {{VERSION}}
 */
class ClientDash_Install {

	/**
	 * Loads the install functions.
	 *
	 * @since {{VERSION}}
	 */
	static function install() {

		add_option( 'clientdash_db_version', '1.0.0' );

		self::setup_tables();
		self::setup_capabilities();
	}

	/**
	 * Sets up the tables.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @global wpdb $wpdb
	 */
	private static function setup_tables() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}cd_customizations (
		  role VARCHAR(100) NOT NULL UNIQUE,
		  menu LONGTEXT,
		  submenu LONGTEXT,
		  dashboard LONGTEXT,
		  cdpages LONGTEXT,
 		  PRIMARY KEY  (role)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Sets up custom capabilities
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private static function setup_capabilities() {

		$administrator = get_role( 'administrator' );

		$administrator->add_cap( 'customize_admin' );
	}
}