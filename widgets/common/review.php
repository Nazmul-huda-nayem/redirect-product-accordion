<?php if ('yes' === $settings['woocpa_review']) : ?>
    <div class="woocpa-rating-box">
        <?php
        $review_count = $product->get_review_count();
        $average_rating = $product->get_average_rating();
        
        echo '<div class="woocpa-star-rating">';
        for ($i = 1; $i <= 5; $i++) {
            $star_class = ($i <= $average_rating) ? 'fas fa-star' : 'far fa-star';
            if ($i - 0.5 == $average_rating) {
                $star_class = 'fas fa-star-half-alt';
            }
            echo '<span class="woocpa-star-icons ' . $star_class . '" aria-hidden="true"></span>';
        }
        echo '</div>';
        if('yes' === $woocpa_review_count){
            if ($review_count > 0) {
                echo '<div class="make_a_star">(' . $review_count . ')</div>';
            }
        }
        ?>
    </div>
<?php endif; ?>
