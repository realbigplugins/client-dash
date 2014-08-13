<?php

/**
 * Class ClientDash_RequireFiles
 *
 * This class requires all of the various files needed to make Client
 * Dash core run.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_RequireFiles extends ClientDash {

	/**
	 * Requires all necessary files for Client Dash.
	 *
	 * Also initiates all page and tab classes.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		// Require our AJAX file
		require_once( plugin_dir_path( __FILE__ ) . 'ajax.php' );

		// Require our deprecated file
		require_once( plugin_dir_path( __FILE__ ) . 'deprecated.php' );

		// Core page and tab files
		foreach ( $this->core_files as $page => $tabs ) {
			// Include page file
			require_once( plugin_dir_path( __FILE__ ) . 'pages/' . $page . '.php' );

			// Initiate the new page class to launch it
			$page_class = 'ClientDash_Page_' . $page;
			new $page_class;

			foreach ( $tabs as $tab ) {
				// Include tabs
				require_once( plugin_dir_path( __FILE__ ) . 'tabs/' . $page . '/' . $tab . '.php' );

				// Initiate the new tab class to launch it
				$tab_class = 'ClientDash_Core_Page_' . $page . '_Tab_' . $tab;
				new $tab_class;
			}
		}

		// Core widget files
		foreach ( $this->core_widgets as $widget ) {
			require_once( plugin_dir_path( __FILE__ ) . 'widgets/' . $widget . '.php' );

			// Initiate the new widget class to launch it
			$widget_class = 'ClientDash_Widget_' . $widget;
			new $widget_class;
		}
	}
}

// Initialize the class into nothing in order to run it and require all of
// the needed files
new ClientDash_RequireFiles();