<?php

/**
 * Class ClientDash_Page_Settings_Tab_Widgets
 *
 * Adds the core content section for Settings -> Widgets.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Widgets
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_Widgets extends ClientDash {

	/**
	 * Available widgets.
	 *
	 * @since Client Dash 1.6
	 */
	public $widgets = array();

	/**
	 * The sidebars for the dashboard.
	 *
	 * @since Client Dash 1.6
	 */
	public $sidebars = array(
		array(
			'id'   => 'cd-dashboard',
			'name' => 'Dashboard',
		),
	);

	/**
	 * Whether or not the widgets section is currently active or not.
	 *
	 * @since Client Dash 1.6
	 */
	public static $_active = false;

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.6
	 */
	function __construct() {

		global $ClientDash;

		// Allow filtering of some properties
		add_action( 'admin_init', array( $this, 'filter_properties' ) );

		// Filter the sidebars_widgets option
		add_filter( 'pre_update_option_sidebars_widgets', array( $this, 'sync_widgets' ), 10, 2 );

		// Anything in here will ONLY apply to this particular settings page OR if there is a POST
		// value set of 'cd-widgets' (for when using AJAX)
		if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'cd_settings'
		       && isset( $_GET['tab'] ) && $_GET['tab'] == 'widgets'
		     ) || isset( $_POST['cd-widgets'] )
		) {

			// Set the widgets area to currently active
			$this->active = true;

			// Register a sidebar for each role
			add_action( 'admin_init', array( $this, 'register_sidebars' ), 10 );

			// Include widget interface
			include_once( $ClientDash->path . 'core/tabs/settings/widgets/widget-interface.php' );

			// Add default widgets to empty sidebars
			add_action( 'admin_init', array( $this, 'populate_sidebars' ), 11 );

			// Remove all existing widgets
			add_action( 'widgets_init', array( $this, 'remove_existing_widgets' ), 99 );

			// Create widgets from CD Core
			add_action( 'widgets_init', array( $this, 'create_cd_core_widgets' ), 100 );

			// Create widgets from existing dashboard widgets
			add_action( 'widgets_init', array( $this, 'create_existing_dashboard_widgets' ), 100 );

			// Necessary scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Add nav-menus body class for our custom CD page
			add_filter( 'admin_body_class', array( $this, 'add_nav_menu_class' ) );

			// Remove form wrap and submit button
			add_filter( 'cd_settings_form_wrap', '__return_false' );
			add_filter( 'cd_submit', '__return_false' );

			// Add extra field(s) to the widget form
			add_action( 'in_widget_form', array( $this, 'add_extra_fields' ), 10, 2 );
		}

		$this->add_content_section( array(
			'name'     => __( 'Core Settings Widgets', 'client-dash' ),
			'page'     => __( 'Settings', 'client-dash' ),
			'tab'      => __( 'Widgets', 'client-dash' ),
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * Houses some filters that allow modifications to the class.
	 *
	 * @since Client Dash 1.6.4
	 */
	public function filter_properties() {

		/**
		 * Allows the available sidebars in the CD Settings -> Widgets page to be modified.
		 *
		 * @since Client Dash 1.6.4
		 */
		$this->sidebars = apply_filters( 'cd_widget_sidebars', $this->sidebars );
	}

	/**
	 * Registers all sidebars for each role.
	 *
	 * @since Client Dash 1.6
	 */
	public function register_sidebars() {

		// Create a sidebar area for each sidebar that's been set
		foreach ( $this->sidebars as $sidebar ) {

			register_sidebar( array(
				'id'   => $sidebar['id'],
				'name' => $sidebar['name'],
			) );
		}
	}

	/**
	 * Populates the empty sidebars with default widgets.
	 *
	 * @since Client Dash 1.6
	 */
	public function populate_sidebars() {

		// Don't populate on AJAX
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX == true ) {
			return;
		}

		// Don't do this more than once
		if ( get_option( 'cd_populate_dashboard_widgets' ) ) {
			return;
		}
		update_option( 'cd_populate_dashboard_widgets', true );

		$active_widgets = get_option( 'sidebars_widgets' );

		$cd_widgets_update = array();

		// Cycle through each sidebar to populate
		$i      = 1;
		$update = false;
		foreach ( $this->sidebars as $sidebar ) {
			$i ++;

			// Pass over if widgets already have been added
			if ( ! empty( $active_widgets[ $sidebar['id'] ] ) ) {
				continue;
			}

			$update = true;

			// Add our CD Core widgets
			foreach ( self::$_cd_widgets as $widget_ID => $widget ) {

				$active_widgets[ $sidebar['id'] ][] = "$widget_ID-$i";

				$cd_widgets_update[ $widget_ID ][ $i ] = array(
					'_original_title' => self::$_cd_widgets[ $widget_ID ]['title'],
					'_callback'       => self::$_cd_widgets[ $widget_ID ]['callback'],
					'_cd_core'        => '1',
				);
			}
		}

		// Update options
		foreach ( $cd_widgets_update as $widget_ID => $widget ) {
			update_option( "widget_$widget_ID", $widget );
		}

		if ( $update ) {
			update_option( 'sidebars_widgets', $active_widgets );
		}

		// Redirect now that they're added (so they will show up)
		wp_redirect( $_SERVER["REQUEST_URI"] );
		exit();
	}

	/**
	 * Removes existing widgets.
	 *
	 * @since Client Dash 1.6
	 */
	public function remove_existing_widgets() {

		// Get the registered widgets, and if there are none, exit
		$widgets = ! empty( $GLOBALS['wp_widget_factory'] ) ? $GLOBALS['wp_widget_factory'] : false;
		if ( ! $widgets ) {
			return;
		}

		// Remove ALL but those that are explicitly stated as allowed
		foreach ( $widgets->widgets as $widget_class => $widget ) {

			/**
			 * Allows specific default available widgets to not be removed. Simply return false after
			 * checking if the current widget is the one you want to keep.
			 *
			 * @since Client Dash 1.6
			 */
			if ( ! apply_filters( 'cd_widgets_remove_default', true, $widget_class, $widget ) ) {
				return;
			}

			unregister_widget( $widget_class );
		}
	}

	/**
	 * Adds CD Core widgets to available widgets.
	 *
	 * @since Client Dash 1.6
	 */
	public function create_cd_core_widgets() {

		foreach ( self::$_cd_widgets as $widget_ID => $widget ) {

			/**
			 * Allows filtering of supplied values for CD Core available widgets.
			 *
			 * @since Client Dash 1.6
			 */
			$args = apply_filters( 'cd_widgets_available_cd_core', array(
				'id'          => $widget_ID,
				'title'       => $widget['title'],
				'description' => $widget['description'],
				'callback'    => $widget['callback'],
				'cd_core'     => '1',
			) );

			$this->register_widget( $args );
		}
	}

	/**
	 * Adds plugin / theme / WP Core widgets to available widgets.
	 *
	 * @since Client Dash 1.6
	 */
	public function create_existing_dashboard_widgets() {

		foreach ( get_option( 'cd_active_widgets', array() ) as $widget_ID => $widget ) {

			// Issue with html in quick press title
			if ( strpos( $widget['title'], __( 'Quick Draft', 'client-dash' ) ) !== false ) {
				$widget['title'] = __( 'Quick Draft', 'client-dash' );
			}

			/**
			 * Allows filtering of supplied values for plugin / theme / WP Core available widgets.
			 *
			 * @since Client Dash 1.6
			 */
			$args = apply_filters( 'cd_widgets_available_plugin', array(
				'id'          => $widget_ID,
				'title'       => $widget['title'],
				'description' => null,
				'plugin'      => '1',
			) );

			$this->register_widget( $args );
		}
	}

	/**
	 * Registers a widget, CD style.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param array $args Args for the available widget.
	 */
	public function register_widget( $args = array() ) {

		global $wp_widget_factory;

		// Only allow this function to fire if widgets are currently active
		if ( ! isset( $this->active ) || ! $this->active ) {
			return;
		}

		// Add our new widget to the array that the interface will be pulling from
		$this->widgets[] = $args;

		// Now add the widget into the WP Widget Factory
		$wp_widget_factory->widgets[ $args['id'] ] = new CD_Widget();
	}

	/**
	 * Include necessary scripts for the page.
	 *
	 * @since Client Dash 1.6
	 */
	public function enqueue_scripts() {

		$scripts = array(
			'admin-widgets',
			'jquery-ui-widgets',
			'jquery-ui-draggable',
			'jquery-ui-sortable',
			'jquery-effects-shake',
		);

		foreach ( $scripts as $script ) {
			wp_enqueue_script( $script );
		}
	}

	/**
	 * Adds the nav-menu body class (which is normally present on the nav-menus page and necessary).
	 *
	 * @since Client Dash 1.6
	 */
	public function add_nav_menu_class( $classes ) {
		return $classes . ' cd-widgets widgets-php';
	}

	/**
	 * Adds this extra field on initial saving of widgets so on AJAX loads, this class still gets loaded.
	 *
	 * @since Client Dash 1.6
	 */
	public function add_extra_fields() {

		echo '<input type="hidden" name="cd-widgets" value="1" />';
	}

	/**
	 * Makes sure sidebars don't get removed from this option.
	 *
	 * When on the CD widgets page, WP sidebars don't exist, and would normally get erased when updating this option.
	 * Vice versa also applies. So to keep them both in sync with each other, I simply merge them here.
	 *
	 * @since Client Dash 1.6.3
	 *
	 * @param mixed $sidebars The new option value.
	 * @param mixed $old_sidebars The old option value.
	 *
	 * @return array The new sidebars array.
	 */
	public function sync_widgets( $sidebars, $old_sidebars ) {

		/**
		 * Allows the disabling of this filter.
		 *
		 * @since Client Dash 1.6.3
		 */
		if ( ! apply_filters( 'cd_sync_widgets', true ) ) {
			return $sidebars;
		}

		// Get rid of the array version
		unset( $sidebars['array_version'] );
		unset( $old_sidebars['array_version'] );

		// If a sidebar has been improperly emptied, just remove it
		foreach ( $sidebars as $key => $sidebar ) {

			if ( is_array( $sidebar ) && empty( $sidebar ) ) {
				unset( $sidebars[ $key ] );
			}
		}

		// If the # of sidebars don't match, OR the keys aren't identical, merge them
		if ( count( $sidebars ) != count( $old_sidebars ) ||
		     count( array_intersect_key( $sidebars, $old_sidebars ) ) != count( $sidebars )
		) {

			foreach ( $sidebars as $sidebar_ID => $sidebar ) {
				unset( $old_sidebars[ $sidebar_ID ] );
			}

			if ( empty( $sidebars ) ) {
				$sidebars = $old_sidebars;
			} elseif ( ! empty( $old_sidebars ) ) {
				$sidebars = array_merge( $sidebars, $old_sidebars );
			}
		}

		return $sidebars;
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		// Check to see if any plugin modifications have been made, and notify the user
		$active_plugins    = get_option( 'active_plugins' );
		$cd_active_plugins = get_option( 'cd_active_plugins' );
		if ( $active_plugins != $cd_active_plugins ) {

			$dashboard_link = get_admin_url();
			$dashboard_link = add_query_arg( 'cd_update_dash', 'true', $dashboard_link );

			$this->error_nag(
				__( 'Hate to bother you, but one or more plugins has been activated / deactivated. Could you ' .
				    'please visit the %sDashboard%s to refresh the available widgets?', 'client-dash' ),
				"<a href=\"$dashboard_link\">",
				'</a>'
			);

			return;
		}

		// WP API for widgets; required for use
		require_once( ABSPATH . 'wp-admin/includes/widgets.php' );

		// From wp-admin/widgets.php. Modified for CD use.
		?>

        <div id="cd-widgets">
            <div class="widget-liquid-left">
                <div id="widgets-left">
                    <div id="available-widgets" class="widgets-holder-wrap">
                        <div class="sidebar-name">
                            <div class="sidebar-name-arrow"><br/></div>
                            <h3><?php _e( 'Available Widgets', 'client-dash' ); ?> <span
                                        id="removing-widget"><?php _e( 'Deactivate', 'client-dash' ); ?>
                                    <span></span></span></h3>
                        </div>
                        <div class="widget-holder">
                            <div class="sidebar-description">
                                <p class="description">
									<?php _e( 'To activate a widget drag it to a sidebar or click ' .
									          'on it. To deactivate a widget and delete its settings, drag it back.',
										'client-dash' ); ?>
                                </p>
                            </div>
                            <div id="widget-list">
								<?php wp_list_widgets(); ?>
                            </div>
                            <br class='clear'/>
                        </div>
                        <br class="clear"/>
                    </div>
                </div>
            </div>

            <div class="widget-liquid-right">
                <div id="widgets-right" class="single-sidebar">
                    <div class="sidebars-column-1">
						<?php

						$i = 0;
						foreach ( $this->sidebars as $sidebar ) {

							$wrap_class = 'widgets-holder-wrap';
							if ( ! empty( $registered_sidebar['class'] ) ) {
								$wrap_class .= ' sidebar-' . $registered_sidebar['class'];
							}

							if ( $i > 0 ) {
								$wrap_class .= ' closed';
							}

							?>
                            <div class="<?php echo esc_attr( $wrap_class ); ?>">
								<?php wp_list_widget_controls( $sidebar['id'], $sidebar['name'] ); ?>
                            </div>
							<?php

							$i ++;
						}
						?>
                    </div>
                </div>
            </div>
            <form action="" method="post">
				<?php wp_nonce_field( 'save-sidebar-widgets', '_wpnonce_widgets', false ); ?>
            </form>
            <br class="clear"/>

            <div class="widgets-chooser">
                <ul class="widgets-chooser-sidebars"></ul>
                <div class="widgets-chooser-actions">
                    <button class="button-secondary"><?php _e( 'Cancel', 'client-dash' ); ?></button>
                    <button class="button-primary"><?php _e( 'Add Widget', 'client-dash' ); ?></button>
                </div>
            </div>
        </div>
		<?php
	}
}