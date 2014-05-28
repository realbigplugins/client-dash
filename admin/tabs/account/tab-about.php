<?php
function cd_core_about_tab() {
	// Get the current user object
	global $current_user;

	// Get the user information
	$cd_username       = $current_user->user_login;
	$cd_firstname      = $current_user->first_name;
	$cd_lastname       = $current_user->last_name;
	$cd_useremail      = $current_user->user_email;
	$cd_userregistered = $current_user->user_registered;

	// Get current user role
	$user_roles  = $current_user->roles;
	$cd_userrole = ucwords( array_shift( $user_roles ) );
	?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">Your username</th>
			<td><?php echo $cd_username; ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Your name</th>
			<td><?php echo $cd_firstname . ' ' . $cd_lastname; ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Your e-mail address</th>
			<td><?php echo $cd_useremail; ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">When you first joined this site</th>
			<td><?php echo $cd_userregistered; ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Your role</th>
			<td><?php echo $cd_userrole; ?></td>
		</tr>
		<tr valign="top">
			<th scope="row"><a href="<?php site_url(); ?>/wp-admin/profile.php" class="button-primary">Edit your
					profile</a></th>
			<td></td>
		</tr>
	</table>
<?php
}

add_action( 'cd_account_about_tab', 'cd_core_about_tab' );