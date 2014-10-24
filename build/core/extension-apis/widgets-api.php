<?php

/**
 * Class ClientDash_Widget_API
 *
 * This class provides static functions to use within widget extensions.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Extensions
 *
 * @since Client Dash 1.6
 */
abstract class ClientDash_Widgets_API extends ClientDash_Functions {

	/**
	 * Opens a WP standard form table.
	 *
	 * @since Client Dash 1.6
	 *
	 * @return string HTML for opening a standard WP form table.
	 */
	public static function open_form_table() {
		return '<table class="form-table"><tbody>';
	}

	/**
	 * Closes a WP standard form table.
	 *
	 * @since Client Dash 1.6
	 *
	 * @return string HTML for closing a standard WP form table.
	 */
	public static function close_form_table() {
		return '</table></tbody>';
	}

	/**
	 * Outputs and manages a custom text field.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The name of the field.
	 * @param string $title The title of the field.
	 * @param array $atts Extra html atts to add.
	 *
	 * @return string The text field html.
	 */
	public static function text_field( $ID, $field, $title = '', $atts = array() ) {

		// Update and retrieve the new value if updating
		$value = self::_update_field( $ID, $field );

		// Get the value if not updating
		if ( $value === false ) {
			$field_name_array = self::_get_field_name_array( $ID, $field );
			$option           = get_option( $field_name_array[0] );
			$value            = $option ? $option[ $field_name_array[1] ] : '';
		}

		// Get the field name and ID
		$field_name = self::_get_field_name( $ID, $field );
		$field_ID   = self::get_field_ID( $ID, $field );

		// Get any extra classes
		if ( isset( $atts['class'] ) ) {
			$classes = explode( ' ', $atts['class'] );
		}

		$html = '';

		$html .= '<p>';

		$html .= "<label for='$field_ID'>";
		$html .= $title;
		$html .= '</label>';

		$html .= '<br/>';

		$html .= "<input ";
		$html .= "type='text' ";
		$html .= "id='$field_ID' ";
		$html .= "name='" . $field_name . "' ";

		// Add the base class as well as any extra that may have been added
		$html .= "class='cd-widget-text-field";
		if ( isset( $classes ) ) {
			foreach ( $classes as $class ) {
				$html .= " $class";
			}
		}
		$html .= "' ";

		$html .= "value='$value' ";

		// Cycle through any extra atts and output them (except class, that's handled above)
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $att => $att_value ) {
				$html .= $att != 'class' ? "$att=\"$att_value\" " : '';
			}
		}

		$html .= '/>';

		$html .= '</p>';

		return $html;
	}

	/**
	 * Outputs and manages a custom checkbox field.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The name of the field.
	 * @param string $title The title of the field.
	 * @param array $atts Extra html atts to add.
	 *
	 * @return string The checkbox field html.
	 */
	public static function checkbox_field( $ID, $field, $title, $atts = array() ) {

		// Update and retrieve the new value if updating
		$value = self::_update_field( $ID, $field );

		// Get the value if not updating
		if ( $value === false ) {
			$field_name_array = self::_get_field_name_array( $ID, $field );
			$option           = get_option( $field_name_array[0] );
			$value            = $option ? $option[ $field_name_array[1] ] : '';
		}

		// Get the field name and ID
		$field_name = self::_get_field_name( $ID, $field );
		$field_ID   = self::get_field_ID( $ID, $field );

		// Get any extra classes
		if ( isset( $atts['class'] ) ) {
			$classes = explode( ' ', $atts['class'] );
		}

		$checked = isset( $value ) ? 'checked' : '';

		$html = '';

		$html .= '<p>';

		$html .= "<label for='$field_ID'>";
		$html .= $title;
		$html .= '</label>';

		$html .= ' '; // Intentional space for proper spacing

		$html .= "<input ";
		$html .= "type='checkbox' ";
		$html .= "id='$field_ID' ";
		$html .= "name='" . $field_name . "' ";

		// Add the base class as well as any extra that may have been added
		$html .= "class='cd-widget-checkbox-field";
		if ( isset( $classes ) ) {
			foreach ( $classes as $class ) {
				$html .= " $class";
			}
		}
		$html .= "' ";

		$html .= "value='1' ";

		// Cycle through any extra atts and output them (except class, that's handled above)
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $att => $att_value ) {
				$html .= $att != 'class' ? "$att=\"$att_value\" " : '';
			}
		}

		$html .= $checked;

		$html .= '/>';

		$html .= '</p>';

		return $html;
	}

	/**
	 * Outputs and manages a custom text area field.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The name of the field.
	 * @param string $title The title of the field.
	 * @param array $atts Extra html atts to add.
	 *
	 * @return string The textarea field html.
	 */
	public static function textarea_field( $ID, $field, $title, $atts = array() ) {

		// Update and retrieve the new value if updating
		$value = self::_update_field( $ID, $field );

		// Get the value if not updating
		if ( $value === false ) {
			$field_name_array = self::_get_field_name_array( $ID, $field );
			$option           = get_option( $field_name_array[0] );
			$value            = $option ? $option[ $field_name_array[1] ] : '';
		}

		// Get the field name and ID
		$field_name = self::_get_field_name( $ID, $field );
		$field_ID   = self::get_field_ID( $ID, $field );

		// Get any extra classes
		if ( isset( $atts['class'] ) ) {
			$classes = explode( ' ', $atts['class'] );
		}

		$html = '';

		$html .= '<p>';

		$html .= "<label for='$field_ID'>";
		$html .= $title;
		$html .= '</label>';

		$html .= '<br/>';

		$html .= "<textarea ";
		$html .= "id='$field_ID' ";
		$html .= "name='" . $field_name . "' ";

		// Add the base class as well as any extra that may have been added
		$html .= "class='cd-widget-textarea-field";
		if ( isset( $classes ) ) {
			foreach ( $classes as $class ) {
				$html .= " $class";
			}
		}
		$html .= "' ";

		// Cycle through any extra atts and output them (except class, that's handled above)
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $att => $att_value ) {
				$html .= $att != 'class' ? "$att=\"$att_value\" " : '';
			}
		}

		$html .= '>';

		$html .= $value;

		$html .= '</textarea>';

		$html .= '</p>';

		return $html;
	}

	/**
	 * Outputs and manages a custom select box field.
	 *
	 * @since Client Dash 1.6
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

		// Update and retrieve the new value if updating
		$value = self::_update_field( $ID, $field );

		// Get the value if not updating
		if ( $value === false ) {
			$field_name_array = self::_get_field_name_array( $ID, $field );
			$option           = get_option( $field_name_array[0] );
			$value            = $option ? $option[ $field_name_array[1] ] : '';
		}

		// Get the field name and ID
		$field_name = self::_get_field_name( $ID, $field );
		$field_ID   = self::get_field_ID( $ID, $field );

		// Get any extra classes
		if ( isset( $atts['class'] ) ) {
			$classes = explode( ' ', $atts['class'] );
		}

		$html = '';

		$html .= '<p>';

		$html .= "<label for='$field_ID'>";
		$html .= $title;
		$html .= '</label>';

		$html .= '<br/>';

		$html .= "<select ";
		$html .= "id='$field_ID' ";
		$html .= "name='" . $field_name . "' ";

		// Add the base class as well as any extra that may have been added
		$html .= "class='cd-widget-text-field";
		if ( isset( $classes ) ) {
			foreach ( $classes as $class ) {
				$html .= " $class";
			}
		}
		$html .= "' ";

		// Cycle through any extra atts and output them (except class, that's handled above)
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $att => $att_value ) {
				$html .= $att != 'class' ? "$att=\"$att_value\" " : '';
			}
		}

		$html .= '>';

		// Cycle through all options and output them
		foreach ( $options as $option_name => $option_value ) {

			$selected = $value && $value == $option_value ? 'selected' : '';

			$html .= "<option value='$option_value' $selected>$option_name</option>";
		}

		$html .= '</select>';

		$html .= '</p>';

		return $html;
	}

	/**
	 * Returns the CD standards field name, and also takes care of updating.
	 *
	 * If you need to create an input field by hand (without using the supplied functions), then use
	 * this function for the name attr so that updating is managed by CD.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The name of the field with the given ID and field name.
	 */
	public static function get_field_name( $ID, $field ) {

		self::_update_field( $ID, $field, true );

		return self::_get_field_name( $ID, $field );
	}

	/**
	 * Returns the CD standards field ID.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The ID of the field with the given ID and field name.
	 */
	public static function get_field_ID( $ID, $field ) {

		return "cd-custom-widget-{$ID}-{$field}-" . self::_get_unique_ID();
	}

	/**
	 * Returns the supplied field value.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The supplied field value.
	 */
	public static function get_field( $ID, $field ) {

		$field_name_array = self::_get_field_name_array( $ID, $field );
		$option           = get_option( $field_name_array[0] );
		$value            = $option[ $field_name_array[1] ];

		return $value;
	}

	public static function register_field( $option_group, $ID, $field ) {

		register_setting( $option_group, self::_get_field_name( $ID, $field ) );
	}

	/**
	 * Generates and returns a unique ID to be used for input labels and ID's.
	 *
	 * @since Client Dash 1.6
	 *
	 * @return string The unique ID.
	 */
	private static function _get_unique_ID() {

		return wp_create_nonce( rand() );
	}

	/**
	 * Returns the CD standards field name with no updating.
	 *
	 * @access Private.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The name of the field with the given ID and field name.
	 */
	private static function _get_field_name( $ID, $field ) {

		return "cd_custom_widget_{$ID}[$field]";
	}

	/**
	 * Returns the CD standards field name in an array with 0 being the option name and
	 * 1 being the specific field, with no updating.
	 *
	 * @access Private.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The name of the field with the given ID and field name.
	 */
	private static function _get_field_name_array( $ID, $field ) {

		return array( "cd_custom_widget_$ID", $field );
	}

	/**
	 * Updates the supplied field.
	 *
	 * @access Private.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The updated value.
	 */
	private static function _update_field( $ID, $field ) {

		$field_name       = self::_get_field_name( $ID, $field );
		$field_name_array = self::_get_field_name_array( $ID, $field );

		// Either update the option if set, or delete it otherwise
		if ( isset( $_POST[ $field_name_array[0] ] ) ) {

			// Get the current option and post field
			$option     = get_option( $field_name_array[0] );
			$post_field = $_POST[ $field_name_array[0] ];

			// If the option is not yet set
			if ( ! $option ) {

				// If this array key no longer exists (checkboxes), delete it entirely
				if ( ! array_key_exists( $field_name_array[1], $post_field ) ) {
					delete_option( $field_name );

					return null;
				}

				// Otherwise, just update it with the new post field
				update_option( $field_name_array[0], $post_field );

				return $post_field[ $field_name_array[1] ];
			} else {

				// If the option is set and this specific array key isn't set (checkboxes), delete
				// it from the array
				if ( ! array_key_exists( $field_name_array[1], $post_field ) ) {
					unset( $option[ $field_name_array[1] ] );
					update_option( $field_name_array[0], $option );

					return null;
				}

				// Otherwise, just add it and update it!
				$option[ $field_name_array[1] ] = $post_field[ $field_name_array[1] ];
				update_option( $field_name_array[0], $option );

				return $post_field[ $field_name_array[1] ];
			}
		} else {
			return false;
		}
	}
}