<div class="woocpa_product_price">
    <?php
    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        $prices = array();

        foreach ($variations as $variation) {
            $variation_price = $variation['display_price']; 
            $prices[] = $variation_price; 
        }

        if (!empty($prices)) {
            $min_price = wc_price(min($prices));
            $max_price = wc_price(max($prices));
            echo $min_price . ' - ' . $max_price;
        } else {
            echo "No variations price available for this product.";
        }
    } else {
        $regular_price = wc_get_price_to_display($product, array('price' => $product->get_regular_price()));
        $sale_price = wc_get_price_to_display($product, array('price' => $product->get_sale_price()));

        $woocpa_regular_price = '<div class="woocpa-regular-price woocpa-sale-price"><del>' . wc_price($regular_price) . '</del></div><div class="woocpa-current-price"> ' . wc_price($sale_price) . '</div>';
        $woocpa_dis_price = '<div class="woocpa-regular-price"> ' . wc_price($regular_price) . '</div>';
        $woocpa_sale_check = ($product->is_on_sale()) ? $woocpa_regular_price : $woocpa_dis_price;
        $woocpa_regu_check = ($regular_price) ? $woocpa_sale_check : '';
        echo $woocpa_regu_check;
    }
    ?>
</div>
