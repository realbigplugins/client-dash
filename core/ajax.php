<?php

/**
 * Class ClientDash_AJAX
 *
 * Adds all AJAX functionality to Client Dash
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @since Client Dash 1.5
 */
class ClientDash_AJAX extends ClientDash {

	/**
	 * Constructs the class.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		add_action( 'wp_ajax_cd_reset_roles', array( $this, 'reset_roles' ) );

		add_action( 'wp_ajax_cd_reset_all_settings', array( $this, 'reset_all_settings' ) );

		add_action( 'wp_ajax_cd_reset_admin_menu', array( $this, 'reset_admin_menu' ) );
	}

	/**
	 * Resets all of the roles settings to default.
	 *
	 * @since Client Dash 1.5
	 */
	public function reset_roles() {

		foreach ( $this->core_widgets as $page ) {
			update_option( "cd_hide_page_$page", $this->option_defaults["hide_page_$page"] );
		}
		update_option( 'cd_content_sections_roles', $this->option_defaults['content_sections_roles'] );
		echo 'Roles successfully reset!';

		die();
	}

	public function reset_all_settings() {

		// Cycle through all option defaults and delete them
		foreach ( $this->option_defaults as $name => $value ) {
			delete_option( "cd_$name" );
		}

		// Remove the modified nav menu
		wp_delete_nav_menu( 'cd_admin_menu' );

		echo 'Settings successfully reset!';

		die();
	}

	public function reset_admin_menu() {

		$roles = get_editable_roles();

		foreach ( $roles as $role_name => $role ) {

			// Remove the modified nav menu
			wp_delete_nav_menu( "cd_admin_menu_$role_name" );

			// Delete the option
			delete_option( "cd_admin_menu_{$role_name}_modified" );
		}

		// TODO Remove
		update_option( 'cd_testing_cron', 'IT HASN\'T RAN' );

		echo 'Admin menu successfully reset!';

		die();
	}
}

new ClientDash_AJAX();