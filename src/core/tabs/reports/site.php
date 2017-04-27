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
			'name'     => __( 'Basic Information', 'client-dash' ),
			'page'     => __( 'Reports', 'client-dash' ),
			'tab'      => __( 'Site', 'client-dash' ),
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
                                        <a href="<?php echo $link . '&post_status=publish'; ?>">
											<?php
											printf(
												__( '%s published', 'client-dash' ),
												$count->publish
											)
											?>
                                        </a>
									<?php else: ?>
										<?php
										printf(
										/* translators: %s number of posts publisehd */
											__( '%s published', 'client-dash' ),
											$count->publish
										);
										?>
									<?php endif; ?>

                                </li>
                                <li>
									<?php if ( current_user_can( 'edit_posts' ) ) : ?>
                                        <a href="<?php echo $link . '&post_status=pending'; ?>"><?php echo $count->pending; ?>
											<?php
											printf(
											/* translators: %s number of posts pending */
												__( '%s pending', 'client-dash' ),
												$count->pending
											);
											?>
                                        </a>
									<?php else: ?>
										<?php
										printf(
										/* translators: %s number of posts pending */
											__( '%s pending', 'client-dash' ),
											$count->pending
										);
										?>
									<?php endif; ?>

                                </li>
                                <li>
									<?php if ( current_user_can( 'edit_posts' ) ) : ?>
                                        <a href="<?php echo $link . '&post_status=draft'; ?>"><?php echo $count->draft; ?>
											<?php
											printf(
											/* translators: %s number of posts draft */
												__( '%s drafts', 'client-dash' ),
												$count->draft
											);
											?>
                                        </a>
									<?php else: ?>
										<?php
										printf(
										/* translators: %s number of posts draft */
											__( '%s drafts', 'client-dash' ),
											$count->draft
										);
										?>
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
							<?php _e( 'Comments', 'client-dash' ); ?>
                        </a>
					<?php else: ?>
						<?php _e( 'Comments', 'client-dash' ); ?>
					<?php endif; ?>
                </th>
                <td>
                    <ul>
                        <li>
							<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
                                <a href="<?php echo get_admin_url() . 'edit-comments.php?comment_status=approved'; ?>"><?php echo $cd_count_comments->approved; ?>
									<?php
									printf(
									/* translators: %s number of comments approved */
										__( '%s approved', 'client-dash' ),
										$cd_count_comments->approved
									);
									?>
                                </a>
							<?php else: ?>
								<?php
								printf(
								/* translators: %s number of comments approved */
									__( '%s approved', 'client-dash' ),
									$cd_count_comments->approved
								);
								?>
							<?php endif; ?></li>
                        <li>
							<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
                                <a href="<?php echo get_admin_url() . 'edit-comments.php?comment_status=moderated'; ?>"><?php echo $cd_count_comments->moderated; ?>
									<?php
									printf(
									/* translators: %s number of comments approved */
										__( '%s approved', 'client-dash' ),
										$cd_count_comments->approved
									);
									?>
                                </a>
							<?php else: ?>
								<?php
								printf(
								/* translators: %s number of comments pending */
									__( '%s pending', 'client-dash' ),
									$cd_count_comments->moderated
								);
								?>
							<?php endif; ?></li>
                        <li>
							<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
                                <a href="<?php echo get_admin_url() . 'edit-comments.php?comment_status=spam'; ?>"><?php echo $cd_count_comments->spam; ?>
									<?php
									printf(
									/* translators: %s number of comments approved */
										__( '%s approved', 'client-dash' ),
										$cd_count_comments->approved
									);
									?>
                                </a>
							<?php else: ?>
								<?php
								printf(
								/* translators: %s number of comments spam */
									__( '%s spam', 'client-dash' ),
									$cd_count_comments->spam
								);
								?>
							<?php endif; ?></li>
                    </ul>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
					<?php if ( current_user_can( 'upload_files' ) ) : ?>
                        <a href="<?php echo get_admin_url(); ?>upload.php">
							<?php _e( 'Media', 'client-dash' ); ?>
                        </a>
					<?php else: ?>
						<?php _e( 'Media', 'client-dash' ); ?>
					<?php endif; ?>
                </th>
				<?php
				$upload_dir  = wp_upload_dir();
				$dir_info    = $this->get_dir_size( $upload_dir['basedir'] );
				$attachments = wp_count_posts( 'attachment' );
				?>
                <td>
					<?php
					printf(
					/* translators: %s number of media items */
						__( '%s total media items', 'client-dash' ),
						$attachments->inherit
					);
					?>
                    <br/>
					<?php
					printf(
					/* translators: %s: size of media library */
						__( '%s total media library size', 'client-dash' ),
						$this->format_dir_size( $dir_info['size'] )
					);
					?>
                    (<?php
					printf(
					/* translators: %s: number of files */
						__( '%s files', 'client-dash' ),
						$dir_info['count']
					);
					?>)
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
					<?php if ( current_user_can( 'list_users' ) ) : ?>
                        <a href="<?php echo get_admin_url(); ?>users.php">
							<?php _e( 'Users', 'client-dash' ); ?>
                        </a>
					<?php else: ?>
						<?php _e( 'Users', 'client-dash' ); ?>
					<?php endif; ?>
                </th>
                <td>
					<?php
					printf(
					/* translators: %s: registered users */
						__( '%s total registered users', 'client-dash' ),
						$cd_count_users['total_users']
					);
					?>
					<?php foreach ( $cd_count_users['avail_roles'] as $role => $count ) {
						echo $count . ' ' . $role . '<br/>';
					} ?>
                </td>
            </tr>
        </table>
		<?php
	}
}