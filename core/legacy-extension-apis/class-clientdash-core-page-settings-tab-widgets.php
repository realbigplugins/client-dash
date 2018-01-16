<?php
/**
 * Legacy class.
 *
 * @deprecated.
 */

/**
 * Class ClientDash_Core_Page_Settings_Tab_Widgets
 *
 * Legacy class for old extension api.
 *
 * @deprecated
 */
class ClientDash_Core_Page_Settings_Tab_Widgets {

	/**
	 * Widgets to add.
	 *
	 * @deprecated
	 *
	 * @var array
	 */
	public $widgets = array();

	/**
	 * Registers a widget.
	 *
	 * @deprecated
	 */
	public function register_widget( $widget ) {

		if ( ! has_filter( 'wp_dashboard_setup', array( $this, 'add_widgets' ) ) ) {

			add_action( 'wp_dashboard_setup', array( $this, 'add_widgets' ) );
		}

		$this->widgets[] = $widget;
	}

	/**
	 * Adds widgets.
	 *
	 * @deprecated
	 */
	function add_widgets() {

		foreach ( $this->widgets as $widget ) {

			wp_add_dashboard_widget( $widget['id'], $widget['title'], $widget['callback'] );
		}
	}
}