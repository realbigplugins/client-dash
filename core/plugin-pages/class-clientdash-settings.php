<?php
/**
 * Handles all plugin settings.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class ClientDash_Settings
 *
 * Handles all plugin settings.
 *
 * @since {{VERSION}}
 */
class ClientDash_Settings {

	/**
	 * ClientDash_Settings constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Registers settings.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function register_settings() {

		register_setting( 'clientdash_settings', 'cd_adminpage_feed_url' );
		register_setting( 'clientdash_settings', 'cd_adminpage_feed_count' );

		register_setting( 'clientdash_admin_page', 'cd_adminpage_content' );

		add_settings_section(
			'admin_page_feed',
			__( 'Admin Page Feed', 'client-dash' ),
			null,
			'clientdash_settings'
		);

		add_settings_section(
			'other',
			__( 'Other', 'client-dash' ),
			null,
			'clientdash_settings'
		);

		$this->add_settings_fields();
	}

	/**
	 * Adds settings fields.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	private function add_settings_fields() {

		$settings = array();

		$settings['admin_page_feed'] = array(
			array(
				'id'       => 'adminpage_feed_url',
				'label'    => __( 'Feed URL', 'client-dash' ) .
				              cd_get_field_tip( __( 'RSS feed url that will be used on the custom Admin Page', 'client-dash' ) ),
				'callback' => 'cd_do_field_text',
				'args'     => array(
					'option_field' => true,
					'input_class'  => 'regular-text',
				),
			),
			array(
				'id'       => 'adminpage_feed_count',
				'label'    => __( 'Feed Count', 'client-dash' ) .
				              cd_get_field_tip( __( 'Number of items to display in the feed', 'client-dash' ) ),
				'callback' => 'cd_do_field_number',
				'args'     => array(
					'option_field' => true,
				),
			),
		);

		$enable_customize_tutorial_link = admin_url( 'admin.php?page=clientdash_settings&cd_enable_customize_tutorial' );
		$reset_settings_link            = admin_url( 'admin.php?page=clientdash_settings&cd_reset_settings' );

		$settings['other'] = array(
			array(
				'id'       => 'enable_customize_tutorial',
				'label'    => __( 'Enable Customize Admin Tutorial', 'client-dash' ) .
				              cd_get_field_tip( __( 'Once you hide or complete the tutorial for the "Customize Admin" tool, it will disappear. Click this button to enable it again', 'client-dash' ) ),
				'callback' => 'cd_do_field_html',
				'args'     => array(
					'html' =>
						"<a href=\"{$enable_customize_tutorial_link}\" class=\"button\">" .
						__( 'Enable', 'client-dash' ) .
						'</a>',
				),
			),
			array(
				'id'       => 'reset_all_settings',
				'label'    => __( 'Reset All Settings', 'client-dash' ),
				'callback' => 'cd_do_field_html',
				'args'     => array(
					'html' =>
						"<a href=\"{$reset_settings_link}\" class=\"button\">" .
						__( 'Reset', 'client-dash' ) .
						'</a>',
				),
			),
		);

		/**
		 * All plugin settings fields displayed on the setting page.
		 *
		 * @since {{VERSION}}
		 */
		$settings = apply_filters( 'cd_settings_fields', $settings );

		foreach ( $settings as $section => $page_settings ) {

			foreach ( $page_settings as $setting ) {

				add_settings_field(
					$setting['id'],
					$setting['label'],
					$setting['callback'],
					'clientdash_settings',
					$section,
					array_merge(
						array(
							'option_field' => true,
							'name'         => $setting['id'],
						),
						$setting['args']
					)
				);
			}
		}
	}
}