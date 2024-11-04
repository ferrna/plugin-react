<?php
class My_REST_Posts_Controller {
	public function __construct() {
		$this->namespace     = '/reactminiapp/v1';
		$this->resource_name = 'data';
	}

	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->resource_name, array(
			array(
				'methods'   => 'GET',
				'callback'  => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
			),
			'schema' => array( $this, 'get_item_schema' ),
		) );
		register_rest_route( $this->namespace, '/' . $this->resource_name . '/(?P<id>[\d]+)', array(
			// Notice how we are registering multiple endpoints the 'schema' equates to an OPTIONS request.
			array(
				'methods'   => 'GET',
				'callback'  => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
			),
			// Register our schema callback.
			'schema' => array( $this, 'get_item_schema' ),
		) );
	}

	public function get_items_permissions_check( $request ) {
		if ( ! current_user_can( 'read' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		return true;
	}

	public function get_items( $request ) {
		$args = array(
			'post_per_page' => 5,
		);
		$posts = get_posts( $args );

		$data = array();

		if ( empty( $posts ) ) {
			return rest_ensure_response( $data );
		}

		foreach ( $posts as $post ) {
			$response = $this->prepare_item_for_response( $post, $request );
			$data[] = $this->prepare_response_for_collection( $response );
		}

		return rest_ensure_response( $data );
	}
}

// Function to register our new routes from the controller.
function prefix_register_my_rest_routes() {
	$controller = new My_REST_Posts_Controller();
	$controller->register_routes();
}

add_action( 'rest_api_init', 'prefix_register_my_rest_routes' );


//class RMA_API_Routes {
//    public function __construct() {
//        add_action('rest_api_init', array($this, 'register_routes'));
//    }
//
//    public function register_routes() {
//        register_rest_route('reactminiapp/v1', '/data', array(
//            'methods' => 'GET',
//            'callback' => array($this, 'get_data'),
//            'permission_callback' => array($this, 'check_permission')
//        ));
//    }
//
//    public function check_permission() {
//        return current_user_can('manage_options');
//    }
//
//    public function get_data() {
//        // Example data fetch from external API
//        $external_api_url = 'https://api.example.com/data';
//        $response = wp_remote_get($external_api_url);
//        
//        if (is_wp_error($response)) {
//            return new WP_Error('api_error', 'External API error', array('status' => 500));
//        }
//
//        return rest_ensure_response(json_decode(wp_remote_retrieve_body($response)));
//    }
//}