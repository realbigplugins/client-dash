<?php
/**
 * The REST Customizations controller.
 *
 * @since {{VERSION}}
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
 * @since {{VERSION}}
 */
class ClientDash_REST_Customizations_Controller {

	/**
	 * ClientDash_REST_Customizations_Controller constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		$this->namespace     = '/clientdash/v1';
		$this->resource_name = 'customizations';
	}

	/**
	 * Register all endpoint routes.
	 *
	 * @since {{VERSION}}
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
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
			),
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
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
	 * @since {{VERSION}}
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function get_item_permissions_check( $request ) {

		global $current_user;

		// TODO Get these permission checks working!
		if ( ! current_user_can( 'customize_admin' ) ) {

			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the customizations resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}

		return true;
	}

	/**
	 * Checks if a given request has access to create a customizations.
	 *
	 * @since {{VERSION}}
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has access to create items, WP_Error object otherwise.
	 */
	public function create_item_permissions_check( $request ) {

		// TODO Get these permission checks working!
		if ( ! current_user_can( 'customize_admin' ) ) {

			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot edit the customizations resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}

		return true;
	}

	/**
	 * Check permissions for the customizations.
	 *
	 * @since {{VERSION}}
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function update_item_permissions_check( $request ) {

		// TODO Get these permission checks working!
		if ( ! current_user_can( 'customize_admin' ) ) {

			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the customizations resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}

		return true;
	}

	/**
	 * Check permissions for the customizations.
	 *
	 * @since {{VERSION}}
	 *
	 * @param WP_REST_Request $request Current request.
	 */
	public function delete_item_permissions_check( $request ) {

		// TODO Get these permission checks working!
		if ( ! current_user_can( 'customize_admin' ) ) {

			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the customizations resource.' ), array( 'status' => $this->authorization_status_code() ) );
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
			'cdpages'   => $request['cdpages'],
		) );

		if ( $results === false ) {

			return new WP_Error(
				'rest_customizations_cant_create',
				__( 'Cannot create customization for unkown reasons.', 'clientdash' ),
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
				__( 'Invalid customizations Role.', 'clientdash' ),
				array( 'status' => 404 )
			);
		}

		$customizations = $this->prepare_item_for_database( $request );

		$results = cd_update_role_customizations( $request['role'], array(
			'menu'      => $request['menu'],
			'submenu'   => $request['submenu'],
			'dashboard' => $request['dashboard'],
			'cdpages'   => $request['cdpages'],
		) );

		if ( $results === false ) {

			return new WP_Error(
				'rest_customizations_cant_update',
				__( 'Cannot update customization for unkown reasons.', 'clientdash' ),
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
				__( 'Invalid customizations Role.', 'clientdash' ),
				array( 'status' => 404 )
			);
		}

		$customizations = cd_get_customizations( $request['role'] );

		if ( ! $customizations ) {

			$response = new WP_REST_Response();
			$response->set_data( array(
				'deleted' => false,
				'message' => __( 'Role has no customizations.', 'clientdash' ),
			) );

			return $response;
		}

		$results = cd_delete_customizations( $request['role'] );

