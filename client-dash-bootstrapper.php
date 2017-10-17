<?php
/**
 * Bootstrapper for the plugin.
 *
 * Makes sure everything is good to go for loading the plugin, and then loads it.
 *
 * @since {{VERSION}}
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
 * @since {{VERSION}}
 */
class ClientDash_Bootstrapper {

	/**
	 * Notices to show if cannot load.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @var array
	 */
	private $notices = array();

	/**
	 * ClientDash_Bootstrapper constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'maybe_disable_client_dash_pro' ), 9 );
		add_action( 'plugins_loaded', array( $this, 'maybe_load' ), 1 );
	}

	/**
	 * Disable Client Dash Pro, if version is too low.
	 *
	 * Client Dash Pro, when configured for Client Dash < 2.0, will break with > 2.0 versions. Removing the load hook
	 * is tricky because the Bootstrapper instance is not accessible, so I must loop through the tags to find it
	 * manually so I can remove it.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function maybe_disable_client_dash_pro() {

		// Don't disable if active and past version 1.1
		if ( ! defined( 'CLIENTDASH_PRO_VERSION' ) || version_compare( CLIENTDASH_PRO_VERSION, '1.1', '>=' ) ) {
			return;
		}

		global $wp_filter;

		if ( isset( $wp_filter['plugins_loaded'] ) && isset( $wp_filter['plugins_loaded']->callbacks['10'] ) ) {

			foreach ( $wp_filter['plugins_loaded']->callbacks['10'] as $id => $callback ) {

				if ( is_array( $callback['function'] ) && $callback['function'][0] instanceof ClientDash_Pro_Bootstrapper ) {

					remove_action( 'plugins_loaded', array( $callback['function'][0], 'maybe_load' ) );
					add_action( 'admin_notices', array( $this, 'disable_cd_pro_admin_notice' ) );
				}
			}
		}
	}

	/**
	 * Maybe loads the plugin.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function maybe_load() {

		$php_version = phpversion();
		$wp_version  = get_bloginfo( 'version' );

		// Minimum PHP version
		if ( version_compare( $php_version, '5.3.0' ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum PHP version of 5.3.0 required. Current version is %s. Please contact your system administrator to upgrade PHP to its latest version.', 'clientdash' ),
				$php_version
			);
		}

		// Minimum WordPress version
		if ( version_compare( $wp_version, '4.7.0' ) === - 1 ) {

			$this->notices[] = sprintf(
				__( 'Minimum WordPress version of 4.0.0 required. Current version is %s. Please contact your system administrator to upgrade WordPress to its latest version.', 'clientdash' ),
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
	 * @since {{VERSION}}
	 * @access private
	 */
	private function load() {

		ClientDash();
	}

	/**
	 * Shows notices on failure to load.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function notices() {
		?>
        <div class="notice error">
            <p>
				<?php
				printf(
					__( '%sClient Dash%s could not load because of the following errors:', 'clientdash' ),
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
	 * Shows notice on disabling of CD Pro.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function disable_cd_pro_admin_notice() {
		?>
        <div class="notice error">
            <p>
				<?php
				printf(
					__( '%sClient Dash Pro%s could not load because it needs to be upgraded first.', 'clientdash' ),
					'<strong>',
					'</strong>'
				);
				?>
            </p>
        </div>
		<?php
	}
}