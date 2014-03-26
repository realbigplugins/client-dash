<?php
global $current_user;
get_currentuserinfo();
$cd_username = $current_user->user_login;
$cd_firstname = $current_user->first_name;
$cd_lastname = $current_user->last_name;
$cd_useremail = $current_user->user_email;
$cd_userregistered = $current_user->user_registered;
$cd_userrole = 'STILL NEED THIS FUNCTION';
?>
<table class="form-table">
	<tr valign="top">
		<th scope="row">Your username</th>
		<td><?php echo $cd_username; ?></td>
	</tr>
	<tr valign="top">
		<th scope="row">Your name</th>
		<td><?php echo $cd_firstname.' '.$cd_lastname; ?></td>
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
</table>