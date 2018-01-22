<?php
/**
 * The Admin Page page.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core/pluginpages/views
 *
 * @var string $admin_page_title
 * @var string $admin_page_content
 */

defined( 'ABSPATH' ) || die;
?>

<div class="wrap clientdash">

    <h1 class="clientdash-page-title">
		<?php echo get_admin_page_title(); ?>
		<?php
		cd_field_tip( __( 'This is where you can manage your custom Admin Page. Use this page for anything like, such as: your company landing page, a help page for employees using this website, displaying all of your favorite kinds of ice cream, so much more.', 'client-dash' ) );
		?>
    </h1>

	<?php settings_errors(); ?>

    <section class="clientdash-page-wrap">

        <form method="post" action="options.php" id="clientdash-admin-page-form">

			<?php settings_fields( 'clientdash_admin_page' ); ?>

			<?php wp_editor( $admin_page_content, 'cd_adminpage_content' ); ?>

        </form>

    </section>

	<?php include_once CLIENTDASH_DIR . 'core/plugin-pages/views/sidebar/sidebar.php'; ?>

</div>
