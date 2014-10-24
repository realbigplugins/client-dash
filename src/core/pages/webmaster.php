<?php

/**
 * Class ClientDash_Page_Webmaster
 *
 * Creates the toolbar sub-menu item and the page for Webmaster.
 *
 * @package WordPress
 * @subpackage ClientDash
 *
 * @category Pages
 *
 * @since Client Dash 1.5
 */
class ClientDash_Page_Webmaster extends ClientDash {

	function __construct() {

		// Add the menu item to the toolbar
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
	}

	public function add_submenu_page() {

		global $ClientDash;

		$webmaster = get_option( 'cd_webmaster_name', $ClientDash->option_defaults['webmaster_name'] );

		if ( ! empty( $ClientDash->content_sections['webmaster'] ) ) {
			add_submenu_page(
				'index.php',
				$webmaster,
				$webmaster,
				'read',
				'cd_webmaster',
				array( $this, 'page_output' )
			);
		}
	}

	public function page_output() {

		?>
		<div class="wrap cd-webmaster">
			<?php
			$this->the_page_title( 'webmaster' );
			$this->create_tab_page();
			?>
		</div>
	<?php
	}
}