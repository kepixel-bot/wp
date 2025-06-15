jQuery(document).ready(function ($) {
    _paq = _paq || [];

    // Enhanced ecommerce tracking - trackCompleteRegistration
    _paq.push(['trackCompleteRegistration',
        {method: 'wordpress'}, // params
        {},                    // user_data - already set in main tracking code if user is logged in
        {}                     // custom_data
    ]);

    // Enhanced ecommerce tracking - trackSignUp
    _paq.push(['trackSignUp',
        {},                    // user_data - already set in main tracking code if user is logged in
        {}                     // custom_data
    ]);
});
