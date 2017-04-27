<?php

/**
 * Class ClientDash_Page_Help_Tab_Domain
 *
 * Adds the core content section for Help -> Domain.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Help_Tab_Domain extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		$this->add_content_section( array(
			'name'     => __( 'Basic Information', 'client-dash' ),
			'page'     => __( 'Help', 'client-dash' ),
			'tab'      => __( 'Domain', 'client-dash' ),
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		// Get the current site's domain
		$cd_domain = str_replace( 'http://', '', get_site_url() );
		$cd_ip     = gethostbyname( $cd_domain );
		$cd_dns    = dns_get_record( $cd_domain );

		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Current site domain', 'client-dash' ); ?></th>
				<td><?php echo $cd_domain; ?></td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Domain IP address', 'client-dash' ); ?></th>
				<td><?php echo $cd_ip; ?></td>
			</tr>
		</table>
		<h3>DNS Records</h3>
		<table class="form-table">
			<?php foreach ( $cd_dns as $dns ) { ?>
				<tr valign="top">
					<th scope="row"><?php echo $dns['type']; ?></th>
					<td>
						<ul>
							<?php foreach ( $dns as $name => $value ) {
								// Skip type, that's the title
								if ( $name == 'type' ) {
									continue;
								}
								echo "<li>$name: $value</li>";
							}
							?>
						</ul>
					</td>
				</tr>
			<?php } ?>
		</table>
	<?php
	}
}