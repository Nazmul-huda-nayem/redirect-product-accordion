

<?php if('yes' === $settings['woocpa_cart_btn']): ?>
    <div class="woocpa_cart_bttn">
        <a href="<?php echo esc_url(('cart' === $woocpa_the_cart_type || 'icon' === $woocpa_the_cart_type) ? $product->add_to_cart_url() : get_permalink()); ?>" class="woocpa-cartBtn">
            <?php 
            echo ('cart' === $woocpa_the_cart_type) ? esc_html__($woocpa_cart_button) : 
                (('buy' === $woocpa_the_cart_type) ? esc_html__($woocpa_details_btn_text) : '');

            if ('icon' === $woocpa_the_cart_type) {
                \Elementor\Icons_Manager::render_icon($settings['woocpa_cart_button_icon'], ['aria-hidden' => 'true']);
            } 
            ?>
        </a>
            

    </div>
<?php endif; ?>
