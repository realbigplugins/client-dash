<?php
/**
 * The settings page.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views
 *
 * @var string $reset_settings_link
 */

defined( 'ABSPATH' ) || die;
?>

<div class="wrap clientdash">

    <h1 class="clientdash-page-title">
		<?php echo get_admin_page_title(); ?>
    </h1>

    <?php settings_errors(); ?>

    <form action="options.php" method="post">

        <table class="form-table">
            <tbody>
            <tr valign="top">
                <th scope="row">
                    <label for="cd_reset_all_settings">
						<?php _e( 'Reset All Settings', 'client-dash' ); ?>
                    </label>
                </th>

                <td>
                    <a href="<?php echo esc_attr( $reset_settings_link ); ?>" id="cd_reset_all_settings" class="button">
						<?php _e( 'Reset', 'client-dash' ); ?>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>

    </form>

</div>
