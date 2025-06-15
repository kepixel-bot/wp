jQuery(document).ready(function ($) {
    _paq = _paq || [];

    // Enhanced ecommerce tracking - trackPage
    _paq.push(['trackPage',
        wpKepixelPageData.pageId,       // id
        wpKepixelPageData.pageName,     // name
        wpKepixelPageData.pageCategory, // category
        wpKepixelPageData.pageType,     // type
        {},                             // user_data - already set in main tracking code if user is logged in
        {}                              // custom_data
    ]);
});
