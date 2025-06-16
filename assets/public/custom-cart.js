jQuery(document).ready(function($) {
    let cart = [];
    let currentStep = 1;

    // Handle variation selection
    $(document).on('change', '.woocpa-variation-select', function() {
        const select = $(this);
        const productContainer = select.closest('.woocpa-Accordion-default');
        const addToCartBtn = productContainer.find('.woocpa-add-variation');
        const selectedOption = select.find(':selected');
        
        if (select.val()) {
            const variationId = select.val();
            const variationPrice = selectedOption.data('price');
            
            addToCartBtn.prop('disabled', false)
                      .data('variation-id', variationId)
                      .data('product-price', variationPrice);
        } else {
            addToCartBtn.prop('disabled', true)
                      .removeData('variation-id')
                      .removeData('product-price');
        }
    });

    // Add to Cart AJAX
    $(document).on('click', '.woocpa-add-to-cart', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const productId = button.data('product-id');
        const productName = button.data('product-name');
        const isVariable = button.data('is-variable');
        let variationId = 0;
        let productPrice = parseFloat(button.data('product-price'));
        
        // For variable products, get variation details
        if (isVariable) {
            variationId = button.data('variation-id');
            if (!variationId) {
                alert('Please select a variation');
                return;
            }
        }
        
        button.text('Adding...').prop('disabled', true);
        
        // Prepare AJAX data
        const ajaxData = {
            action: 'woocommerce_add_to_cart',
            product_id: productId,
            quantity: 1
        };
        
        // Add variation ID if it's a variable product
        if (variationId) {
            ajaxData.variation_id = variationId;
        }
        
        // Add to WooCommerce cart via AJAX
        $.post(wc_add_to_cart_params.ajax_url, ajaxData, function(response) {
            if (response.error) {
                alert('Error adding product to cart');
                button.text('Add to Cart').prop('disabled', false);
                return;
            }
            
            // Create unique cart item identifier
            const cartItemId = variationId ? `${productId}_${variationId}` : productId;
            const displayName = variationId ? `${productName} (${button.closest('.woocpa-Accordion-default').find('.woocpa-variation-select :selected').text().split(' - ')[0]})` : productName;
            
            // Add to local cart array
            const existingItem = cart.find(item => item.id === cartItemId);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: cartItemId,
                    product_id: productId,
                    variation_id: variationId,
                    name: displayName,
                    price: productPrice,
                    quantity: 1
                });
            }
            
            updateCartDisplay();
            button.text('Added!').removeClass('woocpa-add-to-cart').addClass('added');
            
            setTimeout(() => {
                button.text('Add to Cart').prop('disabled', false).removeClass('added').addClass('woocpa-add-to-cart');
            }, 2000);
        }).fail(function() {
            alert('Network error. Please try again.');
            button.text('Add to Cart').prop('disabled', false);
        });
    });

    // Update cart display
    function updateCartDisplay() {
        const cartItems = $('#woocpa-cart-items');
        const cartSection = $('.woocpa-cart-section');
        
        if (cart.length === 0) {
            cartSection.hide();
            return;
        }
        
        cartSection.show();
        cartItems.empty();
        
        let total = 0;
        cart.forEach(item => {
            const subtotal = item.price * item.quantity;
            total += subtotal;
            
            cartItems.append(`
                <tr data-product-id="${item.id}">
                    <td>${item.name}</td>
                    <td>${item.price.toFixed(2)}</td>
                    <td>
                        <button class="qty-btn minus">-</button>
                        <span class="quantity">${item.quantity}</span>
                        <button class="qty-btn plus">+</button>
                    </td>
                    <td>${subtotal.toFixed(2)}</td>
                    <td><button class="remove-item">Remove</button></td>
                </tr>
            `);
        });
        
        $('#woocpa-cart-total').text(`$${total.toFixed(2)}`);
    }

    // Quantity controls
    $(document).on('click', '.qty-btn', function() {
        const row = $(this).closest('tr');
        const productId = row.data('product-id');
        const isPlus = $(this).hasClass('plus');
        
        const item = cart.find(item => item.id === productId);
        if (item) {
            if (isPlus) {
                item.quantity += 1;
            } else if (item.quantity > 1) {
                item.quantity -= 1;
            }
            updateCartDisplay();
        }
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        const row = $(this).closest('tr');
        const productId = row.data('product-id');
        
        cart = cart.filter(item => item.id !== productId);
        updateCartDisplay();
    });

    // Proceed to checkout
    $(document).on('click', '.woocpa-proceed-checkout', function() {
        if (cart.length === 0) return;
        
        // Load checkout form
        $.post(woocpa_ajax.ajax_url, {
            action: 'woocpa_load_checkout',
            cart_data: JSON.stringify(cart),
            nonce: woocpa_ajax.nonce
        }, function(response) {
            $('#woocpa-checkout-form').html(response.data);
            showStep(2);
        });
    });

    // Step navigation
    function showStep(step) {
        $('.woocpa-step-content').hide();
        $('.woocpa-step').removeClass('active');
        
        $(`#step-${step}`).show();
        $(`.woocpa-step[data-step="${step}"]`).addClass('active');
        
        currentStep = step;
    }

    // Handle checkout form submission
    $(document).on('submit', '#woocpa-checkout-form', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        // Show loading state
        $('.woocpa-place-order').text('Processing...').prop('disabled', true);
        
        $.post(woocpa_ajax.ajax_url, {
            action: 'woocpa_process_checkout',
            form_data: formData,
            cart_data: JSON.stringify(cart),
            nonce: woocpa_ajax.nonce
        }, function(response) {
            if (response.success) {
                $('#woocpa-thankyou-content').html(response.data);
                showStep(3);
                // Clear cart after successful order
                cart = [];
            } else {
                alert('Checkout failed: ' + response.data);
                $('.woocpa-place-order').text('Place Order').prop('disabled', false);
            }
        }).fail(function() {
            alert('Network error. Please try again.');
            $('.woocpa-place-order').text('Place Order').prop('disabled', false);
        });
    });
});