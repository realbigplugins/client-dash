<?php

/**
 * Class CD_Widget
 *
 * The framework for creating a custom CD Widget. Extend this class to do so.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Widgets
 *
 * @since Client Dash 1.6
 */
class CD_Widget extends WP_Widget {

	/**
	 * The ID of the widget.
	 *
	 * @since Client Dash 1.6
	 */
	public $id = 'cd_widget';

	/**
	 * The title of the widget.
	 *
	 * @since Client Dash 1.6
	 */
	public $title = 'CD Widget';

	/**
	 * The description of the widget.
	 *
	 * @since Client Dash 1.6
	 */
	public $description = null;

	/**
	 * This where a widget can add extra fields to be saved and used.
	 *
	 * @since Client Dash 1.6
	 */
	public $extra_fields = array();

	/**
	 * The callback to use for the frontend output.
	 *
	 * Private.
	 *
	 * @since Client Dash 1.6
	 */
	public $_callback = false;

	/**
	 * The callback to use for the settings area.
	 *
	 * Private.
	 *
	 * @since Client Dash 1.6
	 */
	public $_settings_callback = false;

	/**
	 * Tells us whether or not the current widget is a part of CD Core.
	 *
	 * Private.
	 *
	 * @since Client Dash 1.6
	 */
	public $_cd_core = '0';

	/**
	 * Tells us whether or not the current widget is a CD extension.
	 *
	 * Private.
	 *
	 * @since Client Dash 1.6
	 */
	public $_cd_extension = '0';

	/**
	 * Tells us if this was a widget added by a plugin / theme / WP Core.
	 *
	 * Private.
	 *
	 * @since Client Dash 1.6
	 */
	public $_plugin = '0';

	/**
	 * Instantiates the parent class.
	 *
	 * @since Client Dash 1.6
	 */
	public function __construct() {

		global $ClientDash_Core_Page_Settings_Tab_Widgets;

		// Gather new properties
		foreach ( $ClientDash_Core_Page_Settings_Tab_Widgets->widgets as $i => $widget ) {

			if ( isset( $widget['completed'] ) ) {
				continue;
			}
			$ClientDash_Core_Page_Settings_Tab_Widgets->widgets[ $i ]['completed'] = true;

			$this->id                 = isset( $widget['id'] ) ? $widget['id'] : $this->id;
			$this->title              = isset( $widget['title'] ) ? $widget['title'] : $this->title;
			$this->description        = isset( $widget['description'] ) ? $widget['description'] : $this->description;
			$this->_settings_callback = isset( $widget['settings_callback'] ) ? $widget['settings_callback'] : $this->_settings_callback;
			$this->_cd_core           = isset( $widget['cd_core'] ) ? $widget['cd_core'] : $this->_cd_core;
			$this->_cd_extension      = isset( $widget['cd_extension'] ) ? $widget['cd_extension'] : $this->_cd_extension;
			$this->_plugin            = isset( $widget['plugin'] ) ? $widget['plugin'] : $this->_plugin;
		}

		// Account for CD Webmaster dynamic title
		if ( $this->title == 'Webmaster' && get_option( 'cd_webmaster_name') ) {
			$this->title = $this->title . ' (' . get_option( 'cd_webmaster_name', '' ) . ')';
		}

		// Instantiate the parent object
		parent::__construct( $this->id, $this->title, array( 'description' => $this->description ) );
	}

	/**
	 * Output for the widget settings area.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param array $instance The widget instance.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {

		global $ClientDash;

		// Don't show title if webmaster widget
		if ( $this->title != 'Webmaster' . ' (' . get_option( 'cd_webmaster_name', '' ) . ')'
		     && $this->title != 'Webmaster'
		) {

			// Title
			$title = isset( $instance['title'] ) ? $instance['title'] : '';
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title</label>
				<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" class="widefat"
				       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>"/>
			</p>
		<?php
		} else {
			$ClientDash::error_nag( 'Title set in <a href="' . $ClientDash::get_settings_url( 'webmaster' ) . '">Webmaster</a> page.' );
		}

		// Extra title input for use when outputting widgets. This is here because when initially creating
		// a widget, you don't have to supply a title. But if you don't, then this widget will have no title
		// associated with it when we try to use it on our dashboard. So I have an extra hidden field here
		// that will use the original title and provide a fallback.
		$_original_title = $this->title;
		echo '<input type="hidden" name="' . $this->get_field_name( '_original_title' ) . '" value="' . htmlspecialchars( esc_attr( $_original_title ) ) . '" />';

		// Do the settings callback if it's set
		$_settings_callback = isset( $instance['_settings_callback'] ) ? $instance['_settings_callback'] : $this->_settings_callback;
		if ( $_settings_callback ) {

			if ( isset( $instance['_settings_is_object'] ) ) {
				$object = new $_settings_callback[0];

				call_user_func( array( $object, $_settings_callback[1] ) );
			} else {
				call_user_func( $_settings_callback, $this->id );
			}
		}

		// The settings callback (private)
		if ( is_array( $_settings_callback ) ) {

			if ( is_object( $_settings_callback[0] ) ) {
				$_settings_callback[0] = get_class( $_settings_callback[0] );
				echo "<input type='hidden' name='" . $this->get_field_name( '_settings_is_object' ) . "' value='1' />";
			}

			echo "<input type='hidden' name='" . $this->get_field_name( '_settings_callback' ) . "[0]' value='$_settings_callback[0]' />";
			echo "<input type='hidden' name='" . $this->get_field_name( '_settings_callback' ) . "[1]' value='$_settings_callback[1]' />";
		} else {
			echo "<input type='hidden' name='" . $this->get_field_name( '_settings_callback' ) . "' value='$_settings_callback' />";
		}

		// CD Core (private)
		echo '<input type="hidden" name="' . $this->get_field_name( '_cd_core' ) . "\" value='$this->_cd_core' />";

		// CD Extension (private)
		echo '<input type="hidden" name="' . $this->get_field_name( '_cd_extension' ) . "\" value='$this->_cd_extension' />";

		// Plugin (private)
		echo '<input type="hidden" name="' . $this->get_field_name( '_plugin' ) . "\" value='$this->_plugin' />";
	}

	/**
	 * Performed when hitting the save button and also when initially added. Saves new values.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param array $new_instance The new widget instance.
	 * @param array $old_instance The old widget instance.
	 *
	 * @return array The new widget instance.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		// Update fields
		$fields = array(
			'title',
			'_original_title',
			'_is_object',
			'_settings_callback',
			'_settings_callback[0]',
			'_settings_callback[1]',
			'_settings_is_object',
			'_cd_core',
			'_cd_extension',
			'_plugin',
		);
		foreach ( $fields as $field ) {

			if ( isset( $new_instance[ $field ] ) ) {
				$instance[ $field ] = $new_instance[ $field ];
			}
		}

		return $instance;
	}
}