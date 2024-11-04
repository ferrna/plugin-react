<?php
/**
 * Plugin Name: ReactMiniApp
 * Description: A WordPress plugin with React integration
 * Version: 1.0.0
 * Author: FernandoArriondo
 */

define('RMA_PATH', plugin_dir_path(__FILE__));

// To include API Routes
require_once RMA_PATH . 'includes/class-api-routes.php';

// Exit if accessed directly (security measure)
if (!defined('ABSPATH')) {
    exit;
}

class ReactMiniApp {
    public function __construct() {
        new RMA_API_Routes();
        add_action('wp_enqueue_scripts', array($this, 'enqueue_react_app'));
        add_shortcode('react_mini_app', array($this, 'render_react_app'));
    }

    function enqueue_react_app() {
        wp_enqueue_script(
            'reactminiapp',
            plugin_dir_url(__FILE__) . 'build/index.js',
            array(), // Dependencias (como jQuery).
            '1.0.0',
            true // Cargar el script en el footer.
        );

        wp_localize_script(
            'reactminiapp',
            'rmaData',
            array(
                'nonce' => wp_create_nonce('wp_rest'),
                'apiUrl' => rest_url('reactminiapp/v1/')
            )
        );
    }

    function render_react_app() {
        return '<div id="react-mini-app-root"></div>';
    }
}

// Initialize the plugin
new ReactMiniApp();



//define('RMA_URL', plugin_dir_url(__FILE__));
//
//class ReactMiniApp {
//    public function __construct() {
//        add_action('admin_menu', array($this, 'add_menu_page'));
//        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
//        
//        // Initialize API Routes
//        new RMA_API_Routes();
//    }