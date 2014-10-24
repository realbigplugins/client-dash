<?php

/**
 * Class ClientDash_Page_Reports_Tab_Site
 *
 * Adds the core content section for Reports -> Site.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Tabs
 *
 * @since Client Dash 1.5
 */
class ClientDash_Core_Page_Reports_Tab_Site extends ClientDash {

	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {

		$this->add_content_section( array(
			'name'     => 'Basic Information',
			'page'     => 'Reports',
			'tab'      => 'Site',
			'callback' => array( $this, 'block_output' )
		) );
	}

	/**
	 * The content for the content section.
	 *
	 * @since Client Dash 1.4
	 */
	public function block_output() {

		// Get the site information
		$args              = array( 'public' => true );
		$cd_count_comments = wp_count_comments();
		$cd_count_users    = count_users();
		$cd_post_types = get_post_types( $args, 'objects' )
		?>

		<table class="form-table">
			<?php foreach ( $cd_post_types as $type ) {
				$nice_name = $type->labels->name;
				$name      = $type->name;
				$count     = wp_count_posts( $name );
				$link      = get_admin_url() . 'edit.php?post_type=' . $name;

				if ( $name != 'attachment' ) {
					?>
					<tr valign="top">
						<th scope="row">
							<?php if ( current_user_can( 'edit_posts' ) ) : ?>
								<a href="<?php echo $link; ?>"><?php echo $nice_name; ?></a>
							<?php else: ?>
								<?php echo $nice_name; ?>
							<?php endif; ?>
						</th>
						<td>
							<ul>
								<li>
									<?php if ( current_user_can( 'edit_posts' ) ) : ?>
										<a href="<?php echo $link . '&post_status=publish'; ?>"><?php echo $count->publish; ?>
											published</a>
									<?php else: ?>
										<?php echo $count->publish; ?> published
									<?php endif; ?>

								</li>
								<li>
									<?php if ( current_user_can( 'edit_posts' ) ) : ?>
										<a href="<?php echo $link . '&post_status=pending'; ?>"><?php echo $count->pending; ?>
											pending</a>
									<?php else: ?>
										<?php echo $count->pending; ?> pending
									<?php endif; ?>

								</li>
								<li>
									<?php if ( current_user_can( 'edit_posts' ) ) : ?>
										<a href="<?php echo $link . '&post_status=draft'; ?>"><?php echo $count->draft; ?>
											drafts</a>
									<?php else: ?>
										<?php echo $count->draft; ?> drafts
									<?php endif; ?>

								</li>
							</ul>
						</td>
					</tr>
				<?php
				}
			} ?>
			<tr valign="top">
				<th scope="row">
					<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
						<a href="<?php echo get_admin_url(); ?>edit-comments.php">
							Comments
						</a>
					<?php else: ?>
						Comments
					<?php endif; ?>
				</th>
				<td>
					<ul>
						<li>
							<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
								<a href="<?php echo get_admin_url() . 'edit-comments.php?comment_status=approved'; ?>"><?php echo $cd_count_comments->approved; ?>
									approved</a>
							<?php else: ?>
								<?php echo $cd_count_comments->approved; ?> approved
							<?php endif; ?></li>
						<li>
							<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
								<a href="<?php echo get_admin_url() . 'edit-comments.php?comment_status=moderated'; ?>"><?php echo $cd_count_comments->moderated; ?>
									pending</a>
							<?php else: ?>
								<?php echo $cd_count_comments->moderated; ?>pending
							<?php endif; ?></li>
						<li>
							<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
								<a href="<?php echo get_admin_url() . 'edit-comments.php?comment_status=spam'; ?>"><?php echo $cd_count_comments->spam; ?>
									spam</a>
							<?php else: ?>
								<?php echo $cd_count_comments->spam; ?> spam
							<?php endif; ?></li>
					</ul>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php if ( current_user_can( 'upload_files' ) ) : ?>
						<a href="<?php echo get_admin_url(); ?>upload.php">
							Media
						</a>
					<?php else: ?>
						Media
					<?php endif; ?>
				</th>
				<?php
				$upload_dir  = wp_upload_dir();
				$dir_info    = $this->get_dir_size( $upload_dir['basedir'] );
				$attachments = wp_count_posts( 'attachment' );
				?>
				<td><?php echo $attachments->inherit; ?> total media items<br/>
					<?php echo $this->format_dir_size( $dir_info['size'] ); ?> total media library size
					(<?php echo $dir_info['count']; ?> files)
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php if ( current_user_can( 'list_users' ) ) : ?>
						<a href="<?php echo get_admin_url(); ?>users.php">
							Users
						</a>
					<?php else: ?>
						Users
					<?php endif; ?>
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
}