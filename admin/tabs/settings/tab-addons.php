<?php

/**
 * Outputs Addons tab under Settings page.
 */
function cd_core_settings_addons_tab() {
	// Get rid of submit button on this page
	add_filter( 'cd_submit', '__return_false' );

	// Activate/Deactivate plugins
	if ( isset( $_GET['cd_activate'] ) ) {
		activate_plugin( $_GET['cd_activate'] );
	}

	if ( isset( $_GET['cd_deactivate'] ) ) {
		deactivate_plugins( $_GET['cd_deactivate'] );
	}

	// Declare addons
	$addons = array(
		'Client Dash WP Help Addon' => array(
			'url'           => 'http://wordpress.org/plugins/client-dash-wp-help-add-on/',
			'install-url'   => '/wp-admin/plugin-install.php?tab=search&s=client+dash+wp+help+add+on&plugin-search-input=Search+Plugins',
			'activate-slug' => 'client-dash-wp-help-add-on/client-dash-wp-help.php',
			'installed'     => ( get_plugins( '/client-dash-wp-help' ) ? true : false ),
			'active'        => ( is_plugin_active( 'client-dash-wp-help/client-dash-wp-help.php' ) ? true : false ),
			'icon'			=> 'editor-help'
		),
		'Client Dash Extension Boilerplate' => array(
			'url'           => 'https://github.com/brashrebel/client-dash-extension-boilerplate',
			'install-url'   => 'https://github.com/brashrebel/client-dash-extension-boilerplate/archive/master.zip',
			'activate-slug' => 'client-dash-extension-boilerplate/client-dash-extension-boilerplate.php',
			'installed'     => ( get_plugins( '/client-dash-extension-boilerplate' ) ? true : false ),
			'active'        => ( is_plugin_active( 'client-dash-extension-boilerplate/client-dash-extension-boilerplate.php' ) ? true : false ),
			'icon'			=> 'admin-tools'
		),
		'Client Dash Status Cake Addon' => array(
			'url'           => 'https://wordpress.org/plugins/client-dash-status-cake-add-on/',
			'install-url'   => '/wp-admin/plugin-install.php?tab=search&s=client+dash+status+cake+add+on&plugin-search-input=Search+Plugins',
			'activate-slug' => 'client-dash-status-cake/client-dash-status-cake.php',
			'installed'     => ( get_plugins( '/client-dash-status-cake' ) ? true : false ),
			'active'        => ( is_plugin_active( 'client-dash-status-cake/client-dash-status-cake.php' ) ? true : false ),
			'icon'			=> 'smiley'
		),
		'Client Dash BackupBuddy Addon' => array(
			'url'           => 'https://wordpress.org/plugins/client-dash-backup-buddy/',
			'install-url'   => '/wp-admin/plugin-install.php?tab=search&s=client+dash+backup+buddy+add+on&plugin-search-input=Search+Plugins',
			'activate-slug' => 'client-dash-backup-buddy/client-dash-backup-buddy.php',
			'installed'     => ( get_plugins( '/client-dash-backup-buddy' ) ? true : false ),
			'active'        => ( is_plugin_active( 'client-dash-backup-buddy/client-dash-backup-buddy.php' ) ? true : false ),
			'icon'			=> 'backup'
		)
	);
	?>

	<h3>Available Client Dash Addons</h3>
	<?php
	foreach ( $addons as $name => $props ) {
		// Set up activate/deactivate urls
		$url            = remove_query_arg( array( 'cd_deactivate', 'cd_activate' ) );
		$activate_url   = add_query_arg( array( 'cd_activate' => $props['activate-slug'] ), $url );
		$deactivate_url = add_query_arg( array( 'cd_deactivate' => $props['activate-slug'] ), $url );

		echo '<div class="cd-addon cd-col-three">';
		echo '<div class="cd-addon-container">';
		echo '<a href="' . $props['url'] . '"><span class="dashicons dashicons-editor-help"></span>';
		echo '<h4>' . $name . '</h4></a>';

		if ( $props['active'] ) {
			echo '<a href="' . $deactivate_url . '" class="button">Deactivate</a>';
		} elseif ( $props['installed'] && ! $props['active'] ) {
			echo '<a href="' . $activate_url . '" class="button">Activate</a>';
		} elseif ( ! $props['installed'] ) {
			echo '<a href="' . $props['install-url'] . '" class="button">Install</a>';
		}

		echo '</div>';
		echo '</div>';
	}
}

cd_content_block(
	'Core Settings Addons',
	'settings',
	'addons',
	'cd_core_settings_addons_tab'
);