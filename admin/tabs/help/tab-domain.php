<?php

/**
 * Outputs Domain tab under Help page.
 */
function cd_core_domain_tab() {
	// Get the current site's domain
	//$cd_site = get_site_url();
	$cd_domain = str_replace('http://', '', get_site_url());
	$cd_ip = gethostbyname( $cd_domain );
	$cd_dns = dns_get_record( $cd_domain );

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
		<tr valign="top">
			<th scope="row">A record</th>
			<td><ul>
				<?php 
					echo '<li>Host: '.$cd_dns[0]['host'].'</li>';
					echo '<li>TTL: '.$cd_dns[0]['ttl'].'</li>';
					echo '<li>IP: '.$cd_dns[0]['ip'].'</li>';
				?>
			</ul></td>
		</tr>
		<tr valign="top">
			<th scope="row">MX records</th>
			<td><ul>
				<?php 
					echo '<li>Host: '.$cd_dns[1]['host'].'</li>';
					echo '<li>Target: '.$cd_dns[1]['target'].'</li>';
					echo '<li>TTL: '.$cd_dns[1]['ttl'].'</li>';
					echo '<li>IP: '.$cd_dns[1]['ip'].'</li>';
					echo '<li>Priority: '.$cd_dns[1]['pri'].'</li>';
				?>
			</ul></td>
		</tr>
		<tr valign="top">
			<th scope="row">CNAME records</th>
			<td><ul>
				<?php 
					echo '<li>Host: '.$cd_dns[2]['host'].'</li>';
					echo '<li>Target: '.$cd_dns[2]['target'].'</li>';
					echo '<li>TTL: '.$cd_dns[2]['ttl'].'</li>';
				?>
			</ul></td>
		</tr>
		<tr valign="top">
			<th scope="row">Name Servers</th>
			<td><ul>
				<?php 
					echo '<li>Host: '.$cd_dns[3]['host'].'</li>';
					echo '<li>Target: '.$cd_dns[3]['target'].'</li>';
					echo '<li>TTL: '.$cd_dns[3]['ttl'].'</li>';
				?>
			</ul></td>
		</tr>
	</table>
<?php

echo '<pre>';
print_r($cd_dns);
echo '</pre>';
}

add_action( 'cd_help_domain_tab', 'cd_core_domain_tab' );