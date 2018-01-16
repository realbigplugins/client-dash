<?php
/**
 * Class ClientDash_Settings_API
 */

/**
 * Class ClientDash_Settings_API
 *
 * Exists only for the legacy extension API.
 *
 * @deprecated
 */
abstract class ClientDash_Settings_API {

	/**
	 * Adds a content section.
	 *
	 * @deprecated
	 */
	public function add_content_section( $section ) {
	}

	/**
	 * Opens a WP standard form table.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @return string HTML for opening a standard WP form table.
	 */
	public static function open_form_table() {
		return '';
	}

	/**
	 * Closes a WP standard form table.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @return string HTML for closing a standard WP form table.
	 */
	public static function close_form_table() {
		return '';
	}

	/**
	 * Outputs and manages a custom text field.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The name of the field.
	 * @param string $title The title of the field.
	 * @param array $atts Extra html atts to add.
	 *
	 * @return string The text field html.
	 */
	public static function text_field( $ID, $field, $title = '', $atts = array() ) {
		return '';
	}

	/**
	 * Outputs and manages a custom checkbox field.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The name of the field.
	 * @param string $title The title of the field.
	 * @param array $atts Extra html atts to add.
	 *
	 * @return string The checkbox field html.
	 */
	public static function checkbox_field( $ID, $field, $title, $atts = array() ) {
		return '';
	}

	/**
	 * Outputs and manages a custom text area field.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The name of the field.
	 * @param string $title The title of the field.
	 * @param array $atts Extra html atts to add.
	 *
	 * @return string The textarea field html.
	 */
	public static function textarea_field( $ID, $field, $title, $atts = array() ) {
		return '';
	}

	/**
	 * Outputs and manages a custom select box field.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The name of the field.
	 * @param string $title The title of the field.
	 * @param array $options The select box options.
	 * @param array $atts Extra html atts to add.
	 *
	 * @return string The select box field html.
	 */
	public static function select_field( $ID, $field, $title, array $options, array $atts = array() ) {
		return '';
	}

	/**
	 * Returns the CD standards field ID.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The ID of the field with the given ID and field name.
	 */
	public static function get_field_ID( $ID, $field ) {
		return '';
	}

	/**
	 * Returns the CD standards field name.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The name of the field with the given ID and field name.
	 */
	public static function get_field_name( $ID, $field ) {
		return '';
	}

	/**
	 * Returns the supplied field value.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The supplied field value.
	 */
	public static function get_field( $ID, $field ) {
		return '';
	}

	/**
	 * Registers a WP setting but does so with the proper field name.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $option_group The option group.
	 * @param string $ID The ID of the settings area.
	 * @param string $field The field name.
	 */
	public static function register_field( $option_group, $ID, $field ) {
	}
}