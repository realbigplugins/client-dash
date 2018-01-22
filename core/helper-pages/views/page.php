<?php
/**
 * Core CD Page: Account
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/core-pages/views
 *
 * @var string $active_tab
 * @var array $cd_page
 */

defined( 'ABSPATH' ) || die();
?>

<div class="wrap cd-account">
    <h2 class="cd-title">
        <span class="cd-icon dashicons <?php echo esc_attr( $cd_page['icon'] ); ?>"></span>
		<?php echo esc_html( $cd_page['title'] ); ?>
    </h2>

	<?php if ( count( $cd_page['tabs'] ) > 1 ) : ?>
        <p class="nav-tab-wrapper">
			<?php foreach ( $cd_page['tabs'] as $tab_ID => $tab ) : ?>
                <a href="?page=<?php echo esc_attr( $_GET['page'] ); ?>&amp;tab=<?php echo esc_attr( $tab_ID ); ?>"
                   class="nav-tab <?php echo $tab_ID == $active_tab ? 'nav-tab-active' : ''; ?>">
					<?php echo esc_html( $tab['title'] ); ?>
                </a>
			<?php endforeach; ?>
        </p>
	<?php endif; ?>

	<?php
	if ( is_callable( $cd_page['tabs'][ $active_tab ]['callback'] ) ) {

		call_user_func( $cd_page['tabs'][ $active_tab ]['callback'] );
	}
	?>
</div>