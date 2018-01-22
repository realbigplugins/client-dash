<?php
/**
 * Core CD Page output
 *
 * Page: Help
 * Tab: Info
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/core-pages/views/help
 *
 * @var string $wp_version
 * @var WP_Theme $current_theme
 * @var false|WP_Theme $child_theme
 * @var array $active_plugins
 * @var array $installed_plugins
 */

defined( 'ABSPATH' ) || die();
?>

<div class="cd-content-section">
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e( 'Current WordPress version', 'client-dash' ); ?></th>
            <td><?php echo $wp_version; ?></td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e( 'Current theme', 'client-dash' ); ?></th>
            <td>
                <a href="<?php echo $current_theme->get( 'Theme URI' ); ?>">
					<?php echo $current_theme; ?>
                </a>

				<?php
				if ( $child_theme ) {

					printf(
                        /* translators: %s is a child theme name */
						__( 'child of %s', 'client-dash' ),
						$child_theme
					);
				}
				?>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e( 'Active plugins', 'client-dash' ); ?></th>
            <td>
                <ul>
					<?php foreach ( $active_plugins as $plugin ) : ?>
                        <li>
							<?php echo $plugin['Name']; ?>
                        </li>
					<?php endforeach; ?>
                </ul>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e( 'Installed plugins', 'client-dash' ); ?></th>
            <td>
                <ul>
		            <?php foreach ( $installed_plugins as $plugin ) : ?>
                        <li>
				            <?php echo $plugin['Name']; ?>
                        </li>
		            <?php endforeach; ?>
                </ul>
            </td>
        </tr>
    </table>
</div>