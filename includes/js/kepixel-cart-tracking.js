jQuery(document).ready(function ($) {
    if (typeof wpKepixelCartData !== 'undefined' && wpKepixelCartData.items.length > 0) {
        _paq = _paq || [];

        // Standard ecommerce tracking
        wpKepixelCartData.items.forEach(function (item) {
            _paq.push(['addEcommerceItem',
                item.sku,
                item.name,
                item.category,
                item.price,
                item.quantity
            ]);
        });
        _paq.push(['trackEcommerceCartUpdate', wpKepixelCartData.cartTotal]);

        // Enhanced ecommerce tracking - trackAddToCart
        _paq.push(['trackAddToCart',
            'USD', // currency - assuming USD, modify as needed
            wpKepixelCartData.cartTotal, // value
            wpKepixelCartData.items, // items
            'Cart updated', // description
            {}, // user_data - already set in main tracking code if user is logged in
            {} // custom_data
        ]);
    }
});
