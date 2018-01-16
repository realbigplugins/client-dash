<?php
/**
 * Class ClientDash_Widgets_API
 */

/**
 * Class ClientDash_Widgets_API
 *
 * Exists only for the legacy extension API.
 *
 * @deprecated
 */
abstract class ClientDash_Widgets_API {

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
	 * Returns the CD standards field name, and also takes care of updating.
	 *
	 * If you need to create an input field by hand (without using the supplied functions), then use
	 * this function for the name attr so that updating is managed by CD.
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

	public static function register_field( $option_group, $ID, $field ) {
		return '';
	}

	/**
	 * Generates and returns a unique ID to be used for input labels and ID's.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @return string The unique ID.
	 */
	private static function _get_unique_ID() {
		return '';
	}

	/**
	 * Returns the CD standards field name with no updating.
	 *
	 * @access Private.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The name of the field with the given ID and field name.
	 */
	private static function _get_field_name( $ID, $field ) {
		return '';
	}

	/**
	 * Returns the CD standards field name in an array with 0 being the option name and
	 * 1 being the specific field, with no updating.
	 *
	 * @access Private.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The name of the field with the given ID and field name.
	 */
	private static function _get_field_name_array( $ID, $field ) {
		return '';
	}

	/**
	 * Updates the supplied field.
	 *
	 * @access Private.
	 *
	 * @since 1.6.0
	 * @deprecated
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The updated value.
	 */
	private static function _update_field( $ID, $field ) {
		return '';
	}
}