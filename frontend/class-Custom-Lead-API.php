<?php

require_once(explode("wp-content", __FILE__)[0] . "wp-load.php");
require_once __DIR__ . '/class-EduLead-Helper.php';
class Custom_Lead_API extends WP_REST_Controller
{

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes()
	{
		$version = '1';
		$namespace = 'marketplace/v' . $version;
		$base = 'leads';
		register_rest_route($namespace, '/' . $base, array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'get_items'),
				'permission_callback' => array($this, 'get_items_permissions_check'),
				'args' => array(),
			),
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback' => array($this, 'create_item'),
				'permission_callback' => array($this, 'create_item_permissions_check'),
				'args' => $this->get_endpoint_args_for_item_schema(true),
			),
		));
		register_rest_route($namespace, '/' . $base . '/(?P<id>[\d]+)', array(
			array(
				'methods' => WP_REST_Server::READABLE,
				'callback' => array($this, 'get_item'),
				'permission_callback' => array($this, 'get_item_permissions_check'),
				'args' => array(
					'context' => array(
						'default' => 'view',
					),
				),
			),
			array(
				'methods' => WP_REST_Server::EDITABLE,
				'callback' => array($this, 'update_item'),
				'permission_callback' => array($this, 'update_item_permissions_check'),
				'args' => $this->get_endpoint_args_for_item_schema(false),
			),
			array(
				'methods' => WP_REST_Server::DELETABLE,
				'callback' => array($this, 'delete_item'),
				'permission_callback' => array($this, 'delete_item_permissions_check'),
				'args' => array(
					'force' => array(
						'default' => false,
					),
				),
			),
		));
		register_rest_route($namespace, '/' . $base . '/schema', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this, 'get_public_item_schema'),
		));
		register_rest_route($namespace, '/' . $base . '/details', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this, 'get_lead_details'),
		));
		register_rest_route($namespace, '/' . $base . '/sethidden', array(
			'methods' => WP_REST_Server::EDITABLE,
			'callback' => array($this, 'persist_hidden_status'),
			'args' => $this->get_endpoint_args_for_item_schema(true),
		));
		register_rest_route($namespace, '/' . $base . '/setunlock', array(
			'methods' => WP_REST_Server::EDITABLE,
			'callback' => array($this, 'persist_unlock_status'),
			'args' => $this->get_endpoint_args_for_item_schema(true),
		));
	}

	/**
	 * Persist unlock status to the database
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function persist_unlock_status($request)
	{
		$lead_id = $request->get_param('lead_id');
		$unlock_status = $request->get_param('unlock_status');
		$unlock_status = (($unlock_status === 'true') ? '1' : '0');
		$userId = apply_filters('determine_current_user', false);
		if ($userId <= 0) {
			return new WP_Error("User($userId) is not logged in");
		}
		$eduLeadHelper = new EduLead_Helper();
		$dbStatus = $eduLeadHelper->set_card_unlock_status_to_db($userId, $lead_id, $unlock_status);
		return $dbStatus;
	}

	/**
	 * Persist hidden status to the database
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function persist_hidden_status($request)
	{
		$lead_id = $request->get_param('lead_id');
		$hidden_status = $request->get_param('hidden_status');
		$hidden_status = (($hidden_status === 'true') ? '1' : '0');
		$userId = apply_filters('determine_current_user', false);
		if ($userId <= 0) {
			return new WP_Error("User($userId) is not logged in");
		}
		$eduLeadHelper = new EduLead_Helper();
		$eduLeadHelper->set_card_hidden_status_to_db($userId, $lead_id, $hidden_status);

		$data_object = array();
		$data_object[] = "Successfully updated $lead_id's hidden_status to $hidden_status in the database";
		$response = new WP_REST_Response($data_object);
		return $response;
	}

	/**
	 * Get details of all the leads
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_lead_details($request)
	{
		//$card1 = new Lead_Card('Rohit', 'Lucknow', 'CEO', 'Nirvana');
		//$card2 = new Lead_Card('Anantharam', 'Chennai', 'CTO', 'Relationship');

		$userId = apply_filters('determine_current_user', false);
		if ($userId <= 0) {
			return new WP_Error("User($userId) is not logged in");
		}

		$eduLeadHelper = new EduLead_Helper();
		$data_object = $eduLeadHelper->get_lead_details_from_db($userId);

		// Create the response object
		$response = new WP_REST_Response($data_object);

		// Add a custom status code
		$response->set_status(201);

		// Add a custom header
		//$response->header( 'Referrer', 'http://www.google.com/' );

		return $response;
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items($request)
	{
		$items = array(); //do a query, call another class, etc
		$data = array();
		foreach ($items as $item) {
			$itemdata = $this->prepare_item_for_response($item, $request);
			$data[] = $this->prepare_response_for_collection($itemdata);
		}

		return new WP_REST_Response($data, 200);
	}

	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item($request)
	{
		//get parameters from request
		$params = $request->get_params();
		$item = array();//do a query, call another class, etc
		$data = $this->prepare_item_for_response($item, $request);

		//return a response or error based on some conditional
		if (1 == 1) {
			return new WP_REST_Response($data, 200);
		} else {
			return new WP_Error('code', __('message', 'text-domain'));
		}
	}

	/**
	 * Create one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function create_item($request)
	{

		$item = $this->prepare_item_for_database($request);

		if (function_exists('slug_some_function_to_create_item')) {
			$data = slug_some_function_to_create_item($item);
			if (is_array($data)) {
				return new WP_REST_Response($data, 200);
			}
		}

		return new WP_Error('cant-create', __('message', 'text-domain'), array('status' => 500));


	}

	/**
	 * Update one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function update_item($request)
	{
		$item = $this->prepare_item_for_database($request);

		if (function_exists('slug_some_function_to_update_item')) {
			$data = slug_some_function_to_update_item($item);
			if (is_array($data)) {
				return new WP_REST_Response($data, 200);
			}
		}

		return new WP_Error('cant-update', __('message', 'text-domain'), array('status' => 500));

	}

	/**
	 * Delete one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function delete_item($request)
	{
		$item = $this->prepare_item_for_database($request);

		if (function_exists('slug_some_function_to_delete_item')) {
			$deleted = slug_some_function_to_delete_item($item);
			if ($deleted) {
				return new WP_REST_Response(true, 200);
			}
		}

		return new WP_Error('cant-delete', __('message', 'text-domain'), array('status' => 500));
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_items_permissions_check($request)
	{
		//return true; <--use to make readable by all
		return current_user_can('edit_something');
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_item_permissions_check($request)
	{
		return $this->get_items_permissions_check($request);
	}

	/**
	 * Check if a given request has access to create items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function create_item_permissions_check($request)
	{
		return current_user_can('edit_something');
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function update_item_permissions_check($request)
	{
		return $this->create_item_permissions_check($request);
	}

	/**
	 * Check if a given request has access to delete a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function delete_item_permissions_check($request)
	{
		return $this->create_item_permissions_check($request);
	}

	/**
	 * Prepare the item for create or update operation
	 *
	 * @param WP_REST_Request $request Request object
	 * @return WP_Error|object $prepared_item
	 */
	protected function prepare_item_for_database($request)
	{
		return array();
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @param mixed $item WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response($item, $request)
	{
		return array();
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params()
	{
		return array(
			'page' => array(
				'description' => 'Current page of the collection.',
				'type' => 'integer',
				'default' => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'description' => 'Maximum number of items to be returned in result set.',
				'type' => 'integer',
				'default' => 10,
				'sanitize_callback' => 'absint',
			),
			'search' => array(
				'description' => 'Limit results to those matching a string.',
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}

try {
	$leadAPI = new Custom_Lead_API();
	//$leadAPI->register_routes();
	add_action('rest_api_init', [$leadAPI, 'register_routes']);
} catch (Exception $e) {
	echo 'Message: ' . $e->getMessage();
}

?>
