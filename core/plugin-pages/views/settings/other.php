<?php
/**
 * Settings page section for Other.
 *
 * @since 2.0.0
 *
 * @var string $reset_settings_link
 * @var string $enable_customize_tutorial_link
 */

defined( 'ABSPATH' ) || die();
?>

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
				<?php _e( 'Once you hide or complete the tutorial for the "Customize Admin" tool, it will disappear. Click this button to enable it again.', 'client-dash' ); ?>
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