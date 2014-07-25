<?php

/**
 * Class ClientDash_Page_Account
 *
 * Creates the toolbar sub-menu item and the page for Account.
 *
 * @package WordPress
 * @subpackage Client Dash
 *
 * @since Client Dash 1.5
 */
class ClientDash_Page_Account extends ClientDash {
	/**
	 * The main construct function.
	 *
	 * @since Client Dash 1.5
	 */
	function __construct() {
		// Add the menu item to the toolbar
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
	}

	/**
	 * Adds the sub-menu item to the toolbar.
	 *
	 * @since Client Dash 1.5
	 */
	public function add_submenu_page() {
		global $ClientDash;

		// Check to make sure there is a content block set, there aren't any filters disabling
		// the page, and the page isn't disabled in Settings
		if ( ! empty( $ClientDash->content_blocks['account'] )
		     && apply_filters( 'cd_show_account_page', true )
		     && ! get_option( 'cd_hide_page_account' )
		) {
			add_submenu_page(
				'index.php',
				'Account Information',
				'Account',
				'publish_posts',
				'cd_account',
				array( $this, 'page_output' )
			);
		}
	}

	/**
	 * The page content.
	 *
	 * @since Client Dash 1.5
	 */
	public function page_output() {
		?>
		<div class="wrap cd-account">
			<?php
			$this->the_page_title( 'account' );
			$this->create_tab_page();
			?>
		</div>
	<?php
	}
}