<?php
/**
 * Core CD Page output
 *
 * Page: Help
 * Tab: Domain
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/core-pages/views/help
 *
 * @var string $domain
 * @var string $ip
 * @var string $dns
 */

defined( 'ABSPATH' ) || die();
?>

<div class="cd-content-section">
    <table class="form-table">
        <tr valign="top">
            <th scope="row"><?php _e( 'Current site domain', 'client-dash' ); ?></th>
            <td><?php echo $domain; ?></td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e( 'Domain IP address', 'client-dash' ); ?></th>
            <td><?php echo $ip; ?></td>
        </tr>
    </table>

    <h3><?php _e( 'DNS Records', 'client-dash' ); ?></h3>

    <table class="form-table">

        <?php foreach ( $dns as $dns ) : ?>
            <tr valign="top">
                <th scope="row"><?php echo $dns['type']; ?></th>
                <td>
                    <ul>
						<?php foreach ( $dns as $name => $value ) : ?>
							<?php
                            if ( $name == 'type' ) {
								continue;
							}
							?>

                            <li><?php echo "$name: $value"; ?></li>
						<?php endforeach; ?>
                    </ul>
                </td>
            </tr>
		<?php endforeach; ?>

    </table>
</div>