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
		<?php foreach ( $cd_dns as $key => $value ) {
			$host   = $value['host'];
			$ip     = $value['ip'];
			$pri    = $value['pri'];
			$target = $value['target'];
			$ttl    = $value['ttl'];
			$mname  = $value['mname'];
			$rname  = $value['rname'];
			$txt    = $value['txt'];
			?>
			<tr valign="top">
				<th scope="row"><?php echo $value['type']; ?></th>
				<td>
					<ul>
						<?php
						if ( ! empty( $host ) ) {
							echo '<li>Host: ' . $host . '</li>';
						}
						if ( ! empty( $ip ) ) {
							echo '<li>IP: ' . $ip . '</li>';
						}
						if ( ! empty( $pri ) ) {
							echo '<li>Priority: ' . $pri . '</li>';
						}
						if ( ! empty( $target ) ) {
							echo '<li>Target: ' . $target . '</li>';
						}
						if ( ! empty( $ttl ) ) {
							echo '<li>TTL: ' . $ttl . '</li>';
						}
						if ( ! empty( $mname ) ) {
							echo '<li>MNAME: ' . $mname . '</li>';
						}
						if ( ! empty( $rname ) ) {
							echo '<li>RNAME: ' . $rname . '</li>';
						}
						if ( ! empty( $txt ) ) {
							echo '<li>TXT: ' . $txt . '</li>';
						}
						?>
					</ul>
				</td>
			</tr>
		<?php } ?>
	</table>
<?php
}

cd_content_block( 'Core Help Domain', 'help', 'domain', 'cd_core_help_domain_tab' );