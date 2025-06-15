jQuery(document).ready(function ($) {
    _paq = _paq || [];

    // Enhanced ecommerce tracking - trackListView
    _paq.push(['trackListView',
        wpKepixelListData.listId,       // id
        wpKepixelListData.listName,     // name
        wpKepixelListData.listCategory, // category
        wpKepixelListData.listType,     // type
        {},                             // user_data - already set in main tracking code if user is logged in
        {}                              // custom_data
    ]);
});
