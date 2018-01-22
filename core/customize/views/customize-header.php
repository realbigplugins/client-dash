<?php
/**
 * The header file for the customizer.
 *
 * @since 2.0.0
 * @subpackage ClientDash/core/customize/views
 */

defined( 'ABSPATH' ) || die;
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>