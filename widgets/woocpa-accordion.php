<?php
namespace WOOCPANamespaceAccordion\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WOOCPAAccordionCreatoR extends Widget_Base {

	public function get_name() {
		return esc_html__('WooCommerceProductAccordion', 'woocommerce-product-accordion' );
	}

	public function get_title() {
		return esc_html__( 'WooCommerce Product Accordion', 'woocommerce-product-accordion' );
	}

	public function get_icon() {
		return 'woocpa-accordion-icon eicon-accordion';
	}

	public function get_categories() {
		return [ 'woocpa-woocommerce-product-accordion-category' ];
	}
	protected function woocpa_get_selected_products_options() {
		$args = array(
			'limit'  => -1,
			'status' => 'publish',
		);	
		$products = wc_get_products($args);	
		$options = array();	
		foreach ($products as $product) {
			$options[$product->get_id()] = $product->get_name();
		}	
		return $options;
	}	
	protected function woocpa_get_product_categories_options() {
		$categories = get_terms('product_cat', array('hide_empty' => false));
		$options = array();
		foreach ($categories as $category) {
			$options[$category->term_id] = $category->name;
		}
		return $options;
	}
	protected function woocpa_get_product_tags_options() {
		$tags = get_terms('product_tag', array('hide_empty' => false));
		$options = array();
		foreach ($tags as $tag) {
			$options[$tag->term_id] = $tag->name;
		}
		return $options;
	}
	protected function woocpa_get_product_types_options() {
		$product_types = get_terms('product_type', array('hide_empty' => false));
		$options = array();
		foreach ($product_types as $product_type) {
			$options[$product_type->slug] = $product_type->name;
		}
		return $options;
	}

	// Function to get all standard post statuses dynamically
	protected function woocpa_get_product_statuses_options() {
		$statuses = get_post_statuses();
		$options = array();
		foreach ($statuses as $status => $label) {
			$options[$status] = $label;
		}
		return $options;
	}

	protected function woocpa_get_product_orderby_options() {
		return apply_filters('woocpa/product-grid/orderby-options', [
			'ID'         => esc_html__('Product ID', 'woocommerce-product-accordion'),
			'title'      => esc_html__('Product Title', 'woocommerce-product-accordion'),
			'_price'     => esc_html__('Price', 'woocommerce-product-accordion'),
			'_sku'       => esc_html__('SKU', 'woocommerce-product-accordion'),
			'date'       => esc_html__('Date', 'woocommerce-product-accordion'),
			'modified'   => esc_html__('Last Modified Date', 'woocommerce-product-accordion'),
			'parent'     => esc_html__('Parent ID', 'woocommerce-product-accordion'),
			'rand'       => esc_html__('Random', 'woocommerce-product-accordion'),
			'menu_order' => esc_html__('Menu Order', 'woocommerce-product-accordion'),
			'comment_count' => esc_html__('Comment Count', 'woocommerce-product-accordion'),
		]);
	}
	protected function woocpa_get_product_filter_options() {
		return apply_filters('woocpa/product-grid/filter-options', [
			'recentlyadded'  => esc_html__('Latest Products', 'woocommerce-product-accordion'),
			'featured'       => esc_html__('Featured Products', 'woocommerce-product-accordion'),
			'onsale'         => esc_html__('On Sale Products', 'woocommerce-product-accordion'),
			'instock'        => esc_html__('In Stock Products', 'woocommerce-product-accordion'),
			'outofstock'     => esc_html__('Out of Stock Products', 'woocommerce-product-accordion'),
			'toprated'       => esc_html__('Top Rated Products', 'woocommerce-product-accordion'),
			'bestselling'    => esc_html__('Best Selling Products', 'woocommerce-product-accordion'),
			'popularity'    => esc_html__('Popular Products', 'woocommerce-product-accordion'),
			'recommended'    => esc_html__('Recommended Products', 'woocommerce-product-accordion'),
			'cheapest'       => esc_html__('Cheapest Products', 'woocommerce-product-accordion'),
			'lowstock'       => esc_html__('Low Stock Products', 'woocommerce-product-accordion'),
			'expensive'      => esc_html__('Most Expensive Products', 'woocommerce-product-accordion'),
			'manual'     	 => esc_html__('Manual Selection', 'woocommerce-product-accordion'),
		]);
	}
	protected function woocpa_render_cart_table() {
		if (WC()->cart->is_empty()) {
			echo '<p class="woocpa-empty-cart">' . esc_html__('Your cart is empty', 'woocommerce-product-accordion') . '</p>';
		} else {
			echo '<table class="woocpa-cart-table">';
			echo '<thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr></thead>';
			echo '<tbody>';
			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
				$product = $cart_item['data'];
				echo '<tr>';
				echo '<td>' . esc_html($product->get_name()) . '</td>';
				echo '<td>' . wc_price($product->get_price()) . '</td>';
				echo '<td>' . esc_html($cart_item['quantity']) . '</td>';
				echo '<td>' . wc_price($cart_item['line_total']) . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
			echo '<button class="woocpa-go-to-checkout">' . esc_html__('Proceed With This Booking', 'woocommerce-product-accordion') . '</button>';
		}
	}
	
