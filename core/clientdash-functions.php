<?php
/**
 * Helper functions.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Gets customizations.
 *
 * Wrapper for ClientDashDB()::get_customizations()
 *
 * @since {{VERSION}}
 *
 * @param string $role Customizations role.
 */
function cd_get_customizations( $role ) {

	return ClientDash_DB::get_customizations( $role );
}

/**
 * Gets the role's custom menu, if set.
 *
 * Wrapper for ClientDashDB()::get_role_menus()
 *
 * @since {{VERSION}}
 *
 * @param string $role Role to get menu for.
 *
 * @return array|bool|mixed|void
 */
function cd_get_role_menus( $role ) {

	return ClientDash_DB::get_role_menu( $role );
}

/**
 * Gets the role's custom dashboard, if set.
 *
 * Wrapper for ClientDashDB()::get_role_dashboard()
 *
 * @since {{VERSION}}
 *
 * @param string $role Role to get dashboard for.
 *
 * @return array|bool|mixed|void
 */
function cd_get_role_dashboard( $role ) {

	return ClientDash_DB::get_role_dashboard( $role );
}

/**
 * Updates or adds a role customizations.
 *
 * Wrapper for ClientDashDB()::update_role_customizations()
 *
 * @since {{VERSION}}
 *
 * @param string $role
 * @param array $data
 *
 * @return array|null|object|void
 */
function cd_update_role_customizations( $role, $data ) {

	return ClientDash_DB::update_customizations( $role, $data );
}

/**
 * Deletes customizations.
 *
 * Wrapper for ClientDashDB()::delete_customizations()
 *
 * @since {{VERSION}}
 *
 * @param string $role Customizations role.
 */
function cd_delete_customizations( $role ) {

	return ClientDash_DB::delete_customizations( $role );
}

/**
 * Searches an array by a nested key and returns the match.
 *
 * @param array $array
 * @param string $key
 * @param string $value
 *
 * @return bool|mixed
 */
function cd_array_get_index_by_key( $array, $key, $value ) {

	foreach ( $array as $i => $array_item ) {

		if ( ! isset( $array_item[ $key ] ) ) {

			continue;
		}

		if ( $array_item[ $key ] === $value ) {

			return $i;
		}
	}

	return false;
}

/**
 * Searches an array by a nested key and returns the match.
 *
 * @since {{VERSION}}
 *
 * @param array $array
 * @param string $key
 * @param string $value
 *
 * @return bool|mixed
 */
function cd_array_search_by_key( $array, $key, $value ) {

	$found_key = cd_array_get_index_by_key( $array, $key, $value );

	if ( $found_key !== false ) {

		return $array[ $found_key ];
	}

	return false;
}

/**
 * Adds a core CD page.
 *
 * @since {{VERSION}}
 *
 * @param array $page
 */
function cd_add_core_page( $page ) {

	global $clientdash_pages;
}

/**
 * Returns the core CD pages.
 *
 * @since {{VERSION}}
 */
function cd_get_core_pages() {

	$pages = ClientDash_Core_Pages::get_pages();

	return $pages;
}

/**
 * Loads a template file from the theme if it exists, otherwise from the plugin.
 *
 * @since {{VERSION}}
 *
 * @param string $template Template file to load.
 *
 * @return string File to load
 */
function cd_get_template( $template ) {

	/**
	 * Filter the template to be located.
	 *
	 * @since {{VERSION}}
	 */
	$template = apply_filters( 'cd_get_template', $template );

	$template_file = locate_template( array( "/client-dash/{$template}" ) );

	if ( $template_file ) {

		return $template_file;

	} else {

		return CLIENTDASH_DIR . "templates/{$template}";
	}
}

/**
 * Loads a template.
 *
 * @since {{VERSION}}
 *
 * @param string $template Template file to load.
 * @param array $args Arguments to extract for the template.
 */
function cd_template( $template, $args = array() ) {

	/**
	 * Filter the args to use in the template.
	 *
	 * @since {{VERSION}}
	 */
	$args = apply_filters( 'cd_get_template_args', $args, $template );

	extract( $args );

	include cd_get_template( $template );
}

/**
 * Takes an ID and determines if it's a Client Dash core page.
 *
 * @since {{VERSION}}
 *
 * @param string $ID Menu/page ID.
 *
 * return bool
 */
function cd_is_core_page( $ID ) {

	return in_array( $ID, array(
		'cd_account',
		'cd_reports',
		'cd_help',
		'cd_admin_page',
	) );
}


/**
 * Gets the size of a directory on the server.
 *
 * @since 1.1.0
 *
 * @param $path
 *
 * @return mixed
 */
function cd_get_dir_size( $path ) {

	$totalsize  = 0;
	$totalcount = 0;
	$dircount   = 0;
	if ( $handle = opendir( $path ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			$nextpath = $path . '/' . $file;
			if ( $file != '.' && $file != '..' && ! is_link( $nextpath ) ) {
				if ( is_dir( $nextpath ) ) {
					$dircount ++;
					$result = cd_get_dir_size( $nextpath );
					$totalsize += $result['size'];
					$totalcount += $result['count'];
					$dircount += $result['dircount'];
				} elseif ( is_file( $nextpath ) ) {
					$totalsize += filesize( $nextpath );
					$totalcount ++;
				}
			}
		}
	}
	closedir( $handle );
	$total['size']     = $totalsize;
	$total['count']    = $totalcount;
	$total['dircount'] = $dircount;

	return $total;
}

/**
 * Correctly formats the bytes size into a more readable size.
 *
 * @since 1.1.0
 *
 * @param int $size Size in bytes
 *
 * @return string
 */
function cd_format_dir_size( $size ) {

	if ( $size < 1024 ) {
		return $size . " bytes";
	} else if ( $size < ( 1024 * 1024 ) ) {
		$size = round( $size / 1024, 1 );

		return $size . " KB";
	} else if ( $size < ( 1024 * 1024 * 1024 ) ) {
		$size = round( $size / ( 1024 * 1024 ), 1 );

		return $size . " MB";
	} else {
		$size = round( $size / ( 1024 * 1024 * 1024 ), 1 );

		return $size . " GB";
	}
}

/**
 * Resets ALL Client Dash settings.
 *
 * @since {{VERSION}}
 *
 * @global WPDB $wpdb
 */
function cd_reset_all_settings() {

	global $wpdb;

	// Totally empty customizations table
	$wpdb->query("TRUNCATE TABLE `{$wpdb->prefix}cd_customizations`");

	/**
	 * Fires during Client Dash settings reset.
	 *
	 * @since {{VERSION}}
	 *
	 * @hooked ClientDash_PluginPages::reset_admin_page 10
	 */
	do_action( 'clientdash_reset_all_settings' );
}