<?php

/**
 * Outputs Site tab under Reports page.
 */
function cd_core_reports_site_tab() {
	// Get the site information
	$args = array( 'public' => true );
	$cd_count_posts    = wp_count_posts();
	$cd_count_pages    = wp_count_posts( 'page' );
	$cd_count_comments = wp_count_comments();
	$cd_count_users    = count_users();
	$cd_post_types	   = get_post_types( $args, 'objects' )
	?>

	<table class="form-table">
		<?php foreach ($cd_post_types as $type) {
			$nice_name = $type->labels->name;
			$name = $type->name;
			$count = wp_count_posts( $name );
			$link = get_admin_url(). 'edit.php?post_type=' .$name;

			if ($name != 'attachment') {
				?>
				<tr valign="top">
					<th scope="row">
						<a href="<?php echo $link; ?>">
							<?php echo $nice_name; ?>
						</a>
					</th>
					<td>
						<ul>
							<li><a href="<?php echo $link. '&post_status=publish'; ?>"><?php echo $count->publish; ?> published</a></li>
							<li><a href="<?php echo $link. '&post_status=pending'; ?>"><?php echo $count->pending; ?> pending</a></li>
							<li><a href="<?php echo $link. '&post_status=draft'; ?>"><?php echo $count->draft; ?> drafts</a></li>
						</ul>
					</td>
				</tr>
			<?php }
		} ?>
		<tr valign="top">
			<th scope="row">
				<a href="<?php echo get_admin_url(); ?>edit-comments.php">
					Comments
				</a>
			</th>
			<td>
				<ul>
					<li><a href="<?php echo get_admin_url() .'edit-comments.php?comment_status=approved'; ?>"><?php echo $cd_count_comments->approved; ?> approved</a></li>
					<li><a href="<?php echo get_admin_url() .'edit-comments.php?comment_status=moderated'; ?>"><?php echo $cd_count_comments->moderated; ?> pending</a></li>
					<li><a href="<?php echo get_admin_url() .'edit-comments.php?comment_status=spam'; ?>"><?php echo $cd_count_comments->spam; ?> spam</a></li>
				</ul>
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
	</table>
<?php
}

cd_content_block(
	'Core Reports Site',
	'reports',
	'site',
	'cd_core_reports_site_tab'
);
