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
			update_option( "cd_hide_page_$page", $this->option_defaults["hide_page_$page"] );
		}
		update_option( 'cd_content_sections_roles', $this->option_defaults['content_sections_roles'] );
		echo 'Roles successfully reset!';

		die();
	}

	public function cd_reset_all_settings() {

		foreach ( $this->option_defaults as $name => $value ) {
			// If the default value is "null", then just delete it
			if ( $value == null ) {
				delete_option( "cd_$name" );
			} else {
				update_option( "cd_$name", $value );
			}
		}

		echo 'Settings successfully reset!';

		die();
	}
}

new ClientDash_AJAX();