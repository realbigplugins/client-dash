<?php

/**
 * Class ClientDash_Page_Settings_Tab_Addons
 *
 * Adds the core content section for Settings -> Addons.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Settings_Tab_Addons extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		$this->add_content_section( array(
			'name'     => __( 'Core Settings Addons', 'client-dash' ),
			'page'     => __( 'Settings', 'client-dash' ),
			'tab'      => __( 'Addons', 'client-dash' ),
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		// Get rid of submit button on this page
		add_filter( 'cd_submit', '__return_false' );

		// Activate/Deactivate plugins
		if ( isset( $_GET['cd_activate'] ) ) {
			activate_plugin( $_GET['cd_activate'] );
			$this->update_nag( __( 'Extension activated! Refresh for changes to take effect.', 'client-dash' ) );
		}

		if ( isset( $_GET['cd_deactivate'] ) ) {
			deactivate_plugins( $_GET['cd_deactivate'] );
			$this->update_nag( __( 'Extension deactivated! Refresh for changes to take effect.', 'client-dash' ) );
		}

		// Declare addons
		$addons = array(
			__( 'Client Dash WP Help Addon', 'client-dash' )         => array(
				'url'           => 'http://wordpress.org/plugins/client-dash-wp-help-add-on/',
				'install-url'   => network_admin_url( 'plugin-install.php' ) . '?tab=search&s=client+dash+wp+help+add+on&plugin-search-input=Search+Plugins',
				'activate-slug' => 'client-dash-wp-help/client-dash-wp-help.php',
				'installed'     => ( get_plugins( '/client-dash-wp-help' ) ? true : false ),
				'active'        => ( is_plugin_active( 'client-dash-wp-help/client-dash-wp-help.php' ) ? true : false ),
				'icon'          => 'editor-help'
			),
			__( 'Client Dash Extension Boilerplate', 'client-dash' ) => array(
				'url'           => 'https://github.com/brashrebel/client-dash-extension-boilerplate',
				'install-url'   => 'https://github.com/brashrebel/client-dash-extension-boilerplate/archive/master.zip',
				'activate-slug' => 'client-dash-extension-boilerplate/client-dash-extension-boilerplate.php',
				'installed'     => ( get_plugins( '/client-dash-extension-boilerplate' ) ? true : false ),
				'active'        => ( is_plugin_active( 'client-dash-extension-boilerplate/client-dash-extension-boilerplate.php' ) ? true : false ),
				'icon'          => 'admin-tools'
			),
			__( 'Client Dash Status Cake Addon', 'client-dash' )     => array(
				'url'           => 'https://wordpress.org/plugins/client-dash-status-cake-add-on/',
				'install-url'   => network_admin_url( 'plugin-install.php' ) . '?tab=search&s=client+dash+status+cake&plugin-search-input=Search+Plugins',
				'activate-slug' => 'client-dash-status-cake/client-dash-status-cake.php',
				'installed'     => ( get_plugins( '/client-dash-status-cake' ) ? true : false ),
				'active'        => ( is_plugin_active( 'client-dash-status-cake/client-dash-status-cake.php' ) ? true : false ),
				'icon'          => 'smiley'
			),
			__( 'Client Dash BackupBuddy Addon', 'client-dash' )     => array(
				'url'           => 'https://wordpress.org/plugins/client-dash-backup-buddy/',
				'install-url'   => network_admin_url( 'plugin-install.php' ) . '?tab=search&s=client+dash+backup+buddy&plugin-search-input=Search+Plugins',
				'activate-slug' => 'client-dash-backup-buddy/client-dash-backup-buddy.php',
				'installed'     => ( get_plugins( '/client-dash-backup-buddy' ) ? true : false ),
				'active'        => ( is_plugin_active( 'client-dash-backup-buddy/client-dash-backup-buddy.php' ) ? true : false ),
				'icon'          => 'backup'
			)
		);
		?>

        <h3><?php _e( 'Available Client Dash Addons', 'client-dash' ); ?></h3>
		<?php
		foreach ( $addons as $name => $props ) {
			// Set up activate/deactivate urls
			$url            = remove_query_arg( array( 'cd_deactivate', 'cd_activate' ) );
			$activate_url   = esc_url( add_query_arg( array( 'cd_activate' => $props['activate-slug'] ), $url ) );
			$deactivate_url = esc_url( add_query_arg( array( 'cd_deactivate' => $props['activate-slug'] ), $url ) );

			echo '<div class="cd-addon cd-col-three">';
			echo '<div class="cd-addon-container">';
			echo '<a href="' . $props['url'] . '"><span class="dashicons dashicons-' . $props['icon'] . '"></span>';
			echo '<h4>' . $name . '</h4></a>';

			if ( $props['active'] ) {
				echo '<a href="' . $deactivate_url . '" class="button">' . __( 'Deactivate', 'client-dash' ) . '</a>';
			} elseif ( $props['installed'] && ! $props['active'] ) {
				echo '<a href="' . $activate_url . '" class="button">' . __( 'Activate', 'client-dash' ) . '</a>';
			} elseif ( ! $props['installed'] ) {
				echo '<a href="' . $props['install-url'] . '" class="button">' . __( 'Install', 'client-dash' ) . '</a>';
			}

			echo '</div>';
			echo '</div>';
		}
	}
}