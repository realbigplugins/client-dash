<?php
/**
 * Core CD Page About
 *
 * Page: Account
 * Tab: About
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/core-pages/views
 *
 * @var WP_User $current_user
 * @var string $user_role
 */

defined( 'ABSPATH' ) || die();
?>

<div class="cd-content-section">
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e( 'Your username', 'client-dash' ); ?></th>
            <td><?php echo $current_user->user_login; ?></td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e( 'Your name', 'client-dash' ); ?></th>
            <td><?php echo $current_user->display_name; ?></td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e( 'Your e-mail address', 'client-dash' ); ?></th>
            <td><?php echo $current_user->user_email; ?></td>
        </tr>

		<?php if ( $current_user->user_url ) : ?>
            <tr valign="top">
                <th scope="row"><?php _e( 'Your URL', 'client-dash' ); ?></th>
                <td>
                    <a href="<?php echo $current_user->url; ?>" target="_blank"><?php echo $current_user->url; ?></a>
                </td>
            </tr>
		<?php endif; ?>

        <tr valign="top">
            <th scope="row"><?php _e( 'When you first joined this site', 'client-dash' ); ?></th>
            <td><?php echo $current_user->user_registered; ?></td>
        </tr>

        <tr valign="top">
            <th scope="row">
				<?php _e( 'Your role', 'client-dash' ); ?>
            </th>

            <td>
				<?php echo $user_role; ?>
                <span class="cd-caps cd-click dashicons dashicons-info"></span>

                <div id="cd-caps" style="display: none;">
                    <h4><?php _e( 'Capabilities', 'client-dash' ); ?>:</h4>
					<?php if ( ! empty( $cd_usercaps ) ) : ?>
                        <ul>
							<?php foreach ( $cd_usercaps as $key => $value ) : ?>
                                <li><?php echo $key; ?></li>
							<?php endforeach; ?>
                        </ul>
					<?php endif; ?>
                </div>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <a href="<?php site_url(); ?>/wp-admin/profile.php" class="button-primary">
					<?php _e( 'Edit your profile', 'client-dash' ); ?>
                </a>
            </th>
            <td></td>
        </tr>
    </table>
</div>