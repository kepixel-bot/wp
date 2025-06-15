jQuery(document).ready(function ($) {
    _paq = _paq || [];

    // Track add to wishlist events
    $(document).on('click', '.add_to_wishlist', function() {
        // Get product data from the clicked element
        var $button = $(this);
        var productId = $button.data('product-id') || $button.attr('data-product-id');
        var productName = $button.data('product-name') || $button.attr('data-product-name') || '';
        var productPrice = $button.data('product-price') || $button.attr('data-product-price') || 0;

        // If product name is not available, try to get it from the page
        if (!productName) {
            productName = $('.product_title').text() || '';
        }

        // Create items array for tracking
        var items = [{
            id: productId,
            name: productName,
            price: productPrice,
            quantity: 1
        }];

        // Enhanced ecommerce tracking - trackAddToWishlist
        _paq.push(['trackAddToWishlist',
            items,                      // items
            {},                         // user_data - already set in main tracking code if user is logged in
            {}                          // custom_data
        ]);
    });
});
