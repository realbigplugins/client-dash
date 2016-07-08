<?php

/**
 * Class ClientDash_Functions
 *
 * The main, extensible class for all other classes within Client Dash.
 *
 * @package    WordPress
 * @subpackage ClientDash
 *
 * @category   Base Functionality
 *
 * @since      Client Dash 1.5
 */
abstract class ClientDash_Functions {

	/**
	 * Checks to see if we're on a specific page and tab.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $page The page to check.
	 * @param        bool /string $tab If supplied, will also check that the given tab is active.
	 *
	 * @return bool True of on the page (and tab), false otherwise.
	 */
	public static function is_cd_page( $page, $tab = false ) {

		// Check the page
		if ( isset( $_GET['page'] ) && $_GET['page'] == $page ) {

			// If also set a tab, check that
			if ( $tab ) {
				if ( isset( $_GET['tab'] ) && $tab == $tab ) {
					return true;
				} else {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Outputs the default Client Dash toggle switch (on|off).
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $name The name of the input.
	 * @param string|int $value The value for the input to output.
	 * @param string|int $current_val The current value of whatever the input has output.
	 * @param bool $horizontal Optional. Whether the switch is vertical or horizontal.
	 * @param bool $echo Optional. Whether to echo the HTML or just return it.
	 * @param bool $invert Optional. Whether to invert the relationship between the value and on|off.
	 * @param bool|array $atts Optional. Additional attributes for the item.
	 *
	 * @return string HTML of toggle switch.
	 */
	public static function toggle_switch( $name, $value, $current_val, $horizontal = false, $echo = true, $invert = false, $atts = false ) {

		// Setup orientation
		if ( $horizontal ) {
			$horizontal = 'horizontal';
		}

		// If the current value matches the input value, it's not disabled
		// (if $invert is true, reverse the output)
		$disabled = $invert ? false : true;
		if ( $value == $current_val ) {
			$disabled = $invert ? true : false;
		}

		// The HTML output
		$html = '<span class="cd-toggle-switch ' . ( $disabled ? 'off' : 'on' ) . " $horizontal\"";
		if ( $atts ) {
			foreach ( $atts as $att => $att_val ) {
				$html .= " $att='$att_val' ";
			}
		}
		$html .= '>';
		$html .= "<input type='hidden' id='$name' name='$name' value='$value' " . ( $disabled && ! $invert || $invert && ! $disabled ? 'disabled' : '' ) . '/>';
		$html .= '</span>';

		// Echo by default, return if told so
		if ( $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}

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
	public static function pointer( $content, $position = 'left', $classes = null ) {

		return "<span class='$classes' data-cd-pointer-position='$position'>$content<span class='cd-tip-close'>X</span></span>";
	}

	/**
	 * Outputs the page title with Client Dash standards.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $page The page we're on. Default 'account'.
	 */
	public static function the_page_title( $page = 'account' ) {

		global $ClientDash;

		// Get the current dashicon
		$dashicon = get_option( 'cd_dashicon_' . $page, $ClientDash->option_defaults[ 'dashicon_' . $page ] );

		// If Webmaster, get name
		if ( $page == 'webmaster' ) {
			$page = get_option( 'cd_webmaster_name', $ClientDash->option_defaults['webmaster_name'] );
		}

		echo '<h2 class="cd-title"><span class="dashicons ' . $dashicon . ' cd-icon"></span><span class="cd-title-text">' . ucwords( $page ) . '</span></h2>';
	}

	/**
	 * The main function for building the CD pages.
	 *
	 * @since Client Dash 1.0
	 */
	public static function create_tab_page() {

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
			self::error_nag( 'This page has no content' );

			return;
		}
		// Cycle through all tabs and output the menu
		echo '<h2 class="nav-tab-wrapper">';
		$i = 0;
		foreach ( $ClientDash->content_sections[ $current_page ] as $tab_ID => $props ) {
			$i ++;

			// If active tab, set class
			if ( $active_tab == $tab_ID || ! $active_tab && $i == 1 ) {
				$active = 'nav-tab-active';
			} else {
				$active = '';
			}

			// Output the tab menu item
			echo '<a href="?page=cd_' . $current_page . '&tab=' . $tab_ID . '" class="nav-tab ' . $active . '">' . $props['name'] . '</a>';
		}
		echo '</h2>';

		// If no active tab was set, take the first one
		if ( ! $active_tab ) {
			reset( $ClientDash->content_sections[ $current_page ] );
			$active_tab = key( $ClientDash->content_sections[ $current_page ] );
		}

		// Cycle through all sections and output the menu
		// Skip if total is only 1
		$total = count( $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'] );
		if ( $total > 1 ) {
			echo '<ul class="subsubsub cd-sections-menu">';

			$i = 0;
			foreach ( $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'] as $section_ID => $props ) {
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
			reset( $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'] );
			$active_section = key( $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'] );
		}

		// Get our current section
		$section_output = $ClientDash->content_sections[ $current_page ][ $active_tab ]['content-sections'][ $active_section ];

		// This calls the dynamic class and dynamic callback function
		echo '<div class="cd-content-section">';

		// Only call the tab if it exists
		if ( is_callable( $section_output['callback'] ) ) {
			call_user_func( $section_output['callback'] );
		} else {

			// Let the user know the tab doesn't exist
			self::error_nag( 'This tab doesn\'t seem to exist! Sorry about that.' );

			// Also need to remove the submit button if on settings pages
			if ( $_GET['page'] == 'cd_settings' ) {
				add_filter( 'cd_submit', '__return_false' );
			}
		}

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
	 * @param array $content_section All of the arguments for the function.
	 */
	public static function add_content_section( $content_section ) {

		global $ClientDash;

		/**
		 * Filter each content section that is added.
		 *
		 * @since Client Dash 1.6
		 */
		$content_section = apply_filters( 'cd_add_content_section', $content_section );

		if ( ! isset( $content_section['priority'] ) ) {
			$content_section['priority'] = 10;
		}

		// Generate the content section ID
		$ID = self::translate_name_to_id( $content_section['name'] );

		// Fix up the tab name (to allow spaces and such)
		$tab_ID = self::translate_name_to_id( $content_section['tab'] );

		// Fix up the page name (to allow spaces and such)
		$page = self::translate_name_to_id( $content_section['page'] );

		// Add to the array
		$ClientDash->content_sections[ $page ][ $tab_ID ]['name'] = $content_section['tab'];

		$ClientDash->content_sections[ $page ][ $tab_ID ]['content-sections'][ $ID ] = array(
			'name'     => $content_section['name'],
			'callback' => $content_section['callback'],
			'priority' => $content_section['priority']
		);

		// Also add for the unmodified version
		$ClientDash->content_sections_unmodified[ $page ][ $tab_ID ]['name'] = $content_section['tab'];

		$ClientDash->content_sections_unmodified[ $page ][ $tab_ID ]['content-sections'][ $ID ] = array(
			'name'     => $content_section['name'],
			'callback' => $content_section['callback'],
			'priority' => $content_section['priority']
		);
	}

	/**
	 * Resets all Client Dash settings.
	 *
	 * @since Client Dash 1.5.4
	 *
	 * @param bool $force Whether or not to overrite existing values.
	 */
	public static function reset_settings( $force = false ) {

		global $ClientDash;

		foreach ( $ClientDash->option_defaults as $name => $value ) {
			$existing_option = get_option( "cd_$name" );

			if ( $force ) {
				update_option( "cd_$name", $value );
			} elseif ( empty( $existing_option ) ) {
				update_option( "cd_$name", $value );
			}
		}
	}

	/**
	 * Strips out spaces and dashes and replaces them with underscores. Also
	 * translates to lowercase.
	 *
	 * @since Client Dash 1.3
	 *
	 * @param string $name The name to be translated.
	 *
	 * @return string Translated ID.
	 */
	public static function translate_name_to_id( $name ) {

		return strtolower( str_replace( array( ' ', '-' ), '_', $name ) );
	}

	/**
	 * Replaces underscores with spaces and capitalizes words.
	 *
	 * @since Client Dash 1.6
	 *
	 * @param string $ID The ID to be translated.
	 *
	 * @return string Translated name.
	 */
	public static function translate_id_to_name( $ID ) {

		return ucwords( str_replace( '_', ' ', $ID ) );
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
	public static function get_color_scheme( $which_color = null ) {

		global $ClientDash;

		$current_color = get_user_option( 'admin_color' );

		if ( ! isset( $ClientDash->admin_colors[ $current_color ])) {
			return false;
		}

		$colors        = $ClientDash->admin_colors[ $current_color ];

		$output = array(
			'primary'      => $colors->colors[1],
			'primary-dark' => $colors->colors[0],
			'secondary'    => $colors->colors[2],
			'tertiary'     => $colors->colors[3]
		);

		if ( isset( $output[ $which_color ] ) ) {
			return $output[ $which_color ];
		} else {
			return $output;
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
	public static function get_dir_size( $path ) {

		$totalsize  = 0;
		$totalcount = 0;
		$dircount   = 0;
		if ( $handle = opendir( $path ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				$nextpath = $path . '/' . $file;
				if ( $file != '.' && $file != '..' && ! is_link( $nextpath ) ) {
					if ( is_dir( $nextpath ) ) {
						$dircount ++;
						$result = self::get_dir_size( $nextpath );
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
	public static function format_dir_size( $size ) {

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
	 * This allows us to use array_key_exists recursively.
	 *
	 * @since Client Dash 1.5
	 *
	 * @param string $needle The array key to search for.
	 * @param array $haystack The array to search in.
	 *
	 * @return bool The result.
	 */
	public static function array_key_exists_r( $needle, $haystack ) {

		$result = array_key_exists( $needle, $haystack );
		if ( $result ) {
			return $result;
		}
		foreach ( $haystack as $v ) {
			if ( is_array( $v ) ) {
				$result = self::array_key_exists_r( $needle, $v );
			}
			if ( $result ) {
				return $result;
			}
		}

		return $result;
	}

	/**
	 * Get's the current user's role.
	 *
	 * @since Client Dash 1.4
	 *
	 * @return mixed The role.
	 */
	public static function get_user_role() {

		global $current_user;
		$user_roles = $current_user->roles;
		$user_role  = array_shift( $user_roles );

		return $user_role;
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
	 * @param string $caps Optional. A WordPress recognized capability.
	 * @param bool $echo Optional. Whether to echo or return the error.
	 *
	 * @return string The error.
	 * is 'read'.
	 */
	public static function error_nag( $message, $caps = 'read', $echo = true ) {

		if ( current_user_can( $caps ) ) {
			if ( $echo ) {
				echo "<div class='error inline cd-message'><p>$message</p></div>";
			} else {
				return "<div class='error inline cd-message'><p>$message</p></div>";
			}
		}
	}

	/**
	 * Displays a WordPress update nag.
	 *
	 * The nag will only show if the current user has the capabilities that
	 * are defined by the second parameter. This defaults to allowing
	 * everybody to see the message.
	 *
	 * @since Client Dash 1.4
	 *
	 * @param string $message The message to show.
	 * @param string $caps . Optional. A WordPress recognized capability. Default
	 *                        is 'read'.
	 */
	public static function update_nag( $message, $caps = 'read' ) {

		if ( current_user_can( $caps ) ) {
			echo "<div class='updated inline cd-message'><p>$message</p></div>";
		}
	}

	/**
	 * Returns the settings url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public static function get_settings_url( $tab = null, $section = null ) {

		return get_admin_url() . 'options-general.php?page=cd_settings' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the account url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public static function get_account_url( $tab = null, $section = null ) {

		return get_admin_url() . 'index.php?page=cd_account' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the help url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public static function get_help_url( $tab = null, $section = null ) {

		return get_admin_url() . 'index.php?page=cd_help' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the reports url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public static function get_reports_url( $tab = null, $section = null ) {

		return get_admin_url() . 'index.php?page=cd_reports' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns the webmaster url.
	 *
	 * @since Client Dash 1.2
	 *
	 * @param string $tab The tab to link to.
	 * @param string $section The content section to link to.
	 *
	 * @return string
	 */
	public static function get_webmaster_url( $tab = null, $section = null ) {

		return get_admin_url() . 'index.php?page=cd_webmaster' . ( $tab ? "&tab=$tab" : '' ) . ( $section ? "&section=$section" : '' );
	}

	/**
	 * Returns 1.
	 *
	 * Built for use in turning the dashboard columns into one.
	 *
	 * @since Client Dash 1.5
	 */
	public static function return_1() {

		return 1;
	}

	/**
	 * Used in widgets_init.
	 *
	 * @since Client Dash 1.5
	 *
	 * @param mixed $matches Matches from a preg_replace_callback.
	 *
	 * @return string Upped count.
	 */
	public static function replace_count( $matches ) {

		$n = intval( $matches[2] ) + 1;

		return $matches[1] . $n;
	}

	/**
	 * Allows an array of needles instead of just one.
	 *
	 * @since  Client Dash 1.6
	 *
	 * @author Binyamin (stackoverflow)
	 *
	 * @param       $haystack
	 * @param array $needles
	 * @param int $offset
	 *
	 * @return bool|mixed
	 */
	public static function strposa( $haystack, $needles = array(), $offset = 0 ) {
		$chr = array();
		foreach ( $needles as $needle ) {
			$res = strpos( $haystack, $needle, $offset );
			if ( $res !== false ) {
				$chr[ $needle ] = $res;
			}
		}
		if ( empty( $chr ) ) {
			return false;
		}

		return min( $chr );
	}

	/**
	 * Fixes incomplete classes.
	 *
	 * @since 1.6.8
	 *
	 * @param object $object The broken object.
	 *
	 * @return mixed The fixed object.
	 */
	public static function fix_object( &$object ) {

		if ( ! is_object( $object ) && gettype( $object ) == 'object' ) {
			return ( $object = unserialize( serialize( $object ) ) );
		}

		return $object;
	}

	// Widget specific functions
}