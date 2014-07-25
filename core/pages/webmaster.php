<?php
/**
 * Class ClientDash_Page_Webmaster
 *
 * Creates the toolbar sub-menu item and the page for Webmaster.
 *
 * @package WordPress
 * @subpackage Client Dash
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

		if ( apply_filters( 'cd_show_webmaster_page', true )
		     && ! get_option( 'cd_hide_page_webmaster' )
		     && get_option( 'cd_webmaster_enable', false ) == '1'
		     && ! empty( $ClientDash->content_blocks['webmaster'] )
		) {
			add_submenu_page(
				'index.php',
				'Webmaster Information',
				'Webmaster',
				'publish_posts',
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