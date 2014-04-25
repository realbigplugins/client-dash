<?php
// Get the site information
$cd_count_posts = wp_count_posts();
$cd_published_posts = $cd_count_posts->publish;
$cd_count_pages = wp_count_posts('page');
$cd_published_pages = $cd_count_pages->publish;
$cd_count_comments = wp_count_comments();
$cd_approved_comments = $cd_count_comments->approved;
$cd_count_users = count_users();
?>

<table class="form-table">
	<tr valign="top">
		<th scope="row">Total published posts</th>
		<td><?php echo $cd_published_posts; ?></td>
	</tr>
	<tr valign="top">
		<th scope="row">Total approved comments</th>
		<td><?php echo $cd_approved_comments; ?></td>
	</tr>
	<tr valign="top">
		<th scope="row">Total published pages</th>
		<td><?php echo $cd_published_pages; ?></td>
	</tr>
	<tr valign="top">
		<th scope="row">Total registered users</th>
		<td><?php echo $cd_count_users['total_users']; ?></td>
	</tr>
</table>