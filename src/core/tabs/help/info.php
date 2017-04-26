<?php

/**
 * Class ClientDash_Page_Help_Tab_Info
 *
 * Adds the core content section for Help -> Info.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Help_Tab_Info extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		$this->add_content_section( array(
			'name'     => __( 'Basic Information', 'client-dash' ),
			'page'     => __( 'Help', 'client-dash' ),
			'tab'      => __( 'Info', 'client-dash' ),
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		// Get the user information
		$cd_current_theme  = wp_get_theme();
		$cd_plugins        = get_plugins();
		$cd_active_plugins = str_replace( '-', ' ', get_option( 'active_plugins' ) );
		$cd_theme_uri      = $cd_current_theme->get( 'ThemeURI' );
		$cd_author_uri     = $cd_current_theme->get( 'AuthorURI' );
		global $wp_version;

		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Current WordPress version', 'client-dash' ); ?></th>
				<td><?php echo $wp_version; ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Current theme', 'client-dash' ); ?></th>
				<td><?php if ( ! empty( $cd_theme_uri )) { ?>
					<a href="<?php echo $cd_theme_uri; ?>">
						<?php
						echo $cd_current_theme . '</a>';
						} elseif ( ! empty( $cd_author_uri ) && empty( $cd_theme_uri )) {
						?>
						<a href="<?php echo $cd_author_uri; ?>">
							<?php
							echo $cd_current_theme . '</a>';
							} else {
								echo $cd_current_theme;
							}
							?>
							(<?php if ( is_child_theme() ) {
							    printf(
							            /* translators: %s: Theme name */
							            __( 'child of %s', 'client-dash' ),
                                    '<span class="cd-capitalize">' . str_replace( '-', ' ', $cd_current_theme->get( 'Template' ) ) . '</span>'
                                );
							} ?>)
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Active plugins', 'client-dash' ); ?></th>
				<td class="cd-capitalize"><?php foreach ( $cd_active_plugins as $key => $value ) {
						$string = explode( '/', $value ); // Display folder name
						echo $string[0];
						echo "<br/>";
					} ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Installed plugins', 'client-dash' ); ?></th>
				<td><?php foreach ( $cd_plugins as $plugin ) {
						echo $plugin['Name'];
						echo "<br/>";
					} ?></td>
			</tr>
		</table>
	<?php
	}
}