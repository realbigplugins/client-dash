<?php

/**
 * Used in some updates to make sure the DB is backwards compatible.
 *
 * @since 1.6.8
 */

// Only launch the update script once
if ( ! get_option( 'cd_update_1.6.8' ) ) {
	update_option( 'cd_update_1.6.8', '1' );

	// UPDATE SCRIPT

	// 1.6.8

	// Force require dashboard widget update
	update_option( 'cd_active_plugins', 'UPDATE' );
}