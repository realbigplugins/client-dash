<?php

/**
 * Class ClientDash_DEV_Database
 *
 * Used for the development version of Client Dash to change things that will only be used when developing.
 */
class ClientDash_DEV_Database {

	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'add_table_to_wpdb' ), 1 );
		add_action( 'switch_blog', array( $this, 'add_table_to_wpdb' ) );

		add_action( 'update_option', array( $this, 'update_option' ), 10, 3 );
		add_action( 'add_option', array( $this, 'add_option' ), 10, 2 );
		add_filter( 'cd_get_option', array( $this, 'get_option' ), 10, 2 );
	}

	public function get_option( $value, $option, $default = '' ) {

		global $wpdb;

		if ( ! isset( $wpdb->cd_development ) ) {
			self::add_table_to_wpdb();
		}

		if ( ! self::option_is_cd( $option ) ) {
			return $value;
		}

		$alloptions = wp_load_alloptions();

		if ( isset( $alloptions[$option] ) ) {
			$value = $alloptions[$option];
			return $value;
		} else {
			$value = wp_cache_get( $option, 'options' );

			if ( false === $value ) {
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->cd_development WHERE option_name = %s LIMIT 1", $option ) );

				// Has to be get_row instead of get_var because of funkiness with 0, false, null values
				if ( is_object( $row ) ) {
					$value = $row->option_value;
					return $value;
				} else { // option does not exist, so we must cache its non-existence
					return $default;
				}
			}
		}
	}

	public function add_option( $option, $value ) {

		global $wpdb;

		if ( ! isset( $wpdb->cd_development ) ) {
			self::add_table_to_wpdb();
		}

		if ( ! self::option_is_cd( $option ) ) {
			return $value;
		}

		$serialized_value = maybe_serialize( $value );
		$autoload = 'yes';

		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO `$wpdb->cd_develop` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)", $option, $serialized_value, $autoload ) );
	}

	public function update_option( $option, $old_value, $value ) {

		global $wpdb;

		if ( ! isset( $wpdb->cd_development ) ) {
			self::add_table_to_wpdb();
		}

		if ( ! self::option_is_cd( $option ) ) {
			return $value;
		}

		$serialized_value = maybe_serialize( $value );
		$result = $wpdb->update( $wpdb->cd_develop, array( 'option_value' => $serialized_value ), array( 'option_name' => $option ) );

		if ( ! $result ) {
			return false;
		}

		return true;
	}

	public static function create_table() {

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;
		global $charset_collate;
		self::add_table_to_wpdb();

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		$sql = "CREATE TABLE {$wpdb->cd_develop} (
				option_id bigint(20) unsigned NOT NULL auto_increment,
				option_name varchar(64) NOT NULL default '',
				option_value longtext NOT NULL,
				PRIMARY KEY  (option_id),
				UNIQUE KEY option_name (option_name)
				) $charset_collate";

		dbDelta( $sql );
	}

	private static function option_is_cd( $option) {

		if ( substr( $option, 0, 3 ) === 'cd_' || $option === 'sidebars_widgets' || substr( $option, 0, 7 ) === 'widget_' ) {
			return true;
		}

		return false;
	}

	public static function add_table_to_wpdb() {

		global $wpdb;

		$wpdb->cd_develop = "{$wpdb->prefix}cd_develop";
	}
}

new ClientDash_DEV_Database();