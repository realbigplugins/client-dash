<?php

/**
 * Force dashboard widgets to one column.
 *
 * @since Client Dash 1.4
 */
function shapeSpace_screen_layout_columns( $columns ) {
	$columns['dashboard'] = 1;

	return $columns;
}

add_filter( 'screen_layout_columns', 'shapeSpace_screen_layout_columns' );

/**
 * Force dashboard widgets to one column.
 *
 * @since Client Dash 1.4
 */
function shapeSpace_screen_layout_dashboard() {
	return 1;
}

add_filter( 'get_user_option_screen_layout_dashboard', 'shapeSpace_screen_layout_dashboard' );

/**
 * Gets all of the active dashboard widgets.
 */
function cd_get_active_widgets() {
	global $wp_meta_boxes, $cd_widgets;

	// Initialize
	$active_widgets = array();

	// This lovely, crazy loop is what gathers all of the widgets and organizes it into MY array
	foreach ( $wp_meta_boxes['dashboard'] as $context => $widgets ) {
		foreach ( $widgets as $priority => $widgets ) {
			foreach ( $widgets as $id => $values ) {
				$active_widgets[ $id ]['title']    = $values['title'];
				$active_widgets[ $id ]['context']  = $context;
				$active_widgets[ $id ]['priority'] = $priority;
			}
		}
	}

	// Unset OUR widgets
	foreach ( $cd_widgets as $widget ) {
		unset( $active_widgets[ $widget ] );
	}

	update_option( 'cd_active_widgets', $active_widgets );
}

add_action( 'wp_dashboard_setup', 'cd_get_active_widgets', 100 );

/**
 * Outputs the page title with Client Dash standards.
 *
 * @param string $page The page we're on. Default 'account'.
 */
function cd_the_page_title( $page = 'account' ) {
	global $cd_option_defaults;

	// Get the current dashicon
	$dashicon = get_option( 'cd_dashicon_' . $page, $cd_option_defaults[ 'dashicon_' . $page ] );

	echo '<h2 class="cd-title"><span class="dashicons ' . $dashicon . ' cd-icon"></span><span class="cd-title-text">' . get_admin_page_title() . '</span></h2>';
}

/**
 * The main function for building the CD pages.
 *
 * @since Client Dash 1.0
 */
function cd_create_tab_page() {
	global $cd_content_blocks;

	// Declare static variable
	$first_tab = '';

	// Get the page for building url
	$current_page = str_replace( 'cd_', '', $_GET['page'] );

	// If a tab is open, get it
	if ( isset( $_GET['tab'] ) ) {
		$active_tab = $_GET['tab'];
	} else {
		$active_tab = null;
	}

	// If no content on this page, show error and bail
	if ( empty( $cd_content_blocks[ $current_page ] ) ) {
		cd_error( 'This page has no content' );

		return;
	}
	?>
	<h2 class="nav-tab-wrapper">
		<?php
		$i = 0;
		foreach ( $cd_content_blocks[ $current_page ] as $tab_ID => $block ) {
			$i ++;

			if ( empty( $cd_content_blocks[ $current_page ][ $tab_ID ] ) ) {
				continue;
			}

			// Translate the tab ID into the tab name
			$tab_name = ucwords( str_replace( '_', ' ', $tab_ID ) );

			if ( $i == 1 ) {
				$first_tab = $tab_ID;
			}

			// If active tab, set class
			if ( $active_tab == $tab_ID || ! $active_tab && $i == 1 ) {
				$active = 'nav-tab-active';
			} else {
				$active = '';
			}

			echo '<a href="?page=cd_' . $current_page . '&tab=' . $tab_ID . '" class="nav-tab ' . $active . '">' . $tab_name . '</a>';
		}
		?>
	</h2>
	<?php

	/* Output Tab Content */

	if ( ! $active_tab ) {
		$active_tab = $first_tab;
	}

	// Add content via actions
	do_action( 'cd_' . $current_page . '_' . $active_tab . '_tab' );
}

/**
 * Creates content blocks.
 *
 * This function creates a content block for Client Dash. It can be set to
 * go into a specific tab in a specific tab.
 *
 * @since Client Dash 1.4
 *
 * @param string $name The name of the content block.
 * @param string $page On which page the content block should show.
 * @param string $tab On which tab the content block should show.
 * @param string $callback The callback function that contains the content.
 * @param int $priority The priority of the action hook.
 */
function cd_content_block( $name = null, $page = null, $tab = null, $callback = null, $priority = 10 ) {
	global $cd_content_blocks;

	// Generate the content block ID
	$ID = strtolower( str_replace( array( ' ', '-' ), '_', $name ) );

	// Fix up the tab name (to allow spaces and such)
	$tab = strtolower( str_replace( array( ' ', '-' ), '_', $tab ) );

	$cd_content_blocks[ $page ][ $tab ][ $ID ] = array(
		'name'     => $name,
		'callback' => $callback
	);

	add_action( 'cd_' . $page . '_' . $tab . '_tab', $callback, $priority );
}

/**
 * Unsets any content blocks that are disabled for current role.
 *
 * @since Client Dash 1.4
 */
function cd_unset_content_blocks() {
	global $cd_content_blocks;

	// Check against disabled roles
	$cd_content_blocks_roles = get_option( 'cd_content_blocks_roles' );
	$current_role            = cd_get_user_role();

	// Cycle through all and unset matching content blocks
	if ( ! empty( $cd_content_blocks_roles ) ) {
		foreach ( $cd_content_blocks_roles as $role => $blocks ) {
			if ( $role != $current_role ) {
				continue;
			}

			foreach ( $blocks as $block => $info ) {
				foreach ( $info as $page => $tab ) {
					// Remove the action as well
					remove_action( 'cd_' . $page . '_' . $tab . '_tab', $cd_content_blocks[ $page ][ $tab ][ $block ]['callback'] );

					unset( $cd_content_blocks[ $page ][ $tab ][ $block ] );

					// If tab now empty, unset it
					if ( empty( $cd_content_blocks[ $page ][ $tab ] ) ) {
						unset( $cd_content_blocks[ $page ][ $tab ] );
					}
				}
			}
		}
	}
}

/**
 * Gets the current color scheme.
 *
 * @param $which_color
 *
 * @return array Current color scheme
 */
function cd_get_color_scheme( $which_color ) {
	global $admin_colors;
	$current_color = get_user_option( 'admin_color' );
	$colors        = $admin_colors[ $current_color ];

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
function cd_get_dir_size( $path ) {
	$totalsize  = 0;
	$totalcount = 0;
	$dircount   = 0;
	if ( $handle = opendir( $path ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			$nextpath = $path . '/' . $file;
			if ( $file != '.' && $file != '..' && ! is_link( $nextpath ) ) {
				if ( is_dir( $nextpath ) ) {
					$dircount ++;
					$result = cd_get_dir_size( $nextpath );
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
function cd_format_dir_size( $size ) {
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
function cd_get_user_role() {
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
function cd_activate_plugin( $plugin ) {
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

// Helper functions

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
function cd_error( $message, $caps = 'read' ) {
	if ( current_user_can( $caps ) ) {
		echo '<div class="settings-error error"><p>' . $message . '</p></div>';
	}
}

/**
 * Returns the settings url.
 *
 * @since Client Dash 1.2
 *
 * @return string
 */
function cd_get_settings_url() {
	return get_admin_url() . 'options-general.php?page=cd_settings';
}

/**
 * Returns the account url.
 *
 * @since Client Dash 1.2
 *
 * @return string
 */
function cd_get_account_url() {
	return get_admin_url() . 'index.php?page=cd_account';
}

/**
 * Returns the help url.
 *
 * @since Client Dash 1.2
 *
 * @return string
 */
function cd_get_help_url() {
	return get_admin_url() . 'index.php?page=cd_help';
}

/**
 * Returns the reports url.
 *
 * @since Client Dash 1.2
 *
 * @return string
 */
function cd_get_reports_url() {
	return get_admin_url() . 'index.php?page=cd_reports';
}

/**
 * Returns the webmaster url.
 *
 * @since Client Dash 1.2
 *
 * @return string
 */
function cd_get_webmaster_url() {
	return get_admin_url() . 'index.php?page=cd_webmaster';
}