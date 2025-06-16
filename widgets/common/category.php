<?php
if('yes' === $settings['woocpa_cat_show']){
    $categories = get_the_terms( $product->get_id(), 'product_cat' );
    if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
        echo '<div class="woocpa-prodCatx">';
        foreach ( $categories as $category ) {
            echo '<a class="woocpa-prodCat" href="' . get_term_link( $category->term_id, 'product_cat' ) . '">' . $category->name . '</a>';
        }
        echo '</div>';
    }
}
?>