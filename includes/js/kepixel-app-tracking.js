jQuery(document).ready(function ($) {
    _paq = _paq || [];

    // Check if this is the first page view in the session
    // We'll use a session storage flag to track this
    if (!sessionStorage.getItem('kepixel_session_started')) {
        // Set the flag to prevent tracking app open again in this session
        sessionStorage.setItem('kepixel_session_started', 'true');

        // Enhanced ecommerce tracking - trackAppOpen
        // This is a proxy for app open in a web context - it tracks the first page view in a session
        _paq.push(['trackAppOpen',
            {},                         // user_data - already set in main tracking code if user is logged in
            {}                          // custom_data
        ]);
    }
});
