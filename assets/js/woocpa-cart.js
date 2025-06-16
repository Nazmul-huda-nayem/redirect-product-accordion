jQuery(document).ready(function($) {
    function updateCart() {
        $.ajax({
            url: woocpa_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'woocpa_load_cart',
                security: woocpa_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    const cartContainer = $('#woocpa-cart-container');
                    const cartItems = response.data.cart_items;
                    let itemsHtml = '';

                    if (cartItems.length > 0) {
                        cartItems.forEach(function(item) {
                            itemsHtml += `
                                <div class="woocpa-cart-item">
                                    <div class="woocpa-cart-item-image">
                                        <img src="${item.image}" alt="${item.title}">
                                    </div>
                                    <div class="woocpa-cart-item-details">
                                        <h4>${item.title}</h4>
                                        <div class="woocpa-cart-item-price">${item.price}</div>
                                        <div class="woocpa-cart-item-quantity">Quantity: ${item.quantity}</div>
                                        <div class="woocpa-cart-item-total">Total: ${item.total}</div>
                                    </div>
                                </div>
                            `;
                        });

                        $('.woocpa-cart-items').html(itemsHtml);
                        $('.woocpa-cart-count').html(`Items: ${response.data.cart_count}`);
                        $('.woocpa-cart-total').html(`Total: ${response.data.cart_total}`);
                    } else {
                        $('.woocpa-cart-items').html('<p>Your cart is empty</p>');
                        $('.woocpa-cart-count').html('');
                        $('.woocpa-cart-total').html('');
                    }
                }
            }
        });
    }

    // Update cart when page loads
    updateCart();

    // Update cart when product is added
    $(document).on('added_to_cart', function() {
        updateCart();
    });
});