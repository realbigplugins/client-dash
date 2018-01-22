<?php
/**
 * Core CD Page output
 *
 * Page: Account
 * Tab: Sites
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/core-pages/views/account
 *
 * @var WP_User $current_user
 * @var array $blogs
 */

defined( 'ABSPATH' ) || die();
?>

<div class="cd-content-section">
    <table class="widefat fixed">
        <tbody>
		<?php $i = 0; ?>
		<?php foreach ( $blogs as $blog ) : ?>
			<?php $i ++; ?>

            <tr class="<?php echo $i % 2 == 0 ? 'alternate' : ''; ?>">

                <td valign="top">
                    <h3><?php echo $blog->blogname; ?></h3>
                    <p>
                        <a href="<?php echo $blog->siteurl; ?>">
							<?php _e( 'Visit', 'client-dash' ); ?>
                        </a>
                        |
                        <a href="<?php echo $blog->siteurl; ?>'/wp-admin/">
							<?php _e( 'Dashboard', 'client-dash' ); ?>
                        </a>
                    </p>
                </td>

            </tr>

		<?php endforeach; ?>
        </tbody>
    </table>
</div>