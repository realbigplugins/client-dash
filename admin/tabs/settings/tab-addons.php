<?php

/**
 * Outputs Addons tab under Settings page.
 */
function cd_core_settings_addons_tab() {
	// Declare addons
	$addons = array(
		'Client Dash WP Help Addon' => 'http://wordpress.org/plugins/client-dash-wp-help-add-on/'
	);

	// Get rid of submit button on this page
	add_filter( 'cd_submit', '__return_false' );
	?>

	<h3>Available Client Dash Addons</h3>
	<div>
		<?php
		foreach ( $addons as $key => $value ) {
			echo '<div class="cd-addon">';
			echo '<a href="' . $value . '"><span></span>';
			echo '<h4>' . $key . '</h4></a>';
			echo '</div>';
		}
		?>
	</div>
<?php
}

add_action( 'cd_settings_addons_tab', 'cd_core_settings_addons_tab' );