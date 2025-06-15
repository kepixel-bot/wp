jQuery(document).ready(function ($) {
    if (typeof wpKepixelCheckoutData !== 'undefined' && wpKepixelCheckoutData.items.length > 0) {
        _paq = _paq || [];

        // Enhanced ecommerce tracking - trackInitiateCheckout
        _paq.push(['trackInitiateCheckout',
            wpKepixelCheckoutData.cartTotal, // value
            'USD', // currency - assuming USD, modify as needed
            wpKepixelCheckoutData.items, // items
            {}, // user_data - already set in main tracking code if user is logged in
            {} // custom_data
        ]);

        // Track when payment information is added
        // This will trigger when the user interacts with payment fields
        var paymentTracked = false;

        // Listen for changes to payment fields
        $(document.body).on('change', 'input[name^="payment_method"], #payment input, #payment select, #payment textarea', function() {
            if (!paymentTracked) {
                // Enhanced ecommerce tracking - trackAddPaymentInfo
                _paq.push(['trackAddPaymentInfo',
                    wpKepixelCheckoutData.cartTotal, // value
                    'USD', // currency - assuming USD, modify as needed
                    wpKepixelCheckoutData.items, // items
                    {}, // user_data - already set in main tracking code if user is logged in
                    {} // custom_data
                ]);

                // Set flag to prevent multiple tracking
                paymentTracked = true;
            }
        });
    }
});
