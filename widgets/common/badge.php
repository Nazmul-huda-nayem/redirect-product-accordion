<div class="woocpa-sale-featured-badge <?php echo esc_attr( $woocpa_badge_one_position == 'right' ) ? 'woocpa-badge-right' : 'woocpa-badge-left'; ?>">
    <?php
    // Feature Badge
    if ('yes' === $woocpa_show_featured_badge && $product->is_featured()) {
        echo '<p class="woocpa-featured-badge">' . esc_html($woocpa_badge_featured_text) . '</p>';
    }
    // Sale Badge
    if ('yes' === $woocpa_show_sale_badge && $product->is_on_sale()) {
        if ($product->is_type('variable')) {
            $variation_ids = $product->get_children();
            $percent_offs = array();

            foreach ($variation_ids as $variation_id) {
                $variation = wc_get_product($variation_id);
                $regular_price = $variation->get_regular_price();
                $sale_price = $variation->get_sale_price();

                if (is_numeric($regular_price) && is_numeric($sale_price) && $regular_price != 0) {
                    $percent_off = round(100 - ($sale_price / $regular_price) * 100);
                    $percent_offs[] = $percent_off;
                }
            }

            $average_percent_off = count($percent_offs) > 0 ? round(array_sum($percent_offs) / count($percent_offs)) : 0;

            $badge_content = ('percent' === $woocpa_sale_badge_type) ?
                esc_html($woocpa_sale_badge_before_percent_text) . ' ' . $average_percent_off . '% ' . esc_html($woocpa_sale_badge_after_percent_text) :
                esc_html($woocpa_sale_badge_text);
        } elseif ($product->is_type('grouped')) {
            $grouped_products = $product->get_children();
            $total_regular_price = 0;
            $total_sale_price = 0;
            $num_grouped_products = count($grouped_products);

            foreach ($grouped_products as $grouped_product_id) {
                $grouped_product = wc_get_product($grouped_product_id);
                $total_regular_price += $grouped_product->get_regular_price();
                $total_sale_price += $grouped_product->get_sale_price();
            }

            if ($num_grouped_products > 0) {
                $average_regular_price = $total_regular_price / $num_grouped_products;
                $average_sale_price = $total_sale_price / $num_grouped_products;

                if ($average_regular_price != 0) {
                    $average_percent_off = round(100 - ($average_sale_price / $average_regular_price) * 100);
                    $badge_content = ('percent' === $woocpa_sale_badge_type) ?
                        esc_html($woocpa_sale_badge_before_percent_text) . ' ' . $average_percent_off . '% ' . esc_html($woocpa_sale_badge_after_percent_text) :
                        esc_html($woocpa_sale_badge_text);
                }
            }
        } else {
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();

            if (is_numeric($regular_price) && is_numeric($sale_price) && $regular_price != 0) {
                $percent_off = round(100 - ($sale_price / $regular_price) * 100);
                $badge_content = ('percent' === $woocpa_sale_badge_type) ?
                    esc_html($woocpa_sale_badge_before_percent_text) . ' ' . $percent_off . '% ' . esc_html($woocpa_sale_badge_after_percent_text) :
                    esc_html($woocpa_sale_badge_text);
            }
        }
        if (isset($badge_content)) {
            echo '<p class="woocpa-sale-badge">' . esc_html($badge_content) . '</p>';
        }
    }
    //Stock Badge
    if ('yes' === $woocpa_show_stock_out_badge) {
        echo !$product->is_in_stock() ? '<p class="woocpa-stock-badge">' . esc_html($woocpa_badge_stock_out_text) . '</p>' : '<p class="woocpa-stock-badge woocpa-stock-count">' . ($product->get_stock_quantity() > 0 ? esc_html__($woocpa_badge_stock_in_number_text) . ' : ' . $product->get_stock_quantity() : esc_html__($woocpa_badge_stock_in_text)) . '</p>';
    }?>
</div>