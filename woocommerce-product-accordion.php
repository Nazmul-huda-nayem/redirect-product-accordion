<?php
/**
 * Plugin Name: WooCommerce Product Accordion
 * Description: Ready to transform your WooCommerce store? Explore the possibilities with the WooCommerce Product Accordion and delight your customers with a seamless browsing journey.
 * Plugin URI:  https://bestwpdeveloper.com/woocommerce-product-accordion
 * Version:     2.6
 * Author:      Best WP Developer
 * Author URI:  https://bestwpdeveloper.com/
 * Text Domain: woocommerce-product-accordion
 * Elementor tested up to: 3.19.0
 * Elementor Pro tested up to: 3.19.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once ( plugin_dir_path(__FILE__) ) . '/includes/requires-check.php';

final class WOOCPAAccordion_Creator {

    const VERSION = '2.6';
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
    const MINIMUM_PHP_VERSION = '7.0';

    private static $instance = null;
    private static $textdomain_loaded = false;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Load textdomain immediately to prevent just-in-time loading
        $this->force_load_textdomain();
        
        // Hook into WordPress initialization
        add_action( 'after_setup_theme', array( $this, 'early_init' ), 1 );
        add_action( 'init', array( $this, 'init_plugin' ), 1 );
    }

    /**
     * Force load textdomain early to prevent just-in-time loading
     */
    private function force_load_textdomain() {
        if ( self::$textdomain_loaded ) {
            return;
        }

        // Disable just-in-time loading for our domain
        add_filter( 'override_load_textdomain', array( $this, 'override_textdomain_loading' ), 10, 3 );
        
        // Load our textdomain immediately
        $this->woocpa_loaded_textdomain();
    }

    /**
     * Override textdomain loading to prevent just-in-time loading warnings
     */
    public function override_textdomain_loading( $override, $domain, $mofile ) {
        if ( 'woocommerce-product-accordion' === $domain ) {
            if ( ! self::$textdomain_loaded ) {
                $this->woocpa_loaded_textdomain();
            }
            return true; // Prevent WordPress from loading it again
        }
        return $override;
    }

    /**
     * Early initialization
     */
    public function early_init() {
        // Ensure textdomain is loaded
        $this->woocpa_loaded_textdomain();
    }

    /**
     * Load plugin textdomain for translations
     */
    public function woocpa_loaded_textdomain() {
        if ( self::$textdomain_loaded ) {
            return true;
        }

        $plugin_dir = plugin_dir_path( __FILE__ );
        $languages_dir = $plugin_dir . 'languages';
        
        // Check if languages directory exists
        if ( ! file_exists( $languages_dir ) ) {
            wp_mkdir_p( $languages_dir );
        }

        $loaded = load_plugin_textdomain( 
            'woocommerce-product-accordion', 
            false, 
            basename( $plugin_dir ) . '/languages'
        );

        self::$textdomain_loaded = true;
        return $loaded;
    }

    /**
     * Initialize the plugin
     */
    public function init_plugin() {
        // Ensure textdomain is loaded
        $this->woocpa_loaded_textdomain();
        
        // Check dependencies and initialize
        $this->check_dependencies();
    }

    /**
     * Check plugin dependencies
     */
    private function check_dependencies() {
        // Check for WooCommerce dependency
        if ( ! $this->is_woocommerce_active() ) {
            add_action( 'admin_notices', array( $this, 'woocpa_woocommerce_missing_notice' ) );
            return;
        }

        // Check if Elementor is installed and activated
        if ( ! $this->is_elementor_active() ) {
            add_action( 'admin_notices', array( $this, 'woocpa_admin_notice_missing_main_plugin' ) );
            return;
        }

        // Check for required Elementor version
        if ( defined( 'ELEMENTOR_VERSION' ) && ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
            add_action( 'admin_notices', array( $this, 'woocpa_admin_notice_minimum_elementor_version' ) );
            return;
        }

        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', array( $this, 'woocpa_admin_notice_minimum_php_version' ) );
            return;
        }

        // All checks passed - initialize plugin
        $this->include_plugin_files();
        $this->woocpa_appsero_connect();
    }

    /**
     * Check if WooCommerce is active
     */
    private function is_woocommerce_active() {
        return did_action( 'woocommerce_loaded' ) || class_exists( 'WooCommerce' );
    }

    /**
     * Check if Elementor is active
     */
    private function is_elementor_active() {
        return did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
    }

    /**
     * Include required plugin files
     */
    private function include_plugin_files() {
        $files = array(
            'woocpa-accordion-boots.php'
        );

        foreach ( $files as $file ) {
            $file_path = plugin_dir_path( __FILE__ ) . $file;
            if ( file_exists( $file_path ) ) {
                require_once $file_path;
            }
        }
    }

    /**
     * Initialize Appsero tracking
     */
    public function woocpa_appsero_connect() {
        $autoloader = __DIR__ . '/vendor/autoload.php';
        if ( file_exists( $autoloader ) ) {
            require $autoloader;
        }

        if ( ! function_exists( 'woocpa_appsero_init_' ) ) {
            function woocpa_appsero_init_() {
                if ( ! class_exists( 'Appsero\Client' ) ) {
                    $appsero_client = __DIR__ . '/appsero/src/Client.php';
                    if ( file_exists( $appsero_client ) ) {
                        require_once $appsero_client;
                    } else {
                        return;
                    }
                }

                try {
                    $client = new Appsero\Client( 
                        '2a83d225-5e1c-4962-970d-2b6b33cfbe86', 
                        'WooCommerce Product Accordion', 
                        __FILE__ 
                    );
                    $client->insights()->init();
                } catch ( Exception $e ) {
                    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                        error_log( 'Appsero initialization failed: ' . $e->getMessage() );
                    }
                }
            }
        }
        
        woocpa_appsero_init_();
    }

    /**
     * Admin notice for missing WooCommerce
     */
    public function woocpa_woocommerce_missing_notice() {
        $this->display_admin_notice(
            __( 'WooCommerce Product Accordion', 'woocommerce-product-accordion' ),
            __( 'WooCommerce', 'woocommerce-product-accordion' )
        );
    }

    /**
     * Admin notice for missing Elementor plugin
     */
    public function woocpa_admin_notice_missing_main_plugin() {
        $this->display_admin_notice(
            __( 'WooCommerce Product Accordion', 'woocommerce-product-accordion' ),
            __( 'Elementor', 'woocommerce-product-accordion' )
        );
    }

    /**
     * Display admin notice helper
     */
    private function display_admin_notice( $plugin_name, $required_plugin ) {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Required plugin name */
            __( '"%1$s" requires "%2$s" to be installed and activated.', 'woocommerce-product-accordion' ),
            '<strong>' . esc_html( $plugin_name ) . '</strong>',
            '<strong>' . esc_html( $required_plugin ) . '</strong>'
        );

        printf( 
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', 
            wp_kses_post( $message ) 
        );
    }

    /**
     * Admin notice for minimum Elementor version
     */
    public function woocpa_admin_notice_minimum_elementor_version() {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Required plugin name 3: Required version */
            __( '"%1$s" requires "%2$s" version %3$s or greater.', 'woocommerce-product-accordion' ),
            '<strong>' . __( 'WooCommerce Product Accordion', 'woocommerce-product-accordion' ) . '</strong>',
            '<strong>' . __( 'Elementor', 'woocommerce-product-accordion' ) . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );

        printf( 
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', 
            wp_kses_post( $message ) 
        );
    }

    /**
     * Admin notice for minimum PHP version
     */
    public function woocpa_admin_notice_minimum_php_version() {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Required technology 3: Required version */
            __( '"%1$s" requires "%2$s" version %3$s or greater.', 'woocommerce-product-accordion' ),
            '<strong>' . __( 'WooCommerce Product Accordion', 'woocommerce-product-accordion' ) . '</strong>',
            '<strong>' . __( 'PHP', 'woocommerce-product-accordion' ) . '</strong>',
            self::MINIMUM_PHP_VERSION
        );

        printf( 
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', 
            wp_kses_post( $message ) 
        );
    }
}

// Initialize the plugin
WOOCPAAccordion_Creator::get_instance();

// Remove problematic shutdown action
remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );