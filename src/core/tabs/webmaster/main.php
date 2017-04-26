<?php

/**
 * Class ClientDash_Page_Webmaster_Tab_Main
 *
 * Adds the core content section for Webmaster -> Main.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Webmaster_Tab_Main extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		// Set the tab name to whatever the user set
		$tab_name = get_option( 'cd_webmaster_main_tab_name' );

		// Make sure the tab name isn't empty
		if ( empty( $tab_name ) ) {
			$tab_name = $this->option_defaults['webmaster_main_tab_name'];
		}

		$this->add_content_section( array(
			'name'     => __( 'Main', 'client-dash' ),
			'page'     => __( 'Webmaster', 'client-dash' ),
			'tab'      => $tab_name,
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		$content = get_option( 'cd_webmaster_main_tab_content' );
		$content = wpautop( $content );

		if ( ! empty( $content ) ) {

			echo $content;

		} else {

			$this->error_nag( sprintf(
				__( 'Please set content under Client Dash %ssettings%s.', 'client-dash' ),
				'<a href="' . $this->get_settings_url( 'webmaster' ) . '">',
				'</a>'
			), 'manage_options' );

			$this->error_nag(
				__( 'This tab has no content. If you believe this to be an error, please contact your system ' .
				    'administrator.', 'client-dash' )
			);
		}
	}
}