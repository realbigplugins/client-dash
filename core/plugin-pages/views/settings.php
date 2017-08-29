<?php
/**
 * The settings page.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views
 *
 * @var string $feed_url
 * @var string $feed_count
 * @var string $reset_settings_link
 * @var string $enable_customize_tutorial_link
 */

defined( 'ABSPATH' ) || die;
?>

<div class="wrap clientdash">

    <form action="options.php" method="post" id="clientdash-settings-page-form">

		<?php settings_fields( 'clientdash_settings' ); ?>

        <h1 class="clientdash-page-title">
			<?php echo get_admin_page_title(); ?>
        </h1>

		<?php settings_errors(); ?>

        <section class="clientdash-page-wrap">

            <div class="clientdash-page-description">
				<?php
				_e( 'Modify Client Dash settings on this page.', 'client-dash' );
				?>
            </div>

            <h2><?php _e( 'Admin Page Feed', 'client-dash' ); ?></h2>

            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row">
                        <label for="cd_adminpage_feed_url">
							<?php _e( 'Feed URL', 'client-dash' ); ?>
                        </label>
                    </th>

                    <td>
                        <input type="text" name="cd_adminpage_feed_url" id="cd_adminpage_feed_url" class="regular-text"
                               value="<?php echo esc_attr( $feed_url ); ?>"/>

                        <p class="description">
							<?php _e( 'RSS feed url that will be used on the custom Admin Page.', 'client-dash' ); ?>
                        </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="cd_adminpage_feed_count">
							<?php _e( 'Feed Count', 'client-dash' ); ?>
                        </label>
                    </th>

                    <td>
                        <input type="text" name="cd_adminpage_feed_count" id="cd_adminpage_feed_count"
                               class="regular-text"
                               value="<?php echo esc_attr( $feed_count ); ?>"/>

                        <p class="description">
							<?php _e( 'Number of items to display in the feed.', 'client-dash' ); ?>
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>

            <h2><?php _e( 'Other', 'client-dash' ); ?></h2>

            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row">
                        <label for="clientdash_hide_customize_tutorial">
							<?php _e( 'Enable Customize Admin Tutorial', 'client-dash' ); ?>
                        </label>
                    </th>

                    <td>
                        <a href="<?php echo esc_attr( $enable_customize_tutorial_link ); ?>"
                           id="clientdash_hide_customize_tutorial"
                           class="button">
							<?php _e( 'Enable', 'client-dash' ); ?>
                        </a>

                        <p class="description">
							<?php _e( 'Once you hide or complete the tutorial for the "Customize Admin" tool, it will disappear. Click this button to enable it again.', 'client-dahs' ); ?>
                        </p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="cd_reset_all_settings">
							<?php _e( 'Reset All Settings', 'client-dash' ); ?>
                        </label>
                    </th>

                    <td>
                        <a href="<?php echo esc_attr( $reset_settings_link ); ?>" id="cd_reset_all_settings"
                           class="button">
							<?php _e( 'Reset', 'client-dash' ); ?>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>

        </section>

		<?php include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/sidebar.php'; ?>

    </form>

</div>
