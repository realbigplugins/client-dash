<?php

/**
 * Outputs Site tab under Reports page.
 */
function cd_core_reports_site_tab() {
	// Get the site information
	$cd_count_posts    = wp_count_posts();
	$cd_count_pages    = wp_count_posts( 'page' );
	$cd_count_comments = wp_count_comments();
	$cd_count_users    = count_users();
	?>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<a href="<?php echo get_admin_url(); ?>edit.php?post_type=post">
				Posts
				</a>
			</th>
			<td><?php echo $cd_count_posts->publish; ?> published<br/>
				<?php echo $cd_count_posts->draft; ?> draft<?php if ( $cd_count_posts->draft != 1 ) {
					echo 's';
				} ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<a href="<?php echo get_admin_url(); ?>edit-comments.php">
				Comments
				</a>
			</th>
			<td><?php echo $cd_count_comments->approved; ?> approved<br/>
				<?php echo $cd_count_comments->spam; ?> spam
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<a href="<?php echo get_admin_url(); ?>edit.php?post_type=page">
				Pages
				</a>
			</th>
			<td><?php echo $cd_count_pages->publish; ?> published<br/>
				<?php echo $cd_count_pages->draft; ?> draft<?php if ( $cd_count_pages->draft != 1 ) {
					echo 's';
				} ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<a href="<?php echo get_admin_url(); ?>users.php">
				Users
				</a>
			</th>
			<td><?php echo $cd_count_users['total_users']; ?> total registered users<br/>
				<?php foreach ( $cd_count_users['avail_roles'] as $role => $count ) {
					echo $count . ' ' . $role . '<br/>';
				} ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<a href="<?php echo get_admin_url(); ?>upload.php">
				Media
				</a>
			</th>
			<?php
			$upload_dir  = wp_upload_dir();
			$dir_info    = cd_get_dir_size( $upload_dir['basedir'] );
			$attachments = wp_count_posts( 'attachment' );
			?>
			<td><?php echo $attachments->inherit; ?> total media items<br/>
				<?php echo cd_format_dir_size( $dir_info['size'] ); ?> total media library size
				(<?php echo $dir_info['count']; ?> files)
			</td>
		</tr>
	</table>
<?php
}

cd_content_block( 'Core Reports Site', 'reports', 'site', 'cd_core_reports_site_tab' );