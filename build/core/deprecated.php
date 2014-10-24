<?php

/**
 * This file contains all functions that could be deprecated.
 *
 * @since Client Dash 1.5
 */

/**
 * Creates content blocks.
 *
 * Deprecated
 *
 * @param null $name Deprecated.
 * @param null $page Deprecated.
 * @param null $tab Deprecated.
 * @param null $callback Deprecated.
 * @param int $priority Deprecated.
 */
function cd_content_block( $name = null, $page = null, $tab = null, $callback = null, $priority = 10 ) {

	global $ClientDash;

	$content_section['name'] = $name;
	$content_section['page'] = $page;
	$content_section['tab'] = $tab;
	$content_section['callback'] = $callback;

	if ( ! isset( $content_section['priority'] ) ) {
		$content_section['priority'] = 10;
	}

	// Generate the content section ID
	$ID = $ClientDash->translate_name_to_id( $content_section['name'] );

	// Fix up the tab name (to allow spaces and such)
	$tab_ID = $ClientDash->translate_name_to_id( $content_section['tab'] );

	// Fix up the page name (to allow spaces and such)
	$page = $ClientDash->translate_name_to_id( $content_section['page'] );

	$ClientDash->content_sections[ $page ][ $tab_ID ] = array(
		'name'             => $content_section['tab'],
		'content-sections' => array(
			$ID => array(
				'name'     => $content_section['name'],
				'callback' => $content_section['callback'],
				'priority' => $content_section['priority']
			)
		)
	);

	// Also add for the unmodified version
	$ClientDash->content_sections_unmodified[ $page ][ $tab_ID ] = array(
		'name'             => $content_section['tab'],
		'content-sections' => array(
			$ID => array(
				'name'     => $content_section['name'],
				'callback' => $content_section['callback'],
				'priority' => $content_section['priority']
			)
		)
	);
}