<?php
namespace WOOCPANamespaceAccordion;

define('WOOCPA_TEST_ASFSK_ASSETS_ADMIN_DIR_FILE', plugin_dir_url(__FILE__) . 'assets/admin');
class WOOCPAAccordionCreator {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function woocpa_admin_editor_scripts() {
		add_filter( 'script_loader_tag', [ $this, 'woocpa_admin_editor_scripts_as_a_module' ], 10, 2 );
	}

	public function woocpa_admin_editor_scripts_as_a_module( $tag, $handle ) {
		if ( 'woocpa_accordion_editor' === $handle ) {
			$tag = str_replace( '<script', '<script type="module"', $tag );
		}

		return $tag;
	}

	private function include_widgets_files() {
		require_once( __DIR__ . '/widgets/woocpa-accordion.php' );
	}

	public function woocpa_register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files(); 

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\WOOCPAAccordionCreatoR() );
	}

	// Register Category
	function woocpa_add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'woocpa-woocommerce-product-accordion-category',
			[
				'title' => esc_html__( 'WooCommerce Product Accordion', 'woocommerce-product-accordion' ),
				'icon' => 'eicon-person',
			]
		);
	}
	public function woocpa_all_assets_for_the_public(){
		wp_enqueue_script( 'woocpa_accordion_main_js', plugin_dir_url( __FILE__ ) . 'assets/public/woocpa-main.js', array('jquery'), '2.5', true );
		wp_enqueue_style( 'woocpa-style', plugin_dir_url( __FILE__ ) . 'assets/public/woocpa-main.css', null, '2.5', 'all');
		wp_enqueue_style( 'woocpa-custom-cart-style', plugin_dir_url( __FILE__ ) . 'assets/public/custom-cart.css', null, '2.5', 'all');

		wp_enqueue_style( 'woocpa-f', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', null, '2.5', 'all');
		wp_enqueue_script( 'woocpa-custom-cart', plugin_dir_url( __FILE__ ) . 'assets/public/custom-cart.js', array('jquery', 'wc-checkout', 'wc-cart-fragments'), '2.5', true );
        wp_localize_script('woocpa-custom-cart', 'woocpa_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('woocpa_nonce'),
            'currency_symbol' => get_woocommerce_currency_symbol(),
            'currency_position' => get_option('woocommerce_currency_pos'),
            'price_format' => get_option('woocommerce_price_format'),
            'thousand_separator' => wc_get_price_thousand_separator(),
            'decimal_separator' => wc_get_price_decimal_separator(),
            'decimals' => wc_get_price_decimals(),
            'checkout_url' => wc_get_checkout_url(),
            'wc_cart_url' => wc_get_cart_url(),
        ));
	}
	public function woocpa_all_assets_for_elementor_editor_admin(){
		$all_css_js_file = array(
            'woocpa_accordion_admin_main_css' => array('woocpa_path_admin_define'=>WOOCPA_TEST_ASFSK_ASSETS_ADMIN_DIR_FILE . '/icon.css'),
        );
        foreach($all_css_js_file as $handle => $fileinfo){
            wp_enqueue_style( $handle, $fileinfo['woocpa_path_admin_define'], null, '2.5', 'all');
        }
	}


    function woocpa_ajax_add_to_cart() {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        $result = WC()->cart->add_to_cart($product_id, $quantity);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => 'Product added to cart',
                'cart_count' => WC()->cart->get_cart_contents_count()
            ));
        } else {
            wp_send_json_error('Failed to add product to cart');
        }
    }
    public function woocpa_sync_cart() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'woocpa_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        $cart_data = json_decode(stripslashes($_POST['cart_data']), true);
        
        if (!is_array($cart_data)) {
            wp_send_json_error('Invalid cart data');
            return;
        }
        
        try {
            // Clear existing cart
            WC()->cart->empty_cart();
            
            // Add items from local cart
            foreach ($cart_data as $item) {
                $product_id = intval($item['product_id']);
                $quantity = intval($item['quantity']);
                $variation_id = isset($item['variation_id']) && $item['variation_id'] ? intval($item['variation_id']) : 0;
                
                // Validate product exists
                $product = wc_get_product($product_id);
                if (!$product) {
                    continue; // Skip invalid products
                }
                
                // Add to WooCommerce cart
                if ($variation_id) {
                    WC()->cart->add_to_cart($product_id, $quantity, $variation_id);
                } else {
                    WC()->cart->add_to_cart($product_id, $quantity);
                }
            }
            
            // Calculate totals
            WC()->cart->calculate_totals();
            
            wp_send_json_success([
                'message' => 'Cart synchronized successfully',
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => WC()->cart->get_cart_total()
            ]);
            
        } catch (Exception $e) {
            wp_send_json_error('Error syncing cart: ' . $e->getMessage());
        }
    }
    
    
    
    
	public function __construct() {
		// For public assets
		add_action('wp_enqueue_scripts', [$this, 'woocpa_all_assets_for_the_public']);

		// For Elementor Editor
		add_action('elementor/editor/before_enqueue_scripts', [$this, 'woocpa_all_assets_for_elementor_editor_admin']);

		// Register Category
		add_action( 'elementor/elements/categories_registered', [ $this, 'woocpa_add_elementor_widget_categories' ] );

		// Register widgets
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'woocpa_register_widgets' ] );

		// Register editor scripts
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'woocpa_admin_editor_scripts' ] );

        // Register AJAX handlers
        add_action('wp_ajax_woocommerce_add_to_cart', [$this, 'woocpa_ajax_add_to_cart']);
        add_action('wp_ajax_nopriv_woocommerce_add_to_cart', [$this, 'woocpa_ajax_add_to_cart']);
        // add_action('wp_ajax_woocpa_load_checkout', [$this, 'woocpa_load_checkout']);
        // add_action('wp_ajax_nopriv_woocpa_load_checkout', [$this, 'woocpa_load_checkout']);
        // add_action('wp_ajax_woocpa_process_checkout', [$this, 'woocpa_process_checkout']);
        // add_action('wp_ajax_nopriv_woocpa_process_checkout', [$this, 'woocpa_process_checkout']);
        add_action('wp_ajax_woocpa_sync_cart', [$this, 'woocpa_sync_cart']);
        add_action('wp_ajax_nopriv_woocpa_sync_cart', [$this, 'woocpa_sync_cart']);
	}
}

// Instantiate Plugin Class
WOOCPAAccordionCreator::instance();
