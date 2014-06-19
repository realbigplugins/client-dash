<?php

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
 * @param array $tabs Associative array of tabs to include.
 */
function cd_create_tab_page( $tabs = null ) {
	global $cd_existing_pages;

	$cd_existing_pages = apply_filters( 'cd_tabs', $cd_existing_pages );

	// Declare static variable
	$first_tab = '';

	/* Create Tab Menu */

	// Get the page for building url
	$current_page = str_replace( 'cd_', '', $_GET['page'] );

	// Gives ability to add more tabs on the fly
	if ( $tabs ) {
		foreach ( $tabs as $name => $ID ) {
			$cd_existing_pages[ $current_page ][ $name ] = $ID;
		}
	}

	// If a tab is open, get it
	if ( isset( $_GET['tab'] ) ) {
		$active_tab = $_GET['tab'];
	} else {
		$active_tab = null;
	}
	?>

	<h2 class="nav-tab-wrapper">
		<?php
		$i = 0;
		foreach ( $cd_existing_pages[ $current_page ] as $name => $ID ) {
			$i ++;
			if ( $i == 1 ) {
				$first_tab = $ID;
			}

			// If active tab, set class
			if ( $active_tab == $ID || ! $active_tab && $i == 1 ) {
				$active = 'nav-tab-active';
			} else {
				$active = '';
			}

			echo '<a href="?page=cd_' . $current_page . '&tab=' . $ID . '" class="nav-tab ' . $active . '">' . $name . '</a>';
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
 * @author Predeep
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
 * @since 1.1
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

// Help functions

/**
 * Returns the settings url.
 *
 * @since 1.2.0
 *
 * @return string
 */
function cd_get_settings_url() {
	return get_admin_url() . 'options-general.php?page=cd_settings';
}

/**
 * Returns the account url.
 *
 * @since 1.2.0
 *
 * @return string
 */
function cd_get_account_url() {
	return get_admin_url() . 'index.php?page=cd_account';
}

/**
 * Returns the help url.
 *
 * @since 1.2.0
 *
 * @return string
 */
function cd_get_help_url() {
	return get_admin_url() . 'index.php?page=cd_help';
}

/**
 * Returns the reports url.
 *
 * @since 1.2.0
 *
 * @return string
 */
function cd_get_reports_url() {
	return get_admin_url() . 'index.php?page=cd_reports';
}

/**
 * Returns the webmaster url.
 *
 * @since 1.2.0
 *
 * @return string
 */
function cd_get_webmaster_url() {
	return get_admin_url() . 'index.php?page=cd_webmaster';
}