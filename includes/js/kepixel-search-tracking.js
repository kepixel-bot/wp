jQuery(document).ready(function ($) {
    _paq = _paq || [];

    // Enhanced ecommerce tracking - trackSearch
    _paq.push(['trackSearch',
        wpKepixelSearchData.searchQuery, // search_string
        {},                              // user_data - already set in main tracking code if user is logged in
        {}                               // custom_data
    ]);
});
