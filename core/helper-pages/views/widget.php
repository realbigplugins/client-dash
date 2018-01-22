<?php
/**
 * The Core CD Pages' widget.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/core-pages/views
 *
 * @var string $icon
 * @var string $link
 */

defined( 'ABSPATH' ) || die();
?>

<a href="<?php echo esc_attr( $link ); ?>" class="cd-core-widget">
    <span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
</a>