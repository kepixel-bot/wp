jQuery(document).ready(function ($) {
    _paq = _paq || [];

    // Standard ecommerce tracking
    wpKepixelOrderData.items.forEach(function (item) {
        _paq.push(['addEcommerceItem',
            item.sku,
            item.name,
            item.category_name,
            item.price,
            item.quantity
        ]);
    });

    _paq.push(['trackEcommerceOrder',
        wpKepixelOrderData.order_number,
        wpKepixelOrderData.total,
        wpKepixelOrderData.subtotal,
        wpKepixelOrderData.total_tax,
        wpKepixelOrderData.shipping_total,
        false
    ]);

    // Enhanced ecommerce tracking - trackPurchase
    _paq.push(['trackPurchase',
        wpKepixelOrderData.order_number, // order_id
        'USD', // currency - assuming USD, modify as needed
        wpKepixelOrderData.total, // value
        wpKepixelOrderData.items, // items
        'Purchase completed', // description
        {}, // user_data - already set in main tracking code if user is logged in
        {} // custom_data
    ]);
});
