<?php

/**
 * Class ClientDash_Settings_API
 *
 * This class provides static functions to use within settings in extensions.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Extensions
 *
 * @since Client Dash 1.6
 */
abstract class ClientDash_Settings_API extends ClientDash_Functions {

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

		// Get the value
		$value = self::get_field( $ID, $field );

		// Get the field name and ID
		$field_name = self::get_field_name( $ID, $field );
		$field_ID   = self::get_field_ID( $ID, $field );

		// Get any extra classes
		if ( isset( $atts['class'] ) ) {
			$classes = explode( ' ', $atts['class'] );
		}

		$html = '';

		$html .= '<tr valign="top">';

		$html .= '<th>';

		$html .= "<label for='$field_ID'>";
		$html .= $title;
		$html .= '</label>';

		$html .= '</th>';

		$html .= '<td>';

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

		$html .= '</td>';

		$html .= '</tr>';

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

		// Get the value
		$value = self::get_field( $ID, $field );

		// Get the field name and ID
		$field_name = self::get_field_name( $ID, $field );
		$field_ID   = self::get_field_ID( $ID, $field );

		// Get any extra classes
		if ( isset( $atts['class'] ) ) {
			$classes = explode( ' ', $atts['class'] );
		}

		$checked = $value === '1' ? 'checked' : '';

		$html = '';

		$html .= '<tr valign="top">';

		$html .= '<th>';

		$html .= "<label for='$field_ID'>";
		$html .= $title;
		$html .= '</label>';

		$html .= '</th>';

		$html .= '<td>';

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

		$html .= '</td>';

		$html .= '</tr>';

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

		// Get the value
		$value = self::get_field( $ID, $field );

		// Get the field name and ID
		$field_name = self::get_field_name( $ID, $field );
		$field_ID   = self::get_field_ID( $ID, $field );

		// Get any extra classes
		if ( isset( $atts['class'] ) ) {
			$classes = explode( ' ', $atts['class'] );
		}

		$html = '';

		$html .= '<tr valign="top">';

		$html .= '<th>';

		$html .= "<label for='$field_ID'>";
		$html .= $title;
		$html .= '</label>';

		$html .= '</th>';

		$html .= '<td>';

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

		$html .= '</td>';

		$html .= '</tr>';

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

		// Get the value
		$value = self::get_field( $ID, $field );

		// Get the field name and ID
		$field_name = self::get_field_name( $ID, $field );
		$field_ID   = self::get_field_ID( $ID, $field );

		// Get any extra classes
		if ( isset( $atts['class'] ) ) {
			$classes = explode( ' ', $atts['class'] );
		}

		$html = '';

		$html .= '<tr valign="top">';

		$html .= '<th>';

		$html .= "<label for='$field_ID'>";
		$html .= $title;
		$html .= '</label>';

		$html .= '</th>';

		$html .= '<td>';

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

		$html .= '</td>';

		$html .= '</tr>';

		return $html;
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

		return "cd-custom-setting-{$ID}-{$field}";
	}

	/**
	 * Returns the CD standards field name.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The widget ID.
	 * @param string $field The field name.
	 *
	 * @return string The name of the field with the given ID and field name.
	 */
	public static function get_field_name( $ID, $field ) {

		return "cd_custom_setting_{$ID}_{$field}";
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

		return get_option( self::get_field_name( $ID, $field ) );
	}

	/**
	 * Registers a WP setting but does so with the proper field name.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $option_group The option group.
	 * @param string $ID The ID of the settings area.
	 * @param string $field The field name.
	 */
	public static function register_field( $option_group, $ID, $field ) {

		register_setting( $option_group, self::get_field_name( $ID, $field ) );
	}
}