<?php

/**
 * Outputs Domain tab under Help page.
 */
function cd_core_help_domain_tab() {
	// Get the current site's domain
	$cd_domain = str_replace( 'http://', '', get_site_url() );
	$cd_ip     = gethostbyname( $cd_domain );
	$cd_dns    = dns_get_record( $cd_domain );

	?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Current site domain</th>
			<td><?php echo $cd_domain; ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Domain IP address</th>
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

cd_content_block(
	'Core Help Domain',
	'help',
	'domain',
	'cd_core_help_domain_tab'
);