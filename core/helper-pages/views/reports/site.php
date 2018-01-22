<?php
/**
 * Core CD Page output
 *
 * Page: Reports
 * Tab: Site
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/core-pages/views/reports
 *
 * @var object $count_comments
 * @var object $count_users
 * @var array $post_types
 * @var string $upload_dir
 * @var string $dir_info
 * @var object $attachments
 */

defined( 'ABSPATH' ) || die();
?>

<div class="cd-content-section">
    <table class="form-table">
		<?php foreach ( $post_types as $type ) : ?>
			<?php $count = wp_count_posts( $type->name ); ?>

			<?php if ( $type->name != 'attachment' ) : ?>

                <tr valign="top">
                    <th scope="row">
						<?php if ( current_user_can( 'edit_posts' ) ) : ?>
                            <a href="<?php echo admin_url( "edit.php?post_type=$type->name" ); ?>">
								<?php echo $type->labels->name; ?>
                            </a>
						<?php else: ?>
							<?php echo $type->labels->name; ?>
						<?php endif; ?>
                    </th>
                    <td>
                        <ul>
                            <li>
								<?php if ( current_user_can( 'edit_posts' ) ) : ?>
                                    <a href="<?php echo $link . '&post_status=publish'; ?>">
										<?php
										printf(
											__( '%d published', 'client-dash' ),
											$count->publish
										);
										?>
                                    </a>
								<?php else: ?>
									<?php
									printf(
										__( '%d published', 'client-dash' ),
										$count->publish
									);
									?>
								<?php endif; ?>

                            </li>
                            <li>
								<?php if ( current_user_can( 'edit_posts' ) ) : ?>
                                    <a href="<?php echo $link . '&post_status=pending'; ?>">
										<?php
										printf(
											__( '%d pending', 'client-dash' ),
											$count->pending
										);
										?>
                                    </a>
								<?php else: ?>
									<?php
									printf(
										__( '%d pending', 'client-dash' ),
										$count->pending
									);
									?>
								<?php endif; ?>

                            </li>
                            <li>
								<?php if ( current_user_can( 'edit_posts' ) ) : ?>
                                    <a href="<?php echo $link . '&post_status=draft'; ?>">
										<?php
										printf(
											__( '%d drafts', 'client-dash' ),
											$count->draft
										);
										?>
                                    </a>
								<?php else: ?>
									<?php
									printf(
										__( '%d drafts', 'client-dash' ),
										$count->draft
									);
									?>
								<?php endif; ?>
                            </li>
                        </ul>
                    </td>
                </tr>

			<?php endif; ?>
		<?php endforeach; ?>

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
                            <a href="<?php echo get_admin_url() . 'edit-comments.php?comment_status=approved'; ?>">
								<?php
								printf(
									__( '%d approved', 'client-dash' ),
									$count_comments->approved
								);
								?>
                            </a>
						<?php else: ?>
							<?php
							printf(
								__( '%d approved', 'client-dash' ),
								$count_comments->approved
							);
							?>
						<?php endif; ?>
                    </li>

                    <li>
						<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
                        <a href="<?php echo get_admin_url() . 'edit-comments.php?comment_status=moderated'; ?>">
							<?php
							printf(
								__( '%d pending', 'client-dash' ),
								$count_comments->moderated
							);
							?>
							<?php else: ?>
								<?php
								printf(
									__( '%d pending', 'client-dash' ),
									$count_comments->moderated
								);
								?>
							<?php endif; ?>
                    </li>

                    <li>
						<?php if ( current_user_can( 'moderate_comments' ) ) : ?>
                        <a href="<?php echo get_admin_url() . 'edit-comments.php?comment_status=spam'; ?>">
							<?php
							printf(
								__( '%d spam', 'client-dash' ),
								$count_comments->spam
							);
							?>
							<?php else: ?>
								<?php
								printf(
									__( '%d spam', 'client-dash' ),
									$count_comments->spam
								);
								?>
							<?php endif; ?>
                    </li>
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

            <td>
				<?php
				printf(
					__( '%d total media items', 'client-dash' ),
					$attachments->inherit
				);
				?>
                <br/>
				<?php
				printf(
					__( '%s total media library size', 'client-dash' ),
					cd_format_dir_size( $dir_info['size'] )
				);
				?>
                (
				<?php
				printf(
					__( '%d files', 'client-dash' ),
					$dir_info['count']
				);
				?>
                )
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
					__( '%d total registered users', 'client-dash' ),
					$count_users['total_users']
				);
				?>
                <br/>

                <ul>
					<?php foreach ( $count_users['avail_roles'] as $role => $count ) : ?>
                        <li>
							<?php echo "$role: $count"; ?>
                        </li>
					<?php endforeach; ?>
                </ul>
            </td>
        </tr>
    </table>
</div>