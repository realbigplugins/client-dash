<?php

/**
 * Class ClientDash_Functions
 *
 * The main, extensible class for all other classes within Client Dash.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
abstract class ClientDash_Functions {

	/**
	 * Outputs a helpful tip that can be closed.
	 *
	 * @since Client Dash 1.5
	 *
	 * @param string $content The content of the tip.
	 * @param string $position The position of the tip. (left or right)
	 * @param string $classes A string containing all the classes to add to the tip.
	 *
	 * @return string The tip.
	 */
	public function tip( $content, $position = 'left', $classes = null ) {

		return "<span class='cd-tip cd-tip-hidden cd-tip-$position $classes'>$content<span class='cd-tip-close'>X</span></span>";
	}

	/**
	 * Outputs the page title with Client Dash standards.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $page The page we're on. Default 'account'.
	 */
	public function the_page_title( $page = 'account' ) {

		global $ClientDash;

		// Get the current dashicon
		$dashicon = get_option( 'cd_dashicon_' . $page, $ClientDash->option_defaults[ 'dashicon_' . $page ] );

		echo '<h2 class="cd-title"><span class="dashicons ' . $dashicon . ' cd-icon"></span><span class="cd-title-text">' . get_admin_page_title() . '</span></h2>';
	}

	/**
	 * The main function for building the CD pages.
	 *
	 * @since Client Dash 1.0
	 */
	public function create_tab_page() {

		global $ClientDash;

		// Get the page for building url
		$current_page = str_replace( 'cd_', '', $_GET['page'] );

		// If a tab is open, get it
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = $_GET['tab'];
		} else {
			$active_tab = null;
		}

		// If a section is open, get it
		if ( isset( $_GET['section'] ) ) {
			$active_section = $_GET['section'];
		} else {
			$active_section = null;
		}

		// If no content on this page, show error and bail
		if ( empty( $ClientDash->content_sections[ $current_page ] ) ) {
			$this->error_nag( 'This page has no content' );

			return;
		}
		// Cycle through all tabs and output the menu
		echo '<h2 class="nav-tab-wrapper">';
		$i = 0;
		foreach ( $ClientDash->content_sections[ $current_page ] as $tab_ID => $block ) {
			$i ++;

			// Translate the tab ID into the tab name
			$tab_name = ucwords( str_replace( '_', ' ', $tab_ID ) );

			// If active tab, set class
			if ( $active_tab == $tab_ID || ! $active_tab && $i == 1 ) {
				$active = 'nav-tab-active';
			} else {
				$active = '';
			}

			// Output the tab menu item
			echo '<a href="?page=cd_' . $current_page . '&tab=' . $tab_ID . '" class="nav-tab ' . $active . '">' . $tab_name . '</a>';
		}
		?>
		</h2>
		<?php

		// If no active tab was set, take the first one
		if ( ! $active_tab ) {
			reset( $ClientDash->content_sections[ $current_page ] );
			$active_tab = key( $ClientDash->content_sections[ $current_page ] );
		}

		// Cycle through all sections and output the menu
		// Skip if total is only 1
		$total = count( $ClientDash->content_sections[ $current_page ][ $active_tab ] );
		if ( $total > 1 ) {
			echo '<ul class="subsubsub cd-sections-menu">';

			$i = 0;
			foreach ( $ClientDash->content_sections[ $current_page ][ $active_tab ] as $section_ID => $props ) {
				$i ++;

				echo '<li>';

				// If active section, set class
				if ( $active_section == $section_ID || ! $active_section && $i == 1 ) {
					$active = 'current';
				} else {
					$active = '';
				}

				// Add on a pipe if not the last item
				$output_text = $props['name'] . ( $i < $total ? ' |' : '' );

				// Output the section menu item
				echo "<a href=\"?page=cd_$current_page&tab=$active_tab&section=$section_ID\" class=\"$active\">$output_text</a>";

				echo '</li>';
			}
			echo '</ul>';
		}

		// If no active section was set, take the first one
		if ( ! $active_section ) {
			reset( $ClientDash->content_sections[ $current_page ][ $active_tab ] );
			$active_section = key( $ClientDash->content_sections[ $current_page ][ $active_tab ] );
		}

		// Get our current section
		$section_output = $ClientDash->content_sections[ $current_page ][ $active_tab ][ $active_section ];

		// This calls the dynamic class and dynamic callback function
		echo '<div class="cd-content-section">';
		$section_output['callback'][0]->$section_output['callback'][1]();
		echo '</div>';
	}

	/**
	 * Creates content sections.
	 *
	 * This function creates a content section for Client Dash. It can be set to
	 * go into a specific tab in a specific tab.
	 *
	 * @since Client Dash 1.4
	 *
	 * @param array $args All of the arguments for the function.
	 */
	public function add_content_section( $args ) {

		global $ClientDash;

		extract( $args );

		// Set up defaults
		if ( ! isset( $name ) ) {
			$name = null;
		}
		if ( ! isset( $page ) ) {
			$page = null;
		}
		if ( ! isset( $tab ) ) {
			$tab = null;
		}
		if ( ! isset( $callback ) ) {
			$callback = null;
		}
		if ( ! isset( $priority ) ) {
			$priority = 10;
		}

		// Generate the content section ID
		$ID = strtolower( str_replace( array( ' ', '-' ), '_', $name ) );

		// Fix up the tab name (to allow spaces and such)
		$tab = strtolower( str_replace( array( ' ', '-' ), '_', $tab ) );

		// Fix up the page name (to allow spaces and such)
		$page = strtolower( str_replace( array( ' ', '-' ), '_', $page ) );

		$ClientDash->content_sections[ $page ][ $tab ][ $ID ] = array(
			'name'     => $name,
			'callback' => $callback,
			'priority' => $priority
		);
	}

	public function add_widget( $args ) {

		global $ClientDash;

		extract( $args );

		// Set up defaults
		if ( ! isset( $title ) ) {
			$title = null;
		}
		if ( ! isset( $callback ) ) {
			$callback = null;
		}
		if ( ! isset( $edit_callback ) ) {
			$edit_callback = null;
		}
		if ( ! isset( $description ) ) {
			$description = null;
		}

		// Generate the widget ID
		$ID = 'cd_' . strtolower( str_replace( array( ' ', '-' ), '_', $title ) );

		$widget = array(
			'ID'            => $ID,
			'title'         => $title,
			'callback'      => $callback,
			'edit_callback' => $edit_callback,
			'description'   => $description
		);

		array_push( $ClientDash->widgets, $widget );
	}

	public function widget_loop( $widgets, $disabled = false ) {

		$i = - 1;
		foreach ( $widgets as $widget ) {
			$i ++;

			// Defaults
			$title         = null;
			$ID            = null;
			$description   = null;
			$callback      = null;
			$edit_callback = null;

			extract( $widget );

			if ( empty( $ID ) ) {
				$ID = 'cd_' . strtolower( str_replace( array( ' ', '-' ), '_', $title ) );
			}
			?>
			<li class="cd-dash-widget ui-draggable">
				<h4 class="cd-dash-widget-title">
					<?php echo $title; ?>
					<span class="cd-up-down open"></span>
				</h4>

				<div class="cd-dash-widget-settings">
					<?php
					if ( $edit_callback ) {
						$edit_callback[0]::$edit_callback[1]();
					} else {
						echo 'No settings';
					}
					?>

					<div class="cd-dash-widget-footer">
						<a href="#" class="cd-dash-widget-delete"
						   onclick="cdWidgets.remove(this); return false;">
							Delete
						</a>
					</div>
				</div>

				<p class="cd-dash-widget-description">
					<?php echo $description; ?>
				</p>

				<?php
				if ( ! empty ( $disabled ) ) {
					echo "<input type='hidden' name='cd_widgets[$i][ID]' value='" . $ID . "' " . ( $disabled ? 'disabled' : '' ) . "/>";
				}
				if ( ! empty ( $title ) ) {
					echo "<input type='hidden' name='cd_widgets[$i][title]' value='" . $title . "' " . ( $disabled ? 'disabled' : '' ) . "/>";
				}
				if ( ! empty ( $callback ) ) {
					echo "<input type='hidden' name='cd_widgets[$i][callback][0]' value='" . $callback[0] . "' " . ( $disabled ? 'disabled' : '' ) . "/>";
					echo "<input type='hidden' name='cd_widgets[$i][callback][1]' value='" . $callback[1] . "' " . ( $disabled ? 'disabled' : '' ) . "/>";
				}
				if ( ! empty ( $edit_callback ) ) {
					echo "<input type='hidden' name='cd_widgets[$i][edit_callback][0]' value='" . $edit_callback[0] . "' " . ( $disabled ? 'disabled' : '' ) . "/>";
					echo "<input type='hidden' name='cd_widgets[$i][edit_callback][1]' value='" . $edit_callback[1] . "' " . ( $disabled ? 'disabled' : '' ) . "/>";
				}
				?>
			</li>
		<?php
		}
	}

	/**
	 * Gets the current color scheme.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param $which_color
	 *
	 * @return array Current color scheme
	 */
	public
	function get_color_scheme(
		$which_color = null
	) {

		global $ClientDash;

		$current_color = get_user_option( 'admin_color' );
		$colors        = $ClientDash->admin_colors[ $current_color ];

		$output = array(
			'primary'      => $colors->colors[1],
			'primary-dark' => $colors->colors[0],
			'secondary'    => $colors->colors[2],
			'tertiary'     => $colors->colors[3]
		);

		if ( ! $which_color ) {
			return $output;
		} elseif ( $which_color == 'primary' ) {
			return $output['primary'];
		} elseif ( $which_color == 'primary-dark' ) {
			return $output['primary-dark'];
		} elseif ( $which_color == 'secondary' ) {
			return $output['secondary'];
		} elseif ( $which_color == 'tertiary' ) {
			return $output['tertiary'];
		}
	}

	/**
	 * Gets the size of a directory on the server.
	 *
	 * @since ClientDash 1.1
	 *
	 * @param $path
	 *
	 * @return mixed
	 */
	public
	function get_dir_size(
		$path
	) {

		$totalsize  = 0;
		$totalcount = 0;
		$dircount   = 0;
		if ( $handle = opendir( $path ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				$nextpath = $path . '/' . $file;
				if ( $file != '.' && $file != '..' && ! is_link( $nextpath ) ) {
					if ( is_dir( $nextpath ) ) {
						$dircount ++;
						$result = $this->get_dir_size( $nextpath );
						$totalsize += $result['size'];
						$totalcount += $result['count'];
						$dircount += $result['dircount'];
					} elseif ( is_file( $nextpath ) ) {
						$totalsize += filesize( $nextpath );
						$totalcount ++;
					}
				}
			}
		}
		closedir( $handle );
		$total['size']     = $totalsize;
		$total['count']    = $totalcount;
		$total['dircount'] = $dircount;

		return $total;
	}

	/**
	 * Correctly formats the bytes size into a more readable size.
	 *
	 * @since Client Dash 1.1
	 *
	 * @param int $size Size in bytes
	 *
	 * @return string
	 */
	public
	function format_dir_size(
		$size
	) {

		if ( $size < 1024 ) {
			return $size . " bytes";
		} else if ( $size < ( 1024 * 1024 ) ) {
			$size = round( $size / 1024, 1 );

			return $size . " KB";
		} else if ( $size < ( 1024 * 1024 * 1024 ) ) {
			$size = round( $size / ( 1024 * 1024 ), 1 );

			return $size . " MB";
		} else {
			$size = round( $size / ( 1024 * 1024 * 1024 ), 1 );

			return $size . " GB";
		}
	}

	/**
	 * Get's the current user's role.
	 *
	 * @since Client Dash 1.4
	 *
	 * @return mixed The role.
	 */
	public
	function get_user_role() {

		global $current_user;

		$user_roles = $current_user->roles;
		$user_role  = array_shift( $user_roles );

		return $user_role;
	}

	/**
	 * Activates a plugin.
	 *
	 * @since Client Dash 1.4
	 *
	 * @param $plugin string Plugin path/Plugin file-name
	 *
	 * @return null
	 */
	public
	function activate_plugin(
		$plugin
	) {

		$current = get_option( 'active_plugins' );
		$plugin  = plugin_basename( trim( $plugin ) );

		if ( ! in_array( $plugin, $current ) ) {
			$current[] = $plugin;
			sort( $current );
			do_action( 'activate_plugin', trim( $plugin ) );
			update_option( 'active_plugins', $current );
			do_action( 'activate_' . trim( $plugin ) );
			do_action( 'activated_plugin', trim( $plugin ) );
		}

		return null;
	}

	/**
	 * Displays a WordPress error nag.
	 *
	 * The nag will only show if the current user has the capabilities that
	 * are defined by the second parameter. This defaults to allowing
	 * everybody to see the message.
	 *
	 * @since Client Dash 1.4
	 *
	 * @param string $message The message to show.
	 * @param string $caps . Optional. A WordPress recognized capability. Default
	 * is 'read'.
	 */
	public
	function error_nag(
		$message, $caps = 'read'
	) {

		if ( current_user_can( $caps ) ) {
			echo "<div class='settings-error error'><p>$message</p></div>";
		}
	}

	/**
	 * Returns the settings url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @return string
	 */
	public
	function get_settings_url(
		$tab = null, $section = null
	) {

		return get_admin_url() . 'options-general.php?page=cd_settings' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the account url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @return string
	 */
	public
	function get_account_url(
		$tab = null, $section = null
	) {

		return get_admin_url() . 'index.php?page=cd_account' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the help url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @return string
	 */
	public
	function get_help_url(
		$tab = null, $section = null
	) {

		return get_admin_url() . 'index.php?page=cd_help' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the reports url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @return string
	 */
	public
	function get_reports_url(
		$tab = null, $section = null
	) {

		return get_admin_url() . 'index.php?page=cd_reports' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the webmaster url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @return string
	 */
	public
	function get_webmaster_url(
		$tab = null, $section = null
	) {

		return get_admin_url() . 'index.php?page=cd_webmaster' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns 1.
	 *
	 * Built for use in turning the dashboard columns into one.
	 *
	 * @since Client Dash 1.5
	 */
	public
	function return_1() {

		return 1;
	}
}