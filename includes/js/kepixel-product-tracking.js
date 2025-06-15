jQuery(document).ready(function ($) {
    _paq = _paq || [];

    // Standard ecommerce tracking
    _paq.push(['setEcommerceView',
        wpKepixelProductData.sku,
        wpKepixelProductData.name,
        wpKepixelProductData.categoryList,
        wpKepixelProductData.price
    ]);

    // Enhanced ecommerce tracking - trackViewContent
    _paq.push(['trackViewContent',
        wpKepixelProductData.sku,         // id
        wpKepixelProductData.name,        // name
        'USD',                            // currency - assuming USD, modify as needed
        'product',                        // type
        wpKepixelProductData.price,       // value
        {},                               // user_data - already set in main tracking code if user is logged in
        {}                                // custom_data
    ]);
});
