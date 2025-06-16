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
		wp_enqueue_script( 'woocpa-custom-cart', plugin_dir_url( __FILE__ ) . 'assets/public/custom-cart.js', array('jquery'), '2.5', true );
        wp_localize_script('woocpa-custom-cart', 'woocpa_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('woocpa_nonce')
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
    function woocpa_load_checkout() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'woocpa_nonce')) {
            wp_send_json_error('Security check failed');
        }
        
        $cart_data = json_decode(stripslashes($_POST['cart_data']), true);
        
        // Clear existing cart and add items
        WC()->cart->empty_cart();
        foreach ($cart_data as $item) {
            WC()->cart->add_to_cart($item['id'], $item['quantity']);
        }
        
        ob_start();
        ?>
        <div class="woocpa-checkout-wrapper">
            <h2>Payment Details</h2>
            
            <form id="woocpa-checkout-form" method="post">
                <div class="checkout-sections">
                    <div class="billing-section">
                        <h3>Billing Information</h3>
                        <div class="form-row">
                            <div class="form-group half">
                                <label for="billing_first_name">First Name</label>
                                <input type="text" name="billing_first_name" id="billing_first_name" required>
                            </div>
                            <div class="form-group half">
                                <label for="billing_last_name">Last Name</label>
                                <input type="text" name="billing_last_name" id="billing_last_name" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="billing_email">Email Address</label>
                            <input type="email" name="billing_email" id="billing_email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="billing_phone" class="optional">Phone Number</label>
                            <input type="tel" name="billing_phone" id="billing_phone">
                        </div>
                        
                        <div class="form-group">
                            <label for="billing_company" class="optional">Company Name</label>
                            <input type="text" name="billing_company" id="billing_company">
                        </div>
                        
                        <div class="form-group">
                            <label for="billing_address_1">Address</label>
                            <input type="text" name="billing_address_1" id="billing_address_1" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group half">
                                <label for="billing_city">City</label>
                                <input type="text" name="billing_city" id="billing_city" required>
                            </div>
                            <div class="form-group half">
                                <label for="billing_postcode">Postal Code</label>
                                <input type="text" name="billing_postcode" id="billing_postcode" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group half">
                                <label for="billing_state" class="optional">State</label>
                                <input type="text" name="billing_state" id="billing_state">
                            </div>
                            <div class="form-group half">
                                <label for="billing_country">Country</label>
                                <select name="billing_country" id="billing_country" required>
                                    <option value="US">United States</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="CA">Canada</option>
                                    <option value="AU">Australia</option>
                                    <!-- Add more countries as needed -->
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-review">
                        <h3>Your Order</h3>
                        <div class="order-summary">
                            <?php
                            $total = 0;
                            foreach ($cart_data as $item):
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                            <div class="order-item">
                                <span class="item-name"><?php echo esc_html($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                                <span class="item-total">$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="order-total">
                                <strong>
                                    <span>Total: </span>
                                    <span>$<?php echo number_format($total, 2); ?></span>
                                </strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-section">
                        <h3>Payment Method</h3>
                        <div class="payment-methods">
                            <label>
                                <input type="radio" name="payment_method" value="cod" checked>
                                Cash on Delivery
                            </label>
                            <?php if (get_option('woocommerce_cheque_settings')): ?>
                            <label>
                                <input type="radio" name="payment_method" value="cheque">
                                Check Payment
                            </label>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="order_comments" class="optional">Order Notes</label>
                        <textarea name="order_comments" id="order_comments" rows="4" placeholder="Notes about your order, e.g. special notes for delivery."></textarea>
                    </div>
                </div>
                
                <button type="submit" class="woocpa-place-order">Place Order</button>
            </form>
        </div>
        
        <style>
        .woocpa-checkout-wrapper {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .checkout-sections {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .billing-section {
            flex: 2;
            min-width: 300px;
        }
        
        .order-review {
            flex: 1;
            min-width: 250px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            height: fit-content;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.half {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group label::after {
            content: " *";
            color: red;
        }
        
        .form-group label.optional::after {
            content: "";
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-total {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-top: 2px solid #e91e63;
            margin-top: 10px;
            font-size: 18px;
        }
        
        .payment-methods label {
            display: block;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .payment-methods input[type="radio"] {
            margin-right: 10px;
            width: auto;
        }
        
        .woocpa-place-order {
            width: 100%;
            background: #e91e63;
            color: white;
            border: none;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
        }
        
        .woocpa-place-order:hover {
            background: #c81e56;
        }
        
        @media (max-width: 768px) {
            .checkout-sections {
                flex-direction: column;
            }
            
            .form-row {
                flex-direction: column;
            }
        }
        </style>
        
        <?php
        $output = ob_get_clean();
        wp_send_json_success($output);
    }
    function woocpa_process_checkout() {
        try {
            if (!defined('WOOCOMMERCE_CHECKOUT')) {
                define('WOOCOMMERCE_CHECKOUT', true);
            }
            
            $cart_data = json_decode(stripslashes($_POST['cart_data']), true);
            parse_str($_POST['form_data'], $form_data);
            
            // Clear and rebuild cart
            WC()->cart->empty_cart();
            foreach ($cart_data as $item) {
                WC()->cart->add_to_cart($item['id'], $item['quantity']);
            }
            
            // Process the order
            $checkout = WC()->checkout();
            $order_id = $checkout->create_order($form_data);
            
            if (is_wp_error($order_id)) {
                wp_send_json_error($order_id->get_error_message());
            }
            
            $order = wc_get_order($order_id);
            $order->update_status('processing');
            
            // Generate thank you content
            ob_start();
            ?>
            <div class="woocpa-thankyou">
                <h2>Thank You for Your Booking!</h2>
                <div class="order-details">
                    <p><strong>Order #:</strong> <?php echo $order_id; ?></p>
                    <p><strong>Total:</strong> <?php echo wc_price($order->get_total()); ?></p>
                    <p><strong>Status:</strong> <?php echo $order->get_status(); ?></p>
                </div>
                <div class="order-items">
                    <h3>Your Training Packages:</h3>
                    <ul>
                        <?php foreach ($order->get_items() as $item): ?>
                            <li><?php echo $item->get_name(); ?> (<?php echo $item->get_quantity(); ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <p>You will receive a confirmation email shortly.</p>
            </div>
            <style>
            .woocpa-thankyou {
                text-align: center;
                padding: 40px 20px;
            }
            .woocpa-thankyou h2 {
                color: #e91e63;
                margin-bottom: 20px;
            }
            .order-details {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 5px;
                margin: 20px 0;
            }
            .order-items ul {
                list-style: none;
                padding: 0;
            }
            .order-items li {
                padding: 5px 0;
                border-bottom: 1px solid #eee;
            }
            </style>
            <?php
            $thankyou_content = ob_get_clean();
            
            wp_send_json_success($thankyou_content);
            
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
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
        add_action('wp_ajax_woocpa_load_checkout', [$this, 'woocpa_load_checkout']);
        add_action('wp_ajax_nopriv_woocpa_load_checkout', [$this, 'woocpa_load_checkout']);
        add_action('wp_ajax_woocpa_process_checkout', [$this, 'woocpa_process_checkout']);
        add_action('wp_ajax_nopriv_woocpa_process_checkout', [$this, 'woocpa_process_checkout']);
	}
}

// Instantiate Plugin Class
WOOCPAAccordionCreator::instance();
