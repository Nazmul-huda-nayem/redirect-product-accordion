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
            'cart_url' => wc_get_cart_url(),
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
    public function woocpa_load_embedded_checkout() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'woocpa_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        $cart_data = json_decode(stripslashes($_POST['cart_data']), true);
        
        // Sync cart first
        $this->sync_cart_items($cart_data);
        
        // Check if cart has items
        if (WC()->cart->is_empty()) {
            wp_send_json_error('Cart is empty');
            return;
        }
        
        // Define checkout constant
        if (!defined('WOOCOMMERCE_CHECKOUT')) {
            define('WOOCOMMERCE_CHECKOUT', true);
        }
        
        // Initialize WooCommerce frontend
        if (!did_action('woocommerce_init')) {
            WC()->frontend_includes();
        }
        
        // Ensure WooCommerce is loaded
        if (!class_exists('WC_Checkout')) {
            wp_send_json_error('WooCommerce checkout class not available');
            return;
        }
        
        ob_start();
        ?>
        <div class="woocpa-embedded-checkout">
            <div class="woocpa-checkout-header">
                <h2>Payment Details</h2>
                <button type="button" class="woocpa-back-to-step1">← Back to Products</button>
            </div>
            
            <div class="woocommerce">
                <?php
                // Initialize checkout
                $checkout = WC()->checkout();
                
                // Load checkout template manually
                ?>
                <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

                    <?php if ( $checkout->get_checkout_fields() ) : ?>

                        <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                        <div class="col2-set" id="customer_details">
                            <div class="col-1">
                                <?php do_action( 'woocommerce_checkout_billing' ); ?>
                            </div>

                            <div class="col-2">
                                <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                                
                                <!-- Shipping Fields (if needed) -->
                                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                                    <div class="woocommerce-shipping-fields">
                                        <h3><?php esc_html_e( 'Shipping details', 'woocommerce' ); ?></h3>
                                        <?php
                                        $fields = $checkout->get_checkout_fields( 'shipping' );
                                        foreach ( $fields as $key => $field ) {
                                            woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                    <?php endif; ?>
                    
                    <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
                    
                    <h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>
                    
                    <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                    <div id="order_review" class="woocommerce-checkout-review-order">
                        <table class="shop_table woocommerce-checkout-review-order-table">
                            <thead>
                                <tr>
                                    <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                                    <th class="product-total"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                do_action( 'woocommerce_review_order_before_cart_contents' );

                                foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                                    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

                                    if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                        ?>
                                        <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                                            <td class="product-name">
                                                <?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ) . '&nbsp;'; ?>
                                                <?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', $cart_item['quantity'] ) . '</strong>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                                <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                            </td>
                                            <td class="product-total">
                                                <?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }

                                do_action( 'woocommerce_review_order_after_cart_contents' );
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="cart-subtotal">
                                    <th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                                    <td><?php wc_cart_totals_subtotal_html(); ?></td>
                                </tr>

                                <?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
                                    <tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                                        <th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
                                        <td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                                    <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
                                    <?php wc_cart_totals_shipping_html(); ?>
                                    <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
                                <?php endif; ?>

                                <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                                    <tr class="fee">
                                        <th><?php echo esc_html( $fee->name ); ?></th>
                                        <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
                                    <?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
                                        <?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
                                            <tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
                                                <th><?php echo esc_html( $tax->label ); ?></th>
                                                <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr class="tax-total">
                                            <th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
                                            <td><?php wc_cart_totals_taxes_total_html(); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

                                <tr class="order-total">
                                    <th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
                                    <td><?php wc_cart_totals_order_total_html(); ?></td>
                                </tr>

                                <?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
                            </tfoot>
                        </table>
                    </div>

                    <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
                    
                    <!-- Payment Methods -->
                    <div id="payment" class="woocommerce-checkout-payment">
                        <?php if ( WC()->cart->needs_payment() ) : ?>
                            <ul class="wc_payment_methods payment_methods methods">
                                <?php
                                $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
                                if ( ! empty( $available_gateways ) ) {
                                    foreach ( $available_gateways as $gateway ) {
                                        wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ), '', WC()->plugin_path() . '/templates/' );
                                    }
                                } else {
                                    echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
                                }
                                ?>
                            </ul>
                        <?php endif; ?>
                        
                        <div class="form-row place-order">
                            <noscript>
                                <?php esc_html_e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ); ?>
                                <br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
                            </noscript>

                            <?php wc_get_template( 'checkout/terms.php' ); ?>

                            <?php do_action( 'woocommerce_review_order_before_submit' ); ?>

                            <button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="<?php esc_attr_e( 'Place order', 'woocommerce' ); ?>" data-value="<?php esc_attr_e( 'Place order', 'woocommerce' ); ?>"><?php esc_html_e( 'Place order', 'woocommerce' ); ?></button>

                            <?php do_action( 'woocommerce_review_order_after_submit' ); ?>

                            <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        
        <style>
        .woocpa-embedded-checkout {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .woocpa-checkout-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e91e63;
        }
        
        .woocpa-checkout-header h2 {
            margin: 0;
            color: #e91e63;
        }
        
        .woocpa-back-to-step1 {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            color: #666;
            text-decoration: none;
        }
        
        .woocpa-back-to-step1:hover {
            background: #e9ecef;
            color: #333;
        }
        
        /* WooCommerce checkout form styling */
        .woocommerce-checkout {
            margin-top: 20px;
        }
        
        .woocommerce-billing-fields,
        .woocommerce-shipping-fields {
            margin-bottom: 30px;
        }
        
        .woocommerce-billing-fields h3,
        .woocommerce-shipping-fields h3 {
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .form-row {
            margin-bottom: 20px;
        }
        
        .form-row label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-row input,
        .form-row select,
        .form-row textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-row input:focus,
        .form-row select:focus,
        .form-row textarea:focus {
            border-color: #e91e63;
            outline: none;
            box-shadow: 0 0 5px rgba(233, 30, 99, 0.2);
        }
        
        #place_order {
            background: #e91e63 !important;
            color: white !important;
            border: none !important;
            padding: 15px 30px !important;
            font-size: 16px !important;
            font-weight: bold !important;
            cursor: pointer !important;
            border-radius: 5px !important;
            width: 100% !important;
            margin-top: 20px !important;
        }
        
        #place_order:hover {
            background: #c81e56 !important;
        }
        
        #place_order:disabled {
            background: #ccc !important;
            cursor: not-allowed !important;
        }
        
        .woocommerce-checkout-review-order {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .woocommerce-checkout-review-order h3 {
            margin-top: 0;
        }
        
        @media (max-width: 768px) {
            .woocpa-checkout-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .col2-set .col-1,
            .col2-set .col-2 {
                float: none;
                width: 100%;
            }
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            // Prevent default WooCommerce checkout redirect
            $('body').addClass('woocommerce-checkout');
            
            // Override WooCommerce checkout submission to use our AJAX handler
            $(document).off('submit', 'form.checkout');
        });
        </script>
        
        <?php
        $output = ob_get_clean();
        wp_send_json_success($output);
    }
    public function woocpa_process_embedded_checkout() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'woocpa_nonce')) {
            wp_send_json_error('Security check failed');
            return;
        }
        
        try {
            if (!defined('WOOCOMMERCE_CHECKOUT')) {
                define('WOOCOMMERCE_CHECKOUT', true);
            }
            
            // Parse form data
            parse_str($_POST['form_data'], $form_data);
            
            // Validate required fields
            $required_fields = ['billing_first_name', 'billing_last_name', 'billing_email'];
            foreach ($required_fields as $field) {
                if (empty($form_data[$field])) {
                    wp_send_json_error("Please fill in the {$field} field");
                    return;
                }
            }
            
            // Process the order through WooCommerce
            $checkout = WC()->checkout();
            
            // Create order
            $order_id = $checkout->create_order($form_data);
            
            if (is_wp_error($order_id)) {
                wp_send_json_error($order_id->get_error_message());
                return;
            }
            
            $order = wc_get_order($order_id);
            if (!$order) {
                wp_send_json_error('Failed to create order');
                return;
            }
            
            // Update order status
            $order->update_status('processing', 'Order created via custom checkout');
            
            // Empty cart
            WC()->cart->empty_cart();
            
            // Generate thank you content
            ob_start();
            ?>
            <div class="woocpa-thankyou-page">
                <div class="woocpa-thankyou-header">
                    <h2>✓ Thank You for Your Booking!</h2>
                    <p class="success-message">Your order has been successfully processed.</p>
                </div>
                
                <div class="woocpa-order-details">
                    <div class="order-info-card">
                        <h3>Order Information</h3>
                        <div class="order-meta">
                            <div class="meta-item">
                                <span class="label">Order Number:</span>
                                <span class="value">#<?php echo $order->get_order_number(); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Order Date:</span>
                                <span class="value"><?php echo $order->get_date_created()->format('F j, Y'); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Total Amount:</span>
                                <span class="value"><?php echo $order->get_formatted_order_total(); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Payment Method:</span>
                                <span class="value"><?php echo $order->get_payment_method_title(); ?></span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Order Status:</span>
                                <span class="value status-<?php echo $order->get_status(); ?>"><?php echo ucfirst($order->get_status()); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-items-card">
                        <h3>Your Training Packages</h3>
                        <div class="items-list">
                            <?php foreach ($order->get_items() as $item_id => $item): ?>
                                <div class="item-row">
                                    <div class="item-details">
                                        <span class="item-name"><?php echo $item->get_name(); ?></span>
                                        <span class="item-quantity">Quantity: <?php echo $item->get_quantity(); ?></span>
                                    </div>
                                    <div class="item-total">
                                        <?php echo wc_price($item->get_total()); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="customer-details-card">
                        <h3>Billing Information</h3>
                        <div class="customer-info">
                            <p><strong><?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></strong></p>
                            <p><?php echo $order->get_billing_email(); ?></p>
                            <?php if ($order->get_billing_phone()): ?>
                                <p><?php echo $order->get_billing_phone(); ?></p>
                            <?php endif; ?>
                            <p><?php echo $order->get_formatted_billing_address(); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="woocpa-next-steps">
                    <h3>What's Next?</h3>
                    <ul>
                        <li>📧 You will receive a confirmation email shortly at <strong><?php echo $order->get_billing_email(); ?></strong></li>
                        <li>📞 Our team will contact you within 24 hours to schedule your training</li>
                        <li>📚 Training materials will be provided before your session</li>
                        <li>❓ If you have any questions, please contact our support team</li>
                    </ul>
                </div>
                
                <div class="woocpa-actions">
                    <a href="<?php echo home_url(); ?>" class="button secondary">Continue Shopping</a>
                    <a href="<?php echo $order->get_view_order_url(); ?>" class="button primary">View Order Details</a>
                </div>
            </div>
            
            <style>
            .woocpa-thankyou-page {
                max-width: 800px;
                margin: 0 auto;
                padding: 40px 20px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            
            .woocpa-thankyou-header {
                text-align: center;
                margin-bottom: 40px;
            }
            
            .woocpa-thankyou-header h2 {
                color: #28a745;
                font-size: 32px;
                margin-bottom: 10px;
            }
            
            .success-message {
                font-size: 18px;
                color: #666;
                margin: 0;
            }
            
            .woocpa-order-details {
                display: grid;
                gap: 30px;
                margin-bottom: 40px;
            }
            
            .order-info-card,
            .order-items-card,
            .customer-details-card {
                background: #f8f9fa;
                padding: 25px;
                border-radius: 10px;
                border-left: 4px solid #e91e63;
            }
            
            .order-info-card h3,
            .order-items-card h3,
            .customer-details-card h3 {
                margin-top: 0;
                margin-bottom: 20px;
                color: #333;
                font-size: 20px;
            }
            
            .order-meta {
                display: grid;
                gap: 15px;
            }
            
            .meta-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid #eee;
            }
            
            .meta-item:last-child {
                border-bottom: none;
            }
            
            .meta-item .label {
                font-weight: 600;
                color: #555;
            }
            
            .meta-item .value {
                font-weight: bold;
                color: #333;
            }
            
            .status-processing {
                color: #28a745;
            }
            
            .items-list {
                display: grid;
                gap: 15px;
            }
            
            .item-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 15px;
                background: white;
                border-radius: 8px;
                border: 1px solid #eee;
            }
            
            .item-details .item-name {
                display: block;
                font-weight: bold;
                color: #333;
                margin-bottom: 5px;
            }
            
            .item-details .item-quantity {
                display: block;
                font-size: 14px;
                color: #666;
            }
            
            .item-total {
                font-weight: bold;
                color: #e91e63;
                font-size: 18px;
            }
            
            .customer-info p {
                margin: 8px 0;
                line-height: 1.5;
            }
            
            .woocpa-next-steps {
                background: #e8f5e8;
                padding: 25px;
                border-radius: 10px;
                margin-bottom: 30px;
            }
            
            .woocpa-next-steps h3 {
                color: #28a745;
                margin-top: 0;
                margin-bottom: 20px;
            }
            
            .woocpa-next-steps ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            .woocpa-next-steps li {
                padding: 10px 0;
                font-size: 16px;
                line-height: 1.6;
            }
            
            .woocpa-actions {
                text-align: center;
                display: flex;
                gap: 20px;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .woocpa-actions .button {
                padding: 15px 30px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: bold;
                font-size: 16px;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .woocpa-actions .button.primary {
                background: #e91e63;
                color: white;
            }
            
            .woocpa-actions .button.primary:hover {
                background: #c81e56;
                color: white;
            }
            
            .woocpa-actions .button.secondary {
                background: #f8f9fa;
                color: #333;
                border: 2px solid #ddd;
            }
            
            .woocpa-actions .button.secondary:hover {
                background: #e9ecef;
                border-color: #adb5bd;
                color: #333;
            }
            
            @media (max-width: 768px) {
                .woocpa-thankyou-page {
                    padding: 20px 15px;
                }
                
                .woocpa-thankyou-header h2 {
                    font-size: 24px;
                }
                
                .meta-item {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 5px;
                }
                
                .item-row {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 10px;
                }
                
                .woocpa-actions {
                    flex-direction: column;
                }
                
                .woocpa-actions .button {
                    width: 100%;
                }
            }
            </style>
            
            <?php
            $thankyou_content = ob_get_clean();
            
            wp_send_json_success($thankyou_content);
            
        } catch (Exception $e) {
            wp_send_json_error('Order processing failed: ' . $e->getMessage());
        }
    }
    private function sync_cart_items($cart_data) {
        if (!is_array($cart_data)) {
            return;
        }
        
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
                continue;
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
        add_action('wp_ajax_woocpa_load_embedded_checkout', [$this, 'woocpa_load_embedded_checkout']);
        add_action('wp_ajax_nopriv_woocpa_load_embedded_checkout', [$this, 'woocpa_load_embedded_checkout']);
        add_action('wp_ajax_woocpa_process_embedded_checkout', [$this, 'woocpa_process_embedded_checkout']);
        add_action('wp_ajax_nopriv_woocpa_process_embedded_checkout', [$this, 'woocpa_process_embedded_checkout']);
	}
}

// Instantiate Plugin Class
WOOCPAAccordionCreator::instance();
