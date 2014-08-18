<?php

/**
 * Class ClientDash_AJAX
 *
 * Adds all AJAX functionality to Client Dash
 *
 * @package WordPress
 * @subpackage Client Dash
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

		add_action( 'wp_ajax_cd_reset_roles', array( $this, 'cd_reset_roles' ) );

		add_action( 'wp_ajax_cd_reset_all_settings', array( $this, 'cd_reset_all_settings' ) );
	}

	/**
	 * Resets all of the roles settings to default.
	 *
	 * @since Client Dash 1.5
	 */
	public function cd_reset_roles() {

		foreach ( $this->core_widgets as $page ) {
			delete_option( "cd_hide_page_$page" );
		}
		delete_option( 'cd_content_sections_roles' );
		echo 'Roles successfully reset!';

		die();
	}

	public function cd_reset_all_settings() {

		foreach ( $this->option_defaults as $name => $value ) {
			delete_option( "cd_$name" );
		}

		echo 'Settings successfully reset!';

		die();
	}
}

new ClientDash_AJAX();