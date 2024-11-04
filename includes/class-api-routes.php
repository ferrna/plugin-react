<?php

class RMA_API_Routes {
    public function __construct() {
        $this->namespace     = '/reactminiapp/v1';
		$this->resource_name = 'data';
        $this->users_name = 'users';
        add_action('rest_api_init', array($this, 'register_routes'));
    }


    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->resource_name, array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => array($this, 'rma_api_get_data'),
            'permission_callback' => '__return_true', // Abierto a todos
        ));

        // Test get users
        register_rest_route($this->namespace, '/' . $this->users_name, array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => array($this, 'rma_api_get_users'),
            'permission_callback' => '__return_true', // Abierto a todos
        ));
    
        register_rest_route($this->namespace, '/' . $this->resource_name, array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'rma_api_create_data'),
            'permission_callback' => array($this, 'rma_api_permissions')
        ));
    
        register_rest_route($this->namespace, '/' . $this->resource_name . '/(?P<id>\d+)', array(
            'methods'  => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'rma_api_update_data'),
            'permission_callback' => array($this, 'rma_api_permissions')
        ));
    
        register_rest_route($this->namespace, '/' . $this->resource_name . '/(?P<id>\d+)', array(
            'methods'  => WP_REST_Server::DELETABLE,
            'callback' => array($this, 'rma_api_delete_data'),
            'permission_callback' => array($this, 'rma_api_permissions')
        ));
    }

    public function rma_api_permissions() {
        if ( ! current_user_can( 'read' ) ) {
			return new WP_Error( 'rest_forbidden', esc_html__( 'You cannot view the post resource.' ), array( 'status' => $this->authorization_status_code() ) );
		}
		return true;
    }

    public function rma_api_get_data() {
        $external_api_url = 'https://api.restful-api.dev/objects';
        $response = wp_remote_get($external_api_url);
        
        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'External API error', array('status' => 500));
        }
    
        $external_data = json_decode(wp_remote_retrieve_body($response));
        
        $current_user = wp_get_current_user();
        $user_data = array();

        if ($current_user->ID !== 0) {
            $user_data = array(
                'ID' => $current_user->ID,
                'username' => $current_user->user_login,
                'user_email' => $current_user->user_email,
                'user_nicename' => $current_user->user_nicename,
                'meta' => get_user_meta($current_user->ID)
            );
        } else {
            $user_data = array(
                'message' => 'No authenticated user'
            );
        }

        return rest_ensure_response(array(
            'external_data' => $external_data,
            'current_user' => $user_data
        ));

    }

    public function rma_api_get_users() {
        $users = get_users();
        return $users;
    }

    public function rma_api_create_data(WP_REST_Request $request) {
        
        $data = $request->get_json_params(); // Get JSON data from the request body

        $name = isset($data['name']) ? sanitize_text_field($data['name']) : '';
        $email = isset($data['email']) ? sanitize_email($data['email']) : '';
        $username = isset($data['username']) ? sanitize_text_field($data['username']) : '';

        if (empty($name) || empty($email) || empty($username)) {
            return new WP_Error('missing_data', 'Name, email, and username are required.' . $name . "1" . $email . "2" . $username, array('status' => 400));
        }

        //$external_data = array(
        //    'name'     => $name,
        //    'email'    => $email,
        //    'username' => $username,
        //);
        $external_data = array(
            "name" => "Apple MacBook Pro 16",
            "data" => array(
                "year" => 2019,
                "price" => 1849.99,
                "CPU model" => "Intel Core i9",
                "Hard disk size" => "1 TB"
            )
        );
        $external_api_url = 'https://api.restful-api.dev/objects';

        $args = array(
            'body'    => json_encode($external_data),
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'method'  => 'POST',
            'timeout' => 15, // Optional: set a timeout for the request
        );

        $response = wp_remote_post($external_api_url, $args);
        
        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'External API error', array('status' => 500));
        }
        
        $response_body = json_decode(wp_remote_retrieve_body($response), true);
        return $response_body;
    }
    public function rma_api_update_data() {
        return 'update_data';
    }
    public function rma_api_delete_data() {
        return 'delete_data';
    }
}