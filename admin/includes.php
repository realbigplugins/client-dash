<?php

/**
 * Registers all Client Dash files.
 *
 * Client Dash @since 0.1
 */
function client_dash_register_files() {
	wp_register_script( 'client-dash', plugins_url( '../js/client-dash.js', __FILE__ ), array( 'jquery' ), null, true );
	wp_register_style( 'client-dash', plugins_url( '../css/client-dash.css', __FILE__ ), array(), null );
}

add_action( 'admin_init', 'client_dash_register_files' );

/**
 * Enqueues all Client Dash files.
 *
 * Only does so if user meets privileges.
 *
 * Client Dash @since 0.1
 */
function client_dash_enqueue_files() {
	if ( current_user_can( 'publish_posts' ) ) {
		wp_enqueue_script( 'client-dash' );
		wp_enqueue_style( 'client-dash' );
	}
}

add_action( 'admin_enqueue_scripts', 'client_dash_enqueue_files' );

/**
 * Dynamically sets up styles based on current Wordpress color scheme.
 */
function client_dash_icon_colors() {
	$colors = cd_get_color_scheme( null );
	echo '
	<style>
	  .cd-icon{
	  	color: ' . $colors['primary'] . ';
	  }
	  .cd-icon:hover{
	  	color: ' . $colors['secondary'] . ';
	  }
	.cd-dashicons-grid-item.active .container{
		background-color: ' . $colors['tertiary'] . ';
		color: #eee;
	}
	#cd-dashicons-selections .dashicons.active{
		color: ' . $colors['secondary'] . ';
	</style>
  ';
}

add_action( 'admin_head', 'client_dash_icon_colors' );