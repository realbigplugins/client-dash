<?php
/**
 * The REST Customizations controller.
 *
 * @since 2.0.0
 *
 * @package ClientDash
 * @subpackage ClientDash/core
 */

defined( 'ABSPATH' ) || die;

/**
 * Class ClientDash_REST_Customizations_Controller
 *
 * The REST Customizations controller.
 *
 * @since 2.0.0
 */
class ClientDash_REST_Customizations_Controller {

	/**
	 * ClientDash_REST_Customizations_Controller constructor.
	 *
	 * @since 2.0.0
	 */
	function __construct() {

		$this->namespace     = '/clientdash/v1';
		$this->resource_name = 'customizations';
	}

	/**
	 * Register all endpoint routes.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->resource_name . '/(?P<role>.+)', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
//				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
//				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
			array(
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'                => array(
					'force' => array(
						'type'        => 'boolean',
						'default'     => false,
						'description' => __( 'Whether to bypass trash and force deletion.' ),
					),
				),
			),
			'schema' => array( $this, 'get_item_schema' ),
		) );
	}

	/**
	 * Check permissions for the customizations.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_item_permissions_check( $request ) {

		if ( ! current_user_can( 'customize_admin' ) ) {

			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the customizations resource.', 'client-dash' ), array( 'status' => $this->authorization_status_code() ) );
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create a customizations.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {

		if ( ! current_user_can( 'customize_admin' ) ) {

			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot edit the customizations resource.', 'client-dash'  ), array( 'status' => $this->authorization_status_code() ) );
		}

		return true;
	}

	/**
	 * Check permissions for the customizations.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function update_item_permissions_check( $request ) {

		if ( ! current_user_can( 'customize_admin' ) ) {

			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the customizations resource.', 'client-dash'  ), array( 'status' => $this->authorization_status_code() ) );
		}

		return true;
	}

	/**
	 * Check permissions for the customizations.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function delete_item_permissions_check( $request ) {

		if ( ! current_user_can( 'customize_admin' ) ) {

			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the customizations resource.', 'client-dash'  ), array( 'status' => $this->authorization_status_code() ) );
		}

		return true;
	}

	/**
	 * Gets a customization.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_item( $request ) {

		$role = $request['role'];

		$customizations = cd_get_customizations( $role );

		$response = $this->prepare_item_for_response( $customizations );

		return $response;
	}

	/**
	 * Creates a customization.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function create_item( $request ) {

		$results = cd_update_role_customizations( $request['role'], array(
			'menu'      => $request['menu'],
			'submenu'   => $request['submenu'],
			'dashboard' => $request['dashboard'],
		) );

		if ( $results === false ) {

			return new WP_Error(
				'rest_customizations_cant_create',
				__( 'Cannot create customization for unkown reasons.', 'client-dash' ),
				array( 'status' => 500 )
			);
		}

		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->resource_name, $results ) ) );

		return $response;
	}

	/**
	 * Updates a customization.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function update_item( $request ) {

		$role = $request['role'];

		if ( empty( $role ) ) {

			return new WP_Error(
				'rest_customizations_invalid_role',
				__( 'Invalid customizations Role.', 'client-dash' ),
				array( 'status' => 404 )
			);
		}

		$customizations = $this->prepare_item_for_database( $request );

		$results = cd_update_role_customizations( $request['role'], array(
			'menu'      => $request['menu'],
			'submenu'   => $request['submenu'],
			'dashboard' => $request['dashboard'],
		) );

		if ( $results === false ) {

			return new WP_Error(
				'rest_customizations_cant_update',
				__( 'Cannot update customization for unkown reasons.', 'client-dash' ),
				array( 'status' => 500 )
			);
		}

		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $customizations );

		return rest_ensure_response( $response );
	}

	/**
	 * Deletes a customization.
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function delete_item( $request ) {

		$role = $request['role'];

		if ( empty( $role ) ) {

			return new WP_Error(
				'rest_customizations_invalid_role',
				__( 'Invalid customizations Role.', 'client-dash' ),
				array( 'status' => 404 )
			);
		}

		$customizations = cd_get_customizations( $request['role'] );

		if ( ! $customizations ) {

			$response = new WP_REST_Response();
			$response->set_data( array(
				'deleted' => false,
				'message' => __( 'Role has no customizations.', 'client-dash' ),
			) );

			return $response;
		}

		$results = cd_delete_customizations( $request['role'] );

		if ( $results === false ) {

			return new WP_Error(
				'rest_customizations_cant_delete',
				__( 'Cannot delete customization for unknown reasons.', 'client-dash' ),
				array( 'status' => 500 )
			);
		}

		$request->set_param( 'context', 'edit' );

		$previous = $this->prepare_item_for_response( $customizations, $request );
		$response = new WP_REST_Response();
		$response->set_data( array(
			'deleted'  => true,
			'previous' => $previous->get_data(),
		) );

		return rest_ensure_response( $response );
	}

	/**
	 * Matches the cusotmizations data to the schema we want.
	 *
	 * @param array $data
	 */
	public function prepare_item_for_response( $data ) {

		$response = array(
			'menu'      => $data['menu'],
			'submenu'   => $data['submenu'],
			'dashboard' => $data['dashboard'],
		);

		return rest_ensure_response( $response );
	}

	/**
	 * Prepares a single customization for create or update.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return stdClass|WP_Error Post object or WP_Error.
	 */
	protected function prepare_item_for_database( $request ) {

		$customizations = array(
			'menu'      => $request->get_param( 'menu' ),
			'submenu'   => $request->get_param( 'submenu' ),
			'dashboard' => $request->get_param( 'dashboard' ),
		);

		/**
		 * Filters before instering the customization.
		 *
		 * @since 2.0.0
		 */
		return apply_filters( 'rest_pre_insert_cd_customizations', $customizations, $request );
	}

	/**
	 * Returns proper auth code.
	 *
	 * @since 2.0.0
	 *
	 * @return int
	 */
	public function authorization_status_code() {

		$status = 401;

		if ( is_user_logged_in() ) {
			$status = 403;
		}

		return $status;
	}
}