		if ( $results === false ) {

			return new WP_Error(
				'rest_customizations_cant_delete',
				__( 'Cannot delete customization for unknown reasons.', 'clientdash' ),
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

		$response = array();

		$schema = $this->get_item_schema();

		if ( isset( $schema['properties']['menu'] ) ) {

			$response['menu'] = $data['menu'];
		}

		if ( isset( $schema['properties']['submenu'] ) ) {

			$response['submenu'] = $data['submenu'];
		}

		if ( isset( $schema['properties']['dashboard'] ) ) {

			$response['dashboard'] = $data['dashboard'];
		}

		if ( isset( $schema['properties']['cdpages'] ) ) {

			$response['cdpages'] = $data['cdpages'];
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Prepares a single customization for create or update.
	 *
	 * @since {{VERSION}}
	 * @access protected
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return stdClass|WP_Error Post object or WP_Error.
	 */
	protected function prepare_item_for_database( $request ) {

		$customizations = array();

		$schema = $this->get_item_schema()['customizations'];

		if ( isset( $schema['properties']['menu'] ) ) {

			$customizations['menu'] = $request->get_param( 'menu' );
		}

		if ( isset( $schema['properties']['submenu'] ) ) {

			$customizations['submenu'] = $request->get_param( 'submenu' );
		}

		if ( isset( $schema['properties']['dashboard'] ) ) {

			$customizations['dashboard'] = $request->get_param( 'dashboard' );
		}

		if ( isset( $schema['properties']['cdpages'] ) ) {

			$customizations['cdpages'] = $request->get_param( 'cdpages' );
		}

		/**
		 * Filters before instering the customization.
		 *
		 * @since {{VERSION}}
		 */
		return apply_filters( 'rest_pre_insert_cd_customizations', $customizations, $request );
	}

	/**
	 * Retrieves an array of endpoint arguments from the item schema for the controller.
	 *
	 * @since {{VERSION}}
	 * @access public
	 *
	 * @param string $method Optional. HTTP method of the request. The arguments for `CREATABLE` requests are
	 *                       checked for required values and may fall-back to a given default, this is not done
	 *                       on `EDITABLE` requests. Default WP_REST_Server::CREATABLE.
	 *
	 * @return array Endpoint arguments.
	 */
	public function get_endpoint_args_for_item_schema( $method = WP_REST_Server::CREATABLE ) {

		$schema            = $this->get_item_schema();
		$schema_properties = ! empty( $schema['properties'] ) ? $schema['properties'] : array();
		$endpoint_args     = array();

		foreach ( $schema_properties as $field_id => $params ) {

			// Arguments specified as `readonly` are not allowed to be set.
			if ( ! empty( $params['readonly'] ) ) {

				continue;
			}

			$endpoint_args[ $field_id ] = array(
				'validate_callback' => 'rest_validate_request_arg',
				'sanitize_callback' => 'rest_sanitize_request_arg',
			);

			if ( isset( $params['description'] ) ) {
				$endpoint_args[ $field_id ]['description'] = $params['description'];
			}

			if ( WP_REST_Server::CREATABLE === $method && isset( $params['default'] ) ) {
				$endpoint_args[ $field_id ]['default'] = $params['default'];
			}

			if ( WP_REST_Server::CREATABLE === $method && ! empty( $params['required'] ) ) {
				$endpoint_args[ $field_id ]['required'] = true;
			}

			foreach ( array( 'type', 'format', 'enum', 'items' ) as $schema_prop ) {
				if ( isset( $params[ $schema_prop ] ) ) {
					$endpoint_args[ $field_id ][ $schema_prop ] = $params[ $schema_prop ];
				}
			}

			// Merge in any options provided by the schema property.
			if ( isset( $params['arg_options'] ) ) {

				// Only use required / default from arg_options on CREATABLE endpoints.
				if ( WP_REST_Server::CREATABLE !== $method ) {
					$params['arg_options'] = array_diff_key( $params['arg_options'], array(
						'required' => '',
						'default'  => ''
					) );
				}

				$endpoint_args[ $field_id ] = array_merge( $endpoint_args[ $field_id ], $params['arg_options'] );
			}
		}

		return $endpoint_args;
	}

	/**
	 * Get our sample schema for customizations.
	 *
	 * @return array Schema.
	 */
	public function get_item_schema() {

		return array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => 'cd_customizations',
			'type'       => 'object',
			'properties' => array(
				'menu'      => array(
					'description' => esc_html__( 'The customizations menu.', 'clientdash' ),
					'type'        => 'array',
				),
				'submenu'   => array(
					'description' => esc_html__( 'The customizations submenu.', 'clientdash' ),
					'type'        => 'object',
				),
				'dashboard' => array(
					'description' => esc_html__( 'The customizations dashboard.', 'clientdash' ),
					'type'        => 'array',
				),
				'cdpages'   => array(
					'description' => esc_html__( 'The customizations Client Dash Pages.', 'clientdash' ),
					'type'        => 'array',
				),
			),
		);
	}

	/**
	 * Returns proper auth code.
	 *
	 * @since {{VERSION}}
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