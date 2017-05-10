<?php
/**
 * API functionality.
 *
 * @since {{VERSION}}
 *
 * @package ClientDash
 * @subpackage ClientDash/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Class ClientDash_API
 *
 * API functionality.
 *
 * @since {{VERSION}}
 */
class ClientDash_API {

	/**
	 * The controller for the REST Customizations endpoint.
	 *
	 * @since {{VERSION}}
	 *
	 * @var ClientDash_REST_Customizations_Controller
	 */
	public $controller_customizations;

	/**
	 * ClientDash_API constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Registers all routes.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function register_rest_routes() {

		require_once CLIENTDASH_DIR . 'core/api/class-clientdash-rest-customizations-controller.php';

		$this->controller_customizations = new ClientDash_REST_Customizations_Controller();
		$this->controller_customizations->register_routes();
	}
}