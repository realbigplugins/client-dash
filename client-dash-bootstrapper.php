<?php
/**
 * Bootstrapper for the plugin.
 *
 * Makes sure everything is good to go for loading the plugin, and then loads it.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 */

defined( 'ABSPATH' ) || die;

/**
 * Class ClientDash_Bootstrapper
 *
 * Bootstrapper for the plugin.
 *
 * Makes sure everything is good to go for loading the plugin, and then loads it.
 *
 * @since 2.0.0
 */
class ClientDash_Bootstrapper {

	/**
	 * Notices to show if cannot load.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * ClientDash_Bootstrapper constructor.
	 *
	 * @since 2.0.0
	 */
	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'maybe_nag_client_dash_pro' ), 9 );
		add_action( 'plugins_loaded', array( $this, 'maybe_load' ), 1 );
	}

	/**
	 * Potentially nag to upgrade Pro.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function maybe_nag_client_dash_pro() {

		// Don't disable if active and past version 1.1
		if ( ! defined( 'CLIENTDASH_PRO_VERSION' ) ) {
			return;
		}

		if ( version_compare( CLIENTDASH_PRO_VERSION, '1.1' ) !== - 1 ) {
			return;
		}

		add_action( 'admin_notices', array( $this, 'cd_pro_admin_notice' ) );

		// Some disabling
		remove_action( 'admin_init', array( ClientDash_Pro()->admin, 'register_settings' ) );
		remove_action( 'admin_notices', array( ClientDash_Pro()->admin, 'licensing_notice' ) );
		remove_action( 'cd_sidebar', array( 'CD_Pro_Admin', 'sidebar_support' ) );
		remove_action( 'admin_init', array( ClientDash_Pro()->admin, 'send_support_email' ) );
		remove_filter( 'parent_file', array( ClientDash_Pro()->admin_pages->admin, 'activate_settings_menu' ) );
		remove_filter( 'submenu_file', array( ClientDash_Pro()->admin_pages->admin, 'activate_clientdash_submenu' ) );
		remove_action( 'all_admin_notices', array( ClientDash_Pro()->admin_pages->admin, 'output_clientdash_menu' ) );
		remove_action( 'admin_init', array( ClientDash_Pro()->admin_pages->admin, 'redirect_cd_tab' ) );
	}

	/**
	 * Maybe loads the plugin.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function maybe_load() {

		$php_version = phpversion();
		$wp_version  = get_bloginfo( 'version' );

		// Minimum PHP version
		if ( version_compare( $php_version, '5.3.0' ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum PHP version of 5.3.0 required. Current version is %s. Please contact your system administrator to upgrade PHP to its latest version.', 'client-dash' ),
				$php_version
			);
		}

		// Minimum WordPress version
		if ( version_compare( $wp_version, '4.7.0' ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum WordPress version of 4.0.0 required. Current version is %s. Please contact your system administrator to upgrade WordPress to its latest version.', 'client-dash' ),
				$wp_version
			);
		}

		// Don't load and show errors if incompatible environment.
		if ( ! empty( $this->notices ) ) {

			add_action( 'admin_notices', array( $this, 'notices' ) );

			return;
		}

		$this->load();
	}

	/**
	 * Loads the plugin.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function load() {

		ClientDash();
	}

	/**
	 * Shows notices on failure to load.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function notices() {
		?>
        <div class="notice error">
            <p>
				<?php
				printf(
					__( '%sClient Dash%s could not load because of the following errors:', 'client-dash' ),
					'<strong>',
					'</strong>'
				);
				?>
            </p>

            <ul>
				<?php foreach ( $this->notices as $notice ) : ?>
                    <li>
						<?php echo $notice; ?>
                    </li>
				<?php endforeach; ?>
            </ul>
        </div>
		<?php
	}

	/**
	 * Shows notice on upgrading of CD Pro.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	function cd_pro_admin_notice() {
		?>
        <div class="notice error">
            <p>
				<?php
				printf(
					__( '%sClient Dash Pro%s needs to be updated to version 1.1 or it will not perform properly. Please update Client Dash Pro immediately.', 'client-dash' ),
					'<strong>',
					'</strong>'
				);
				?>
            </p>
        </div>
		<?php
	}
}