	protected function register_controls() {
		$this->start_controls_section(
			'woocpa_products_content_section',
			[
				'label' => esc_html__( 'Layout', 'woocommerce-product-accordion' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
        $this->add_control(
			'woocpa_products_zone_style',
			[
				'label' => esc_html__( 'Choose Style', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => esc_html__( 'Style 1', 'woocommerce-product-accordion' ),
					'2' => esc_html__( 'Style 2', 'woocommerce-product-accordion' ),
					'3' => esc_html__( 'Style 3', 'woocommerce-product-accordion' ),
					'4' => esc_html__( 'Style 4', 'woocommerce-product-accordion' ),
					'5' => esc_html__( 'Style 5', 'woocommerce-product-accordion' ),
					'6' => esc_html__( 'Style 6', 'woocommerce-product-accordion' ),
					'7' => esc_html__( 'Style 7', 'woocommerce-product-accordion' ),
					'8' => esc_html__( 'Style 8', 'woocommerce-product-accordion' ),
					'9' => esc_html__( 'Style 9', 'woocommerce-product-accordion' ),
					'10' => esc_html__( 'Style 10', 'woocommerce-product-accordion' ),
					'11' => esc_html__( 'Style 11', 'woocommerce-product-accordion' ),
					'12' => esc_html__( 'Style 12', 'woocommerce-product-accordion' ),
					'13' => esc_html__( 'Style 13', 'woocommerce-product-accordion' ),
					'14' => esc_html__( 'Style 14', 'woocommerce-product-accordion' ),
					'15' => esc_html__( 'Style 15', 'woocommerce-product-accordion' ),
					'16' => esc_html__( 'Style 16', 'woocommerce-product-accordion' ),
					'17' => esc_html__( 'Style 17', 'woocommerce-product-accordion' ),
					'18' => esc_html__( 'Style 18', 'woocommerce-product-accordion' ),
					'19' => esc_html__( 'Style 19', 'woocommerce-product-accordion' ),
					'20' => esc_html__( 'Style 20', 'woocommerce-product-accordion' ),
					'21' => esc_html__( 'Style 21', 'woocommerce-product-accordion' ),
					'22' => esc_html__( 'Style 22', 'woocommerce-product-accordion' ),
					'23' => esc_html__( 'Style 23', 'woocommerce-product-accordion' ),
					'24' => esc_html__( 'Style 24', 'woocommerce-product-accordion' ),
					'25' => esc_html__( 'Style 25', 'woocommerce-product-accordion' ),
					'26' => esc_html__( 'Style 26', 'woocommerce-product-accordion' ),
					'27' => esc_html__( 'Style 27', 'woocommerce-product-accordion' ),
					'28' => esc_html__( 'Style 28', 'woocommerce-product-accordion' ),
					'29' => esc_html__( 'Style 29', 'woocommerce-product-accordion' ),
					'30' => esc_html__( 'Style 30', 'woocommerce-product-accordion' ),
					'31' => esc_html__( 'Style 31', 'woocommerce-product-accordion' ),
				],
			]
		);
		$this->end_controls_section(); 
		
		$this->start_controls_section(
			'woocpa_products_settings_section',
			[
				'label' => esc_html__( 'Product Settings', 'woocommerce-product-accordion' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'woocpa_icon',
			[
				'label' => esc_html__( 'Icon', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'woocommerce-product-accordion' ),
				'label_off' => esc_html__( 'Hide', 'woocommerce-product-accordion' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'woocpa_accordion_collapsed_icon',
			[
				'label' => esc_html__( 'Collapsed Icon', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-down',
					'library' => 'solid',
				],
				'condition' => [
					'woocpa_icon' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_accordion_expand_icon',
			[
				'label' => esc_html__( 'Expand Icon', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-up',
					'library' => 'solid',
				],
				'condition' => [
					'woocpa_icon' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_products_description_heading',
			[
				'label' => esc_html__( 'Description', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'woocpa_description',
			[
				'label' => esc_html__( 'Short Description', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'woocommerce-product-accordion' ),
				'label_off' => esc_html__( 'Hide', 'woocommerce-product-accordion' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_responsive_control(
			'woocpa_description_words',
			[
				'label' => esc_html__( 'Word Length', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 15,
				'condition' => [
					'woocpa_description' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_word_trim_indi',
			[
				'label' => esc_html__( 'Word Trim Indicator', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '......', 'woocommerce-product-accordion' ),
				'label_block' => true,
				'condition' => [
					'woocpa_description' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_price',
			[
				'label' => esc_html__( 'Price', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'woocommerce-product-accordion' ),
				'label_off' => esc_html__( 'Hide', 'woocommerce-product-accordion' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'woocpa_date',
			[
				'label' => esc_html__( 'Publish Date', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'woocommerce-product-accordion' ),
				'label_off' => esc_html__( 'Hide', 'woocommerce-product-accordion' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'woocpa_review',
			[
				'label' => esc_html__( 'Rating', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'woocommerce-product-accordion' ),
				'label_off' => esc_html__( 'Hide', 'woocommerce-product-accordion' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'woocpa_review_count',
			[
				'label' => esc_html__( 'Review Count', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'woocommerce-product-accordion' ),
				'label_off' => esc_html__( 'Hide', 'woocommerce-product-accordion' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'woocpa_review' => 'yes',
				]
			]
		);

		$this->add_control(
			'woocpa_sale',
			[
				'label' => esc_html__( 'Total Sale', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'woocommerce-product-accordion' ),
				'label_off' => esc_html__( 'Hide', 'woocommerce-product-accordion' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'woocpa_sale_label',
			[
				'label' => esc_html__('Label', 'woocommerce-product-accordion'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('Total Sale:-', 'woocommerce-product-accordion'),
				'condition' => [
					'woocpa_sale' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);



		$this->add_control(
			'woocpa_cat_show',
			[
				'label' => esc_html__( 'Category', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'woocommerce-product-accordion' ),
				'label_off' => esc_html__( 'Hide', 'woocommerce-product-accordion' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'woocpa_type',
			[
				'label' => esc_html__( 'Product Type', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'woocommerce-product-accordion' ),
				'label_off' => esc_html__( 'Hide', 'woocommerce-product-accordion' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'woocpa_type_label',
			[
				'label' => esc_html__('Label', 'woocommerce-product-accordion'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('Type:-', 'woocommerce-product-accordion'),
				'condition' => [
					'woocpa_type' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->end_controls_section(); 
		
		$this->start_controls_section(
			'woocpa_products_actions_section',
			[
				'label' => esc_html__( 'Product Action', 'creative-products-list' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'woocpa_cart_btn',
			[
				'label' => esc_html__( 'Cart Button', 'creative-products-list' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'creative-products-list' ),
				'label_off' => esc_html__( 'Hide', 'creative-products-list' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'woocpa_the_cart_type',
			[
				'label' => esc_html__( 'Button Type', 'creative-products-list' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cart',
				'options' => [
					'cart' => esc_html__( 'Cart Button', 'creative-products-list' ),
					'icon' => esc_html__( 'Icon', 'creative-products-list' ),
					'buy' => esc_html__( 'Buy Now', 'creative-products-list' ),
				],
				'condition' => [
					'woocpa_cart_btn' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_cart_button',
			[
				'label' => esc_html__('Cart Button', 'creative-products-list'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('Add To Cart', 'creative-products-list'),
				'condition' => [
					'woocpa_cart_btn' => 'yes',
					'woocpa_the_cart_type' => 'cart',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'woocpa_cart_button_icon',
			[
				'label' => esc_html__( 'Icon', 'creative-products-list' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-shopping-cart',
					'library' => 'solid',
				],
				'condition' => [
					'woocpa_cart_btn' => 'yes',
					'woocpa_the_cart_type' => 'icon',
				],
			]
		);
		$this->add_control(
			'woocpa_details_btn_text',
			[
				'label' => esc_html__('Details Button', 'creative-products-list'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('Buy Now', 'creative-products-list'),
				'condition' => [
					'woocpa_cart_btn' => 'yes',
					'woocpa_the_cart_type' => 'buy',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->end_controls_section(); 

		$this->start_controls_section(
			'woocpa_products_sale_stock_badge_section',
			[
				'label' => esc_html__( 'Sale / Stock Badge', 'creative-products' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'woocpa_badge_style',
			[
				'label' => esc_html__( 'Select Style', 'creative-products' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style1',
				'options' => [
					'style1' => esc_html__( 'Style1', 'creative-products' ),
					'style2' => esc_html__( 'Style2', 'creative-products' ),
					'style3' => esc_html__( 'Style3', 'creative-products' ),
					'style4' => esc_html__( 'Style4', 'creative-products' ),
					'style5' => esc_html__( 'Style5', 'creative-products' ),
					'style6' => esc_html__( 'Style6', 'creative-products' ),
					'style7' => esc_html__( 'Style7', 'creative-products' ),
					'style8' => esc_html__( 'Style8', 'creative-products' ),
				],
				'prefix_class' => 'woocpa-badge-',
			]
		);
		$this->add_control(
			'woocpa_show_sale_badge',
			[
				'label' => esc_html__( 'Show Sale Badge', 'creative-products' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'creative-products' ),
				'label_off' => esc_html__( 'Hide', 'creative-products' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'woocpa_sale_badge_type',
			[
				'label' => esc_html__( 'Select Sale Badge', 'creative-products' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'percent',
				'options' => [
					'percent' => esc_html__( 'Percentage', 'creative-products' ),
					'text' => esc_html__( 'Sale Text', 'creative-products' )
				],
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
				]
			]
		);
		$this->add_control(
			'woocpa_sale_badge_before_percent_text',
			[
				'label' => esc_html__('Before Percentage Text', 'creative-products'),
				'type' => Controls_manager::TEXT,
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
					'woocpa_sale_badge_type' => 'percent',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'woocpa_sale_badge_after_percent_text',
			[
				'label' => esc_html__('After Percentage Text', 'creative-products'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('Off', 'creative-products'),
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
					'woocpa_sale_badge_type' => 'percent',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'woocpa_sale_badge_text',
			[
				'label' => esc_html__('Sale Text', 'creative-products'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('Sale', 'creative-products'),
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
					'woocpa_sale_badge_type' => 'text',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'woocpa_show_stock_out_badge',
			[
				'label' => esc_html__( 'Stock Badge', 'creative-products' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'creative-products' ),
				'label_off' => esc_html__( 'Hide', 'creative-products' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'woocpa_show_badge_stock_out_text',
			[
				'label' => esc_html__('Stock Out Text', 'creative-products'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('Stock Out', 'creative-products'),
				'condition' => [
					'woocpa_show_stock_out_badge' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'woocpa_show_badge_stock_in_text',
			[
				'label' => esc_html__('In Stock Text', 'creative-products'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('In Stock', 'creative-products'),
				'description' => esc_html__('This field will be used if the product does not have a stock count number.', 'creative-products'),
				'condition' => [
					'woocpa_show_stock_out_badge' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'woocpa_show_badge_stock_in_number_text',
			[
				'label' => esc_html__('In Stock Text', 'creative-products'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('In Stock', 'creative-products'),
				'description' => esc_html__('This field will be used only if the product has stock count number.', 'creative-products'),
				'condition' => [
					'woocpa_show_stock_out_badge' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'woocpa_show_featured_badge',
			[
				'label' => esc_html__( 'Featured Badge', 'creative-products' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'creative-products' ),
				'label_off' => esc_html__( 'Hide', 'creative-products' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'woocpa_show_badge_featured_text',
			[
				'label' => esc_html__('Featured Text', 'creative-products'),
				'type' => Controls_manager::TEXT,
				'default' => esc_html__('Hot', 'creative-products'),
				'condition' => [
					'woocpa_show_featured_badge' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'woocpa_badge_one_position',
			[
				'label' => esc_html__( 'Badge Left/Right Position', 'creative-products' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'creative-products' ),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'creative-products' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'woocpa_badge_order',
			[
				'label' => esc_html__( 'Badge Order', 'creative-products' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'row',
				'options' => [
					'column' => esc_html__( 'Column', 'creative-products' ),
					'column-reverse' => esc_html__( 'Column Reverse', 'creative-products' ),
					'row' => esc_html__( 'Row', 'creative-products' ),
					'row-reverse' => esc_html__( 'Row Reverse', 'creative-products' ),
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-sale-featured-badge' => 'flex-direction: {{VALUE}};',
				],
				'condition' => [
					'woocpa_show_featured_badge' => 'yes',
					'woocpa_show_sale_badge' => 'yes',
				],
			]
		);
		
		$this->end_controls_section(); 
		
		$this->start_controls_section(
			'woocpa_products_query_section',
			[
				'label' => esc_html__( 'Product Query', 'woocommerce-product-accordion' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'woocpa_product_filter_by',
			[
				'label'    => esc_html__( 'Products Filter', 'woocommerce-product-accordion' ),
				'type'     => \Elementor\Controls_Manager::SELECT,
				'default'  => 'recentlyadded',
				'options'  => $this->woocpa_get_product_filter_options(),
			]
		);

		$this->add_control(
			'woocpa_product_order_by',
			[
				'label'    => esc_html__( 'Order By', 'woocommerce-product-accordion' ),
				'type'     => \Elementor\Controls_Manager::SELECT,
				'default'  => 'date',
				'options'  => $this->woocpa_get_product_orderby_options(),
			]
		);
		$this->add_control(
			'woocpa_product_order',
			[
				'label'   => esc_html__( 'Order', 'woocommerce-product-accordion' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC'  => esc_html__( 'Ascending', 'woocommerce-product-accordion' ),
					'DESC' => esc_html__( 'Descending', 'woocommerce-product-accordion' ),
				],
			]
		);
		$this->add_control(
			'woocpa_the_product_per_page',
			[
				'label' 	  => esc_html__( 'Product Per Page', 'woocommerce-product-accordion' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 6,
				'min'         => 1,
				'placeholder' => esc_html__( '00', 'woocommerce-product-accordion' ),
			]
		);
		$this->add_control(
			'woocpa_product_grid_products_status',
			[
				'label'    => esc_html__( 'Product Status', 'woocommerce-product-accordion' ),
				'type'     => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'options'  => $this->woocpa_get_product_statuses_options(),
			]
		);




		$this->add_control(
			'woocpa_selected_category',
			[
				'label'   => esc_html__( 'Select Category', 'woocommerce-product-accordion' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->woocpa_get_product_categories_options(array( 'product_cat' )),
			]
		);
		$this->add_control(
			'woocpa_selected_tags',
			[
				'label'   => esc_html__( 'Select Tags', 'woocommerce-product-accordion' ),
				'type'    => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->woocpa_get_product_tags_options(),
			]
		);

		$this->add_control(
			'woocpa_selected_include_products',
			[
				'label'       => esc_html__( 'Include Products', 'woocommerce-product-accordion' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->woocpa_get_selected_products_options(),
				'condition'   => [
					'woocpa_product_filter_by' => 'manual',
				],
			]
		);
		$this->add_control(
			'woocpa_selected_exclude_products',
			[
				'label'       => esc_html__( 'Exclude Products', 'woocommerce-product-accordion' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => $this->woocpa_get_selected_products_options(),
				'condition'   => [
					'woocpa_product_filter_by!' => 'manual',
				]
			]
		);
		$this->add_control(
			'woocpa_the_all_date',
			[
				'label' => esc_html__( 'Date', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'All', 'woocommerce-product-accordion' ),
					'd' => esc_html__( 'Today', 'woocommerce-product-accordion' ),
					'm' => esc_html__( 'Current Month', 'woocommerce-product-accordion' ),
					'coustom' => esc_html__( 'Custom', 'woocommerce-product-accordion' ),
				],
			]
		);
		$this->add_control(
			'woocpa_blog_coustom_year',
			[
				'label' => esc_html__( 'Type 4 Digit Year Only', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '2022', 'woocommerce-product-accordion' ),
				'placeholder' => esc_html__( 'Type Your Year Only', 'woocommerce-product-accordion' ),
				'label_block' => true,
				'condition' => [
					'woocpa_the_all_date' => 'coustom',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_coustom_month',
			[
				'label' => esc_html__( 'Select Month', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SELECT,
				'multiple' => true,
				'default' => '0',
				'options' => [
					'0' => esc_html__( 'All', 'woocommerce-product-accordion' ),
					'1' => esc_html__( 'Jan', 'woocommerce-product-accordion' ),
					'2' => esc_html__( 'Feb', 'woocommerce-product-accordion' ),
					'3' => esc_html__( 'Mar', 'woocommerce-product-accordion' ),
					'4' => esc_html__( 'Apr', 'woocommerce-product-accordion' ),
					'5' => esc_html__( 'May', 'woocommerce-product-accordion' ),
					'6' => esc_html__( 'Jun', 'woocommerce-product-accordion' ),
					'7' => esc_html__( 'Jul', 'woocommerce-product-accordion' ),
					'8' => esc_html__( 'Aug', 'woocommerce-product-accordion' ),
					'9' => esc_html__( 'Sep', 'woocommerce-product-accordion' ),
					'10' => esc_html__( 'Oct', 'woocommerce-product-accordion' ),
					'11' => esc_html__( 'Nov', 'woocommerce-product-accordion' ),
					'12' => esc_html__( 'Dec', 'woocommerce-product-accordion' ),
				],
				'condition' => [
					'woocpa_the_all_date' => 'coustom',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_coustom_day',
			[
				'label' => esc_html__( 'Day Only (Date)', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::NUMBER,
				'separator' => 'after',
				'default' => esc_html__( '00', 'woocommerce-product-accordion' ),
				'placeholder' => esc_html__( '00', 'woocommerce-product-accordion' ),
				'condition' => [
					'woocpa_the_all_date' => 'coustom',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * 
		 * @ Start style controls from here
		 * 
		 */

		$this->start_controls_section(
			'woocpa_blog_title_style_section',
			[
				'label' => esc_html__( 'Title', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'woocpa_title_alignment',
			[
				'label' => esc_html__( 'Title Alignment', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'separator' => 'after',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'woocommerce-product-accordion' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'woocommerce-product-accordion' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'woocommerce-product-accordion' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-collapsed' => 'text-align: {{VALUE}};',
				],
				'toggle' => true,
			]
		);
		// Hover control start for title
		$this->start_controls_tabs(
			'woocpa_blog_title_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_title_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_title_typography',
				'selector' => '{{WRAPPER}} .woocpa-panel-title .woocpa-collapsed',
			]
		);
		$this->add_control(
			'woocpa_blog_title_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-panel-title .woocpa-collapsed' => 'color: {{VALUE}} !important',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_title_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-panel-title .woocpa-collapsed' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_title_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_title_hover_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-panel-title .woocpa-collapsed',
			]
		);
		$this->add_control(
			'woocpa_blog_title_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-panel-title .woocpa-collapsed' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_title_hover_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-panel-title .woocpa-collapsed' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		$this->add_responsive_control(
            'woocpa_blog_title_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-panel-title .woocpa-collapsed' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_title_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-panel-title' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_title_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-panel-title' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_icon_t_style_section',
			[
				'label' => esc_html__( 'Icon', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'woocpa_icon' => 'yes',
				],
			]
		);
		// Hover control start for icon_t
		$this->start_controls_tabs(
			'woocpa_blog_icon_t_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_icon_t_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_responsive_control(
			'woocpa_the_title_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-panel-title .woocpa-colexp-icon' => 'font-size: {{SIZE}}px',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_icon_t_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-panel-title .woocpa-colexp-icon' => 'color: {{VALUE}} !important',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_icon_t_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-panel-title .woocpa-collapsed' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_icon_t_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_responsive_control(
			'woocpa_the_title_icon_hover_size',
			[
				'label' => esc_html__( 'Icon Size', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-panel-title .woocpa-colexp-icon' => 'font-size: {{SIZE}}px',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_icon_t_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-panel-title .woocpa-colexp-icon' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_icon_t_hover_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-panel-title .woocpa-collapsed' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'woocpa_pprroo_icon_border',
				'label' => esc_html__( 'Border', 'woocommerce-product-accordion' ),
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .woocpa-collapsed',
			]
		);
		$this->add_responsive_control(
            'woocpa_blog_icon_t_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-collapsed' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_icon_t_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-collapsed' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_content_style_section',
			[
				'label' => esc_html__( 'Content Style', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
	
		// Hover control start for box
		$this->start_controls_tabs(
			'woocpa_blog_box_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_box_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'woocpa_blog_box_bg_grediant_color',
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .woocpa-prodCont',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'woocpa_blog_box_boxshadow',
				'label' => esc_html__( 'Box Shadow', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-prodCont',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'woocpa_blog_box_border',
				'label' => esc_html__( 'Border', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-prodCont',
			]
		);
		$this->add_responsive_control(
            'woocpa_blog_the_box_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-prodCont' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_the_box_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-prodCont' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_box_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-prodCont' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_box_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'woocpa_blog_box_bg_grediant_hover_color',
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .woocpa-prodCont:hover',
			]
		);
		
		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'woocpa_blog_box_boxshadow_hover',
				'label' => esc_html__( 'Box Shadow', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-prodCont:hover',
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'woocpa_blog_box_border_hover',
				'label' => esc_html__( 'Border', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-prodCont:hover',
			]
		);
		$this->add_responsive_control(
            'woocpa_blog_the_box_hover_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-prodCont:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_description_style_section',
			[
				'label' => esc_html__( 'Description', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'woocpa_description' => 'yes',
				],
			]
		);
		// Hover control start for description
		$this->start_controls_tabs(
			'woocpa_blog_description_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_description_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_description_typography',
				'selector' => '{{WRAPPER}} .woocpa-prodDesc',
			]
		);
		$this->add_control(
			'woocpa_blog_description_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-prodDesc' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_description_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-prodDesc' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_description_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_description_hover_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodDesc',
			]
		);
		$this->add_control(
			'woocpa_blog_description_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodDesc' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_description_hover_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodDesc' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		$this->add_responsive_control(
            'woocpa_blog_desc_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-prodDesc' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_desc_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-prodDesc' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_button_style_section',
			[
				'label' => esc_html__( 'Action Style', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'woocpa_cart_btn' => 'yes',
				],
			]
		);
		// Hover control start for description
		$this->start_controls_tabs(
			'woocpa_blog_button_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_button_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_button_typography',
				'selector' => '{{WRAPPER}} .woocpa-cartBtn',
			]
		);
		$this->add_control(
			'woocpa_blog_button_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-cartBtn' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_button_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-cartBtn' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'woocpa_blog_button_border',
				'label' => esc_html__( 'Border', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-cartBtn',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_button_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_button_hover_typography',
				'selector' => '{{WRAPPER}} .woocpa-cartBtn:hover',
			]
		);
		$this->add_control(
			'woocpa_blog_button_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-cartBtn:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_button_bg_hover_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-cartBtn:hover' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'woocpa_blog_button_border_hover',
				'label' => esc_html__( 'Border', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-cartBtn:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End

		$this->add_responsive_control(
            'woocpa_blog_button_bg_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-cartBtn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_button_icon_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-cartBtn' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_button_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-cartBtn' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		// Product Badge

		$this->start_controls_section(
			'woocpa_product_badge_style',
			[
				'label' => esc_html__( 'Sale / Stock Badge Style', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		// Sale Badge
		$this->add_control(
			'woocpa_sale_badge',
			[
				'label' => esc_html__( 'Sale Badge', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_sale_badge_text_color',
			[
				'label' => esc_html__( 'Text Color', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-sale-badge' => 'color: {{VALUE}}',
				],
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_sale_badge_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-sale-badge' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_sale_badge_typography',
				'selector' => '{{WRAPPER}} .woocpa-sale-badge',
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'woocpa_sale_badge_box_size',
			[
				'label' => esc_html__( 'Box Size', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-sale-badge' => 'inline-size: {{SIZE}}{{UNIT}}; block-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
            'woocpa_sale_badge_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-sale-badge' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
				'condition' => [
					'woocpa_show_sale_badge' => 'yes',
				],
            ]
        );

		// Stock Badge
		$this->add_control(
			'woocpa_stock_badge',
			[
				'label' => esc_html__( 'Stock Badge', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'woocpa_show_stock_out_badge' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_stock_badge_text_color',
			[
				'label' => esc_html__( 'Text Color', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-stock-badge' => 'color: {{VALUE}}',
				],
				'condition' => [
					'woocpa_show_stock_out_badge' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_stock_badge_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-stock-badge' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'woocpa_show_stock_out_badge' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_stock_badge_typography',
				'selector' => '{{WRAPPER}} .woocpa-stock-badge',
				'condition' => [
					'woocpa_show_stock_out_badge' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_stock_badge_box_size',
			[
				'label' => esc_html__( 'Box Size', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-stock-badge' => 'inline-size: {{SIZE}}{{UNIT}}; block-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'woocpa_show_stock_out_badge' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'woocpa_stock_badge_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-stock-badge' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
				'condition' => [
					'woocpa_show_stock_out_badge' => 'yes',
				],
            ]
        );

		// Featured Badge
		$this->add_control(
			'woocpa_featured_badge',
			[
				'label' => esc_html__( 'Featured Badge', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'woocpa_show_featured_badge' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_featured_badge_text_color',
			[
				'label' => esc_html__( 'Text Color', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-featured-badge' => 'color: {{VALUE}}',
				],
				'condition' => [
					'woocpa_show_featured_badge' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_featured_badge_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-featured-badge' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'woocpa_show_featured_badge' => 'yes',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_featured_badge_typography',
				'selector' => '{{WRAPPER}} .woocpa-featured-badge',
				'condition' => [
					'woocpa_show_featured_badge' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_featured_badge_box_size',
			[
				'label' => esc_html__( 'Box Size', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-featured-badge' => 'inline-size: {{SIZE}}{{UNIT}}; block-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'woocpa_show_featured_badge' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'woocpa_featured_badge_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-featured-badge' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
				'condition' => [
					'woocpa_show_featured_badge' => 'yes',
				],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_image_style_section',
			[
				'label' => esc_html__( 'Image Style', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'woocpa_the_post_image_width_size',
			[
				'label' => esc_html__( 'Thumbnail Size', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-productImg img' => 'inline-size: {{SIZE}}px',
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-productImg img' => 'block-size: {{SIZE}}px',
				],
			]
		);
		$this->add_responsive_control(
            'woocpa_blog_image_border_radius',
            [
                'label' => esc_html__('Image Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-productImg img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_caategory_style_section',
			[
				'label' => esc_html__( 'Category', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		// Hover control start for description
		$this->start_controls_tabs(
			'woocpa_blog_caategory_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_caategory_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_caategory_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodCat',
			]
		);
		$this->add_control(
			'woocpa_blog_caategory_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodCat' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_caategory_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodCat' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_caategory_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_caategory_hover_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodCat',
			]
		);
		$this->add_control(
			'woocpa_blog_caategory_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodCat' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_caategory_hover_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodCat' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'woocpa_blog_caategory_border',
				'label' => esc_html__( 'Border', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodCat',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'woocpa_blog_caategory_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodCat' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_caategory_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodCat' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_caategory_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodCat' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_price_options',
			[
				'label' => esc_html__( 'Price', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'woocpa_price' => 'yes',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_new_price_options',
			[
				'label' => esc_html__( 'New Price', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		// New Price
		// Hover control start for taxonomy author
		$this->start_controls_tabs(
			'woocpa_blog_taxonomy_new_price_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_taxonomy_new_price_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_taxonomy_new_price_typography',
				'selector' => '{{WRAPPER}} .woocpa-salePrice',
			]
		);
		$this->add_control(
			'woocpa_blog_taxonomy_new_price_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-salePrice' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_taxonomy_new_price_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-salePrice' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_taxonomy_new_price_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_taxonomy_new_price_hover_typography',
				'selector' => '{{WRAPPER}} .woocpa-salePrice:hover',
			]
		);
		$this->add_control(
			'woocpa_blog_taxonomy_new_price_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-salePrice:hover' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_taxonomy_new_price_hover_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-salePrice:hover' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		$this->add_responsive_control(
            'woocpa_blog_new_price_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
                'size_units' => ['px', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-salePrice' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_new_price_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-salePrice' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_taxonomy_new_price_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-salePrice' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		// Old Price
		$this->add_control(
			'woocpa_blog_new_old_price_options',
			[
				'label' => esc_html__( 'Old Price', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		// Hover control start for taxonomy author
		$this->start_controls_tabs(
			'woocpa_blog_taxonomy_old_price_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_taxonomy_old_price_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_taxonomy_old_price_typography',
				'selector' => '{{WRAPPER}} .woocpa-regularPrice',
			]
		);
		$this->add_control(
			'woocpa_blog_taxonomy_old_price_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-regularPrice' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_taxonomy_old_price_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-regularPrice' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_taxonomy_old_price_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_taxonomy_old_price_hover_typography',
				'selector' => '{{WRAPPER}} .woocpa-regularPrice:hover',
			]
		);
		$this->add_control(
			'woocpa_blog_taxonomy_old_price_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-regularPrice:hover' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_taxonomy_old_price_hover_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-regularPrice:hover' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		$this->add_responsive_control(
            'woocpa_blog_old_price_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
                'size_units' => ['px', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-regularPrice' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_old_price_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-regularPrice' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_taxonomy_old_price_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-regularPrice' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_sale_style_section',
			[
				'label' => esc_html__( 'Sale', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'woocpa_sale' => 'yes',
				],
			]
		);
		// Hover control start for description
		$this->start_controls_tabs(
			'woocpa_blog_sale_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_sale_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_sale_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodSaleCount',
			]
		);
		$this->add_control(
			'woocpa_blog_sale_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodSaleCount' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_sale_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodSaleCount' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_sale_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_sale_hover_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodSaleCount',
			]
		);
		$this->add_control(
			'woocpa_blog_sale_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodSaleCount' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_sale_hover_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodSaleCount' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'woocpa_blog_sale_border',
				'label' => esc_html__( 'Border', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodSaleCount',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'woocpa_blog_sale_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodSaleCount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_sale_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodSaleCount' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_sale_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodSaleCount' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_date_style_section',
			[
				'label' => esc_html__( 'Date', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'woocpa_date' => 'yes',
				],
			]
		);
		// Hover control start for description
		$this->start_controls_tabs(
			'woocpa_blog_date_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_date_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_date_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default .woocpa-publishDate',
			]
		);
		$this->add_control(
			'woocpa_blog_date_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-publishDate' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_date_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-publishDate' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_date_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_date_hover_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-publishDate',
			]
		);
		$this->add_control(
			'woocpa_blog_date_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-publishDate' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_date_hover_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-publishDate' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'woocpa_blog_date_border',
				'label' => esc_html__( 'Border', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default .woocpa-publishDate',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'woocpa_blog_date_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-publishDate' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_date_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-publishDate' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_date_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-publishDate' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_type_style_section',
			[
				'label' => esc_html__( 'Type', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'woocpa_type' => 'yes',
				],
			]
		);
		// Hover control start for description
		$this->start_controls_tabs(
			'woocpa_blog_type_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_type_normal_post',
			[
				'label' => esc_html__( 'Normal', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_type_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodType',
			]
		);
		$this->add_control(
			'woocpa_blog_type_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodType' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_type_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodType' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_type_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_blog_type_hover_typography',
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodType',
			]
		);
		$this->add_control(
			'woocpa_blog_type_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodType' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_type_hover_bg_color',
			[
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default:hover .woocpa-prodType' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		
		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'woocpa_blog_type_border',
				'label' => esc_html__( 'Border', 'woocommerce-product-accordion' ),
				'selector' => '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodType',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'woocpa_blog_type_border_radius',
            [
                'label' => esc_html__('Border Radius', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodType' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_type_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodType' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_type_margin',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-Accordion-default .woocpa-prodType' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_review_style_section',
			[
				'label' => esc_html__( 'Review', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'woocpa_review' => 'yes',
				],
			]
		);
		// ============================================
		// Hover control start for box
		$this->start_controls_tabs(
			'woocpa_blog_review_style_post'
		);
		$this->start_controls_tab(
			'woocpa_blog_review_normal_post',
			[
				'label' => esc_html__( 'Icons', 'woocommerce-product-accordion' ),
			]
		);
		// Normal Controls
		$this->add_responsive_control(
			'woocpa_the_post_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-rating-box  .woocpa-star-icons' => 'font-size: {{SIZE}}px',
				],
			]
		);
		$this->add_control(
			'woocpa_blog_taxonomy_comment_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-rating-box  .woocpa-star-icons' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'woocpa_blog_review_hover_tab',
			[
				'label' => esc_html__( 'Number', 'woocommerce-product-accordion' ),
			]
		);
		// Hover Controls
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'woocpa_the_post_number_size',
				'selector' => '{{WRAPPER}} .woocpa-rating-box  .make_a_star',
			]
		);
		$this->add_control(
			'woocpa_blog_number_color',
			[
				'label' => esc_html__( 'Color', 'woocommerce-product-accordion' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocpa-rating-box  .make_a_star' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		// Hover Control End
		// ============================================
		$this->end_controls_section();

		$this->start_controls_section(
			'woocpa_blog_style_section',
			[
				'label' => esc_html__( 'Box Style', 'woocommerce-product-accordion' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'woocpa_blog_box_Wrap_bg_grediant_color',
				'label' => esc_html__( 'Background', 'woocommerce-product-accordion' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .woocpa-panel-body',
			]
		);
		$this->add_responsive_control(
			'woocpa_products_gap',
			[
				'label' => esc_html__( 'Item Gap', 'woocommerce-product-accordion' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocpa-Accordion-default' => 'margin-block-end: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control(
            'woocpa_blog_the_boxL_border_bottom',
            [
                'label' => esc_html__('Margin', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-panel-body' => 'margin-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; margin-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control(
            'woocpa_blog_boxL_padding',
            [
                'label' => esc_html__('Padding', 'woocommerce-product-accordion'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'rem', '%'],
                'selectors' => [
                    '{{WRAPPER}} .woocpa-panel-body' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{RIGHT}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_section();

	}
	protected function render() {
		$settings = $this->get_settings_for_display();
	
		$woocpa_icon = $settings['woocpa_icon'];
		$woocpa_description_swtcher = $settings['woocpa_description'];
		// ==================
		$woocpa_products_style = $settings['woocpa_products_zone_style'];
		$woocpa_description_words = $settings['woocpa_description_words'];
		$woocpa_word_trim_indi = $settings['woocpa_word_trim_indi'];
		$woocpa_price = $settings['woocpa_price'];

		$woocpa_cart_btn = $settings['woocpa_cart_btn'];
		$woocpa_cart_button = $settings['woocpa_cart_button'];
		$woocpa_sale = $settings['woocpa_sale'];
		$woocpa_date = $settings['woocpa_date'];
		$woocpa_type = $settings['woocpa_type'];

		$woocpa_sale_label = $settings['woocpa_sale_label'];
		$woocpa_type_label = $settings['woocpa_type_label'];

		$woocpa_paged = (get_query_var('paged')) ? get_query_var('paged') : 1; // Need For Pagination
		
		

		//Shahadat
		$woocpa_filter_by = $settings['woocpa_product_filter_by'];
		$woocpa_order_by = $settings['woocpa_product_order_by'];
		$woocpa_order = $settings['woocpa_product_order'];
		$woocpa_products_perpage = $settings['woocpa_the_product_per_page'];
		$woocpa_selected_statuses = $settings['woocpa_product_grid_products_status'];
		$woocpa_selected_category = $settings['woocpa_selected_category'];
		$woocpa_selected_tags = $settings['woocpa_selected_tags'];
		$woocpa_selected_include_products = $settings['woocpa_selected_include_products'];
		$woocpa_selected_exclude_products = $settings['woocpa_selected_exclude_products'];
		$woocpa_review_count = $settings['woocpa_review_count'];

		// Product Action 
		$woocpa_the_cart_type = $settings['woocpa_the_cart_type'];
		$woocpa_cart_button = $settings['woocpa_cart_button'];
		$woocpa_details_btn_text = $settings['woocpa_details_btn_text'];

		// Product Badge
		$woocpa_badge_one_position = $settings['woocpa_badge_one_position'];
		$woocpa_show_sale_badge = $settings['woocpa_show_sale_badge'];
		$woocpa_show_featured_badge = $settings['woocpa_show_featured_badge'];
		$woocpa_show_stock_out_badge = $settings['woocpa_show_stock_out_badge'];
		$woocpa_sale_badge_type = $settings['woocpa_sale_badge_type'];
		$woocpa_sale_badge_before_percent_text = $settings['woocpa_sale_badge_before_percent_text'];
		$woocpa_sale_badge_after_percent_text = $settings['woocpa_sale_badge_after_percent_text'];
		$woocpa_sale_badge_text = $settings['woocpa_sale_badge_text'];
		$woocpa_badge_featured_text = $settings['woocpa_show_badge_featured_text'];
		$woocpa_badge_stock_out_text = $settings['woocpa_show_badge_stock_out_text'];
		$woocpa_badge_stock_in_text = $settings['woocpa_show_badge_stock_in_text'];
		$woocpa_badge_stock_in_number_text = $settings['woocpa_show_badge_stock_in_number_text'];


		$woocpa_the_all_date = $settings['woocpa_the_all_date'];
		$woocpa_text_str = ' ';
		if('d' === $woocpa_the_all_date){
			$woocpa_coustom_day = $woocpa_text_str;
			$woocpa_coustom_month = $woocpa_text_str;
			$woocpa_coustom_year = $woocpa_text_str;
			$day =  date('d');
			$wdbp_time = ['day'  => $day];
		} elseif('m' === $woocpa_the_all_date){
			$woocpa_coustom_day = $woocpa_text_str;
			$woocpa_coustom_month = $woocpa_text_str;
			$woocpa_coustom_year = $woocpa_text_str;
			$month =  date('m'); 
			$wdbp_time = ['month'  => $month];
		} elseif('y' === $woocpa_the_all_date){
			$woocpa_coustom_day = $woocpa_text_str;
			$woocpa_coustom_month = $woocpa_text_str;
			$woocpa_coustom_year = $woocpa_text_str;
			$year =  date('y');
			$wdbp_time = ['year'  => $year];
		} elseif('coustom' === $woocpa_the_all_date){
			$woocpa_coustom_year = $settings['woocpa_blog_coustom_year'];
			$woocpa_coustom_month = $settings['woocpa_blog_coustom_month'];
			$woocpa_coustom_day = $settings['woocpa_blog_coustom_day'];
			$wdbp_time = $woocpa_text_str;
		} else{
			$woocpa_coustom_day = $woocpa_text_str;
			$woocpa_coustom_month = $woocpa_text_str;
			$woocpa_coustom_year = $woocpa_text_str;
			$wdbp_time = $woocpa_text_str;
		}


        $woocpa_args = array(
			'post_type'      => 'product',
			'posts_per_page' => $woocpa_products_perpage,
			'orderby'        => $woocpa_order_by,
			'order'          => $woocpa_order,
			'post__in'       => $woocpa_selected_include_products,
			'post__not_in'   => $woocpa_selected_exclude_products,
			'post_status'    => $woocpa_selected_statuses,
			'paged' 		 => $woocpa_paged,
			'date_query'     => $wdbp_time,
			'year'           => $woocpa_coustom_year,
			'monthnum'       => $woocpa_coustom_month,
			'day'            => $woocpa_coustom_day, 
		);

		switch ($woocpa_filter_by) {
			 case 'featured':
				$woocpa_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'featured',
					'operator' => 'IN',
				);
				break;

			case 'onsale':
				$woocpa_args['meta_query'] = array(
					'relation' => 'OR',
					array(
						'key' => '_sale_price',
						'value' => 0,
						'compare' => '>',
						'type' => 'NUMERIC',
					),
					array(
						'key' => '_sale_price',
						'value' => '',
						'compare' => '=',
					),
				);
				break;

			case 'toprated':
				$woocpa_args['meta_query'] = array(
					array(
						'key' => '_wc_average_rating',
						'compare' => '>',
						'value' => 0, 
						'type' => 'NUMERIC',
					),
				);
    			break;

			case 'bestselling':
				$woocpa_args['meta_query'] = array(
					array(
						'key' => 'total_sales',
						'compare' => '>',
						'value' => 0, 
						'type' => 'NUMERIC',
					),
				);
				break;
				
			case 'cheapest':
				$woocpa_args['meta_query'] = array(
					array(
						'key' => '_price',
						'compare' => '>',
						'value' => 0,
						'type' => 'NUMERIC',
					),
				);
				$woocpa_args['orderby'] = 'meta_value_num';
				$woocpa_args['order'] = 'ASC';
				break;

			case 'popularity':
				$woocpa_args['meta_query'] = array(
					array(
						'key' => 'total_sales',
						'compare' => '>',
						'value' => 0,
						'type' => 'NUMERIC',
					),
				);
				break;

			case 'recommended':
				$woocpa_args['meta_query'] = array(
					'relation' => 'AND',
					array(
						'key' => '_wc_average_rating',
						'compare' => '>',
						'value' => 4.5,
						'type' => 'NUMERIC',
					),
					array(
						'key' => '_stock_status',
						'value' => 'instock',
					)
				);
				break;	

			case 'expensive':
				$woocpa_args['meta_query'] = array(
					array(
						'key' => '_price',
						'type' => 'NUMERIC',
					),
				);
				$woocpa_args['orderby'] = 'meta_value_num';
				$woocpa_args['order'] = 'DESC';
				break;

			case 'recentlyadded': 
				$woocpa_args['date_query'] = array(
					array(
						'after' => '6 months ago',
						'inclusive' => true,						
					)
				);
				break;

			case 'instock':
				$woocpa_args['meta_query'] = array(
					array(
						'key' => '_stock_status',
						'value' => 'instock',						
					)
				);
				break;

			case 'outofstock':
				$woocpa_args['meta_query'] = array (
					array(
						'key' => '_stock_status',
						'value' => 'outofstock',						
					)
				);
				break;

			case 'limitedstock':
				$woocpa_args['meta_query'] = array (
					array(
						'key' => '_stock_status',
						'value' => 'instock',
					),
					array (
						'key' => '_stock',
						'value' => 10,
						'compare' => '<=',
						'type' => 'NUMERIC',
					)
				);
				break;
				default:
					'recentlyadded';
				break;
		}

		// If a specific category is selected, add a category argument
		if ($woocpa_selected_category) {
			$woocpa_args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $woocpa_selected_category,
			);
		}

		// If specific tags are selected, add a tag filter to the query
		if (!empty($woocpa_selected_tags)) {
			$woocpa_args['tax_query'][] = array(
				'taxonomy' => 'product_tag',
				'field'    => 'term_id',
				'terms'    => $woocpa_selected_tags,
				'operator' => 'IN',
			);
		}

		


		if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))):
			echo '<div class="woocpa-accordion-all">';
			// Step indicator
			echo '<div class="woocpa-steps">';
		echo '<div class="woocpa-step active" data-step="1">';
			echo '<span class="step-number">1</span>';
			echo '<span class="step-text">Choose Package</span>';
		echo '</div>';
		echo '<div class="woocpa-step-line"></div>';
		echo '<div class="woocpa-step" data-step="2">';
			echo '<span class="step-number">2</span>';
			echo '<span class="step-text">Payment Details</span>';
		echo '</div>';
		echo '<div class="woocpa-step-line"></div>';
		echo '<div class="woocpa-step" data-step="3">';
			echo '<span class="step-number">3</span>';
			echo '<span class="step-text">Confirmation</span>';
		echo '</div>';
	echo '</div>';
		
			echo '<div class="woocpa-step-content" id="step-1">';
				echo '<div class="woocpa-Accordion-'.esc_attr($woocpa_products_style).' woocpa-accordion-common">';
				$woocpa_all_products = new \WP_Query( $woocpa_args ); 
				if($woocpa_all_products->have_posts()):
					while ( $woocpa_all_products->have_posts() ) : $woocpa_all_products->the_post();
						global $product;
						echo '<div class="woocpa-Accordion-default" data-product-id="'.esc_attr($product->get_id()).'">';
							echo '<div class="woocpa-Accordion-heading woocpa-panel-heading">';
								echo '<h4 class="woocpa-panel-title">';
									echo '<span class="woocpa-collapsed">' . esc_html( $product->get_name() ) . '</span>';
									if('yes' === $woocpa_icon):
										echo '<span class="woocpa-collapsed-icon woocpa-colexp-icon">'; 
										\Elementor\Icons_Manager::render_icon( $settings['woocpa_accordion_collapsed_icon'], [ 'aria-hidden' => 'true' ] ); 
										echo '</span>';
										echo '<span class="woocpa-expand-icon woocpa-colexp-icon">'; 
										\Elementor\Icons_Manager::render_icon( $settings['woocpa_accordion_expand_icon'], [ 'aria-hidden' => 'true' ] ); 
										echo '</span>';
									endif;
								echo '</h4>';
							echo '</div>';
							
							echo '<div class="woocpa-accordion-content">';
								echo '<div class="woocpa-panel-body">';
									// Product Image
									$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'woocommerce-product-accordion' );
									if( has_post_thumbnail() ){
										echo '<div class="woocpa-productImg">';
											echo '<a href="' . get_permalink() . '">';
												echo '<img src="' . $image[0] . '" alt="'.esc_attr($product->get_name()).'">';
											echo '</a>';
											include(__DIR__.'/common/badge.php');
										echo '</div>';
									} else {
										echo '<div class="woocpa-no-image">' . esc_html__('No Thumbnail', 'woocommerce-product-accordion') . '</div>';
									}
									
									echo '<div class="woocpa-prodCont">';
										include(__DIR__.'/common/category.php');
										
										echo '<div class="woocpa-prodpriratbtn">';
											echo '<div class="woocpa-prodpriratwrap">';
												include(__DIR__.'/common/price.php');
												include(__DIR__.'/common/review.php');
											echo '</div>';
											echo '<div class="woocpa-prodOptInfo">';
									
									if ($product->is_type('variable')) {
										// For variable products, show variation selector
										$variations = $product->get_available_variations();
										if (!empty($variations)) {
											echo '<select class="woocpa-variation-select" data-product-id="' . $product->get_id() . '">';
											echo '<option value="">Choose an option</option>';
											foreach ($variations as $variation) {
												$variation_obj = wc_get_product($variation['variation_id']);
												$price = $variation_obj->get_price();
												$attributes = [];
												foreach ($variation['attributes'] as $attr_name => $attr_value) {
													$attributes[] = $attr_value;
												}
												$option_text = implode(', ', $attributes) . ' - ' . wc_price($price);
												echo '<option value="' . $variation['variation_id'] . '" data-price="' . $price . '">' . $option_text . '</option>';
											}
											echo '</select>';
										}
										echo '<button class="woocpa-add-to-cart woocpa-add-variation woocpa-cartBtn" data-product-id="' . $product->get_id() . '" data-product-name="' . esc_attr($product->get_name()) . '" data-is-variable="true">Add to Cart</button>';
									} else {
										// For simple products
										echo '<button class="woocpa-add-to-cart woocpa-cartBtn" data-product-id="' . $product->get_id() . '" data-product-name="' . esc_attr($product->get_name()) . '" data-product-price="' . $product->get_price() . '" data-is-variable="false">Add to Cart</button>';
									}
									
									echo '</div>';
								echo '</div>';
										
										echo '<div class="woocpa-prodOptInfo">';
											if('yes' === $woocpa_sale) {
												echo '<div class="woocpa-prodSaleCount woocpa-extra-meta">';
													echo esc_html($woocpa_sale_label) . '<span>' . $product->get_total_sales() . '</span>';
												echo '</div>';
											}
											
											if('yes' === $woocpa_date) {
												echo '<div class="woocpa-publishDate woocpa-extra-meta">';
													echo '<span>'.date( 'j M Y', strtotime( $product->get_date_created() )).'</span>';
												echo '</div>';
											}
											
											if('yes' === $woocpa_type) {
												echo '<div class="woocpa-prodType woocpa-extra-meta">';
													echo esc_html($woocpa_type_label) . '<span>' . $product->get_type() . '</span>';
												echo '</div>';
											}
										echo '</div>';
										
										if('yes' === $woocpa_description_swtcher) {
											$woocpa_description = wp_trim_words($product->get_short_description(), $woocpa_description_words, $woocpa_word_trim_indi);
											echo '<div class="woocpa-desc">';
												echo '<p class="woocpa-prodDesc">'.$woocpa_description.'</p>';
											echo '</div>';
										}
									echo '</div>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					endwhile;
				else: 
					echo '<div class="woocpa-none-product" style="text-align:center;">'.esc_html__('You don\'t have any products. Please add your products.').'</div>'; 
				endif;
				wp_reset_query();
				echo '</div>';
		
				// Cart Table
				echo '<div class="woocpa-cart-section" style="display:none;">';
					echo '<div class="woocpa-cart-header">Your Booking Overview</div>';
					echo '<table class="woocpa-cart-table">';
						echo '<thead>';
							echo '<tr>';
								echo '<th>Product</th>';
								echo '<th>Price</th>';
								echo '<th>Quantity</th>';
								echo '<th>Subtotal</th>';
								echo '<th>Action</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody id="woocpa-cart-items"></tbody>';
						echo '<tfoot>';
							echo '<tr>';
								echo '<td colspan="3"><strong>Total</strong></td>';
								echo '<td><strong id="woocpa-cart-total">$0.00</strong></td>';
								echo '<td></td>';
							echo '</tr>';
						echo '</tfoot>';
					echo '</table>';
					echo '<button class="woocpa-proceed-checkout">PROCEED WITH THIS BOOKING</button>';
				echo '</div>';
			echo '</div>';
		
			// Step 2: Checkout
			echo '<div class="woocpa-step-content" id="step-2" style="display:none;">';
				echo '<div id="woocpa-checkout-form"></div>';
			echo '</div>';
		
			// Step 3: Thank You
			echo '<div class="woocpa-step-content" id="step-3" style="display:none;">';
				echo '<div id="woocpa-thankyou-content"></div>';
			echo '</div>';
		
		echo '</div>';
		else: echo '<a href="https://WordPress.org/plugins/WooCommerce/"><div class="woocpa_wooc_not_acivate_notice">'.esc_html__('May be you are not installed/activated WooCommerce plugin.', 'woocommerce-product-accordion').'</div></a>'; endif;
	}
}
