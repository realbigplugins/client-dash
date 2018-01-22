<?php
/**
 * Outputs the Dashicon selector.
 *
 * @since 2.0.0
 *
 * @package ClientDashPro
 * @subpackage ClientDashPro/core/includes/views
 *
 * @var array $icons
 * @var array $args
 */

defined( 'ABSPATH' ) || die();
?>

<div class="cd-dashicon-selector">
	<span class="cd-dashicon-selector-preview dashicons <?php echo esc_attr( $args['selected'] ); ?>"
	      data-toggle></span>
	<ul>
		<?php foreach ( $icons as $icon ) : ?>
			<li data-icon="<?php echo esc_attr( $icon ); ?>"
				<?php echo $icon == $args['selected'] ? 'class="cd-dashicon-selector-selected"' : ''; ?>>
				<span class="dashicons <?php echo esc_attr( $icon ); ?>"></span>
			</li>
		<?php endforeach; ?>
	</ul>

	<input type="hidden" name="<?php echo esc_attr( $args['name'] ); ?>"
	       value="<?php echo esc_attr( $args['selected'] ); ?>"/>
</div>