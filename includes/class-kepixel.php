<?php
defined('ABSPATH') || die();

/**
 * Add Kepixel tracking code to the site
 */
function kepixel_add_tracking_code()
{
    $app_id = get_option('kepixel_app_id');
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking code if app ID is set and tracking is enabled
    if (empty($app_id) || !$enable_tracking) {
        return;
    }
    ?>

    <!-- Kepixel -->
    <script>
        var _paq = window._paq = window._paq || [];
        <?php
        // Set user ID based on user email if user is logged in
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            echo "_paq.push(['setUserId', '" . esc_js($current_user->user_email) . "']);\n";
        }
        ?>
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function() {
            _paq.push(['setAppId', '<?php echo esc_js($app_id); ?>']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async=true; g.src='https://edge.kepixel.com/anubis.js'; s.parentNode.insertBefore(g,s);
        })();
    </script>
    <!-- End kepixel Code -->
    <?php
}
add_action('wp_head', 'kepixel_add_tracking_code', 10);

/**
 * Add Kepixel's e-commerce tracking script (JS) to product pages
 */
function kepixel_add_ecommerce_tracking_to_product_pages()
{
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    if (is_product()) {
        global $product;

        $sku = $product->get_sku();
        $name = $product->get_name();
        $categories = wp_get_post_terms($product->get_id(), 'product_cat');
        $category_names = array_map(function ($term) {
            return $term->name;
        }, $categories);
        $category_list = implode(', ', $category_names);
        $price = $product->get_price();

        wp_enqueue_script('kepixel-product-tracking', plugins_url('/js/kepixel-product-tracking.js', __FILE__), array('jquery'), null, true);

        wp_localize_script('kepixel-product-tracking', 'wpKepixelProductData', array(
            'sku' => esc_js($sku),
            'name' => esc_js($name),
            'categoryList' => esc_js($category_list),
            'price' => esc_js($price),
        ));
    }
}
add_action('wp_head', 'kepixel_add_ecommerce_tracking_to_product_pages', 999);

/**
 * Add Kepixel's e-commerce tracking script (JS) to category pages
 */
function kepixel_add_ecommerce_tracking_to_category_pages()
{
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    if (is_product_category()) {
        $category = get_queried_object();
        $category_name = $category->name;

        wp_enqueue_script('kepixel-category-tracking', plugins_url('/js/kepixel-category-tracking.js', __FILE__), array('jquery'), null, true);

        wp_localize_script('kepixel-category-tracking', 'wpKepixelCategoryData', array(
            'categoryName' => esc_html($category_name),
        ));
    }
}
add_action('wp_head', 'kepixel_add_ecommerce_tracking_to_category_pages', 999);

/**
 * Add Kepixel's e-commerce tracking script (JS) to cart page
 */
function kepixel_add_tracking_to_cart_page()
{
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    if (is_cart()) {
        wp_enqueue_script('kepixel-cart-tracking', plugins_url('/js/kepixel-cart-tracking.js', __FILE__), array('jquery'), null, true);

        $items = array();
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            if (!is_a($_product, 'WC_Product')) continue;

            $_category = wp_get_post_terms($_product->get_id(), 'product_cat');
            $_category_name = !empty($_category) ? esc_html($_category[0]->name) : '';

            $items[] = array(
                'sku' => esc_attr($_product->get_sku()),
                'name' => esc_html($_product->get_name()),
                'category' => $_category_name,
                'price' => esc_attr($_product->get_price()),
                'quantity' => intval($cart_item['quantity']),
            );
        }

        wp_localize_script('kepixel-cart-tracking', 'wpKepixelCartData', array(
            'items' => $items,
            'cartTotal' => WC()->cart->total,
        ));
    }
}
add_action('wp_head', 'kepixel_add_tracking_to_cart_page', 999);

/**
 * Add Kepixel's e-commerce tracking script (JS) to order received page
 */
function kepixel_add_tracking_to_order_received_page($order_id)
{
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    $order = wc_get_order($order_id);

    if (!$order) {
        return;
    }

    wp_enqueue_script('kepixel-order-tracking', plugins_url('/js/kepixel-order-tracking.js', __FILE__), array('jquery'), null, true);

    $items_data = array();
    foreach ($order->get_items() as $item_id => $item) {
        $_product = $item->get_product();
        $_categories = wp_get_post_terms($_product->get_id(), 'product_cat');
        $category_name = !empty($_categories) ? esc_html($_categories[0]->name) : '';

        $items_data[] = array(
            'sku' => esc_html($_product->get_sku()),
            'name' => esc_html($item->get_name()),
            'category_name' => $category_name,
            'price' => esc_html($item->get_subtotal()),
            'quantity' => esc_html($item->get_quantity()),
        );
    }

    $order_data = array(
        'order_number' => esc_html($order->get_order_number()),
        'total' => esc_html($order->get_total()),
        'subtotal' => esc_html($order->get_subtotal()),
        'total_tax' => esc_html($order->get_total_tax()),
        'shipping_total' => esc_html($order->get_shipping_total()),
        'items' => $items_data,
    );

    wp_localize_script('kepixel-order-tracking', 'wpKepixelOrderData', $order_data);
}
add_action('woocommerce_thankyou', 'kepixel_add_tracking_to_order_received_page', 999);

/**
 * Add Kepixel's e-commerce tracking script (JS) to checkout page
 */
function kepixel_add_tracking_to_checkout_page()
{
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    if (is_checkout() && !is_order_received_page()) {
        wp_enqueue_script('kepixel-checkout-tracking', plugins_url('/js/kepixel-checkout-tracking.js', __FILE__), array('jquery'), null, true);

        $items = array();
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            if (!is_a($_product, 'WC_Product')) continue;

            $_category = wp_get_post_terms($_product->get_id(), 'product_cat');
            $_category_name = !empty($_category) ? esc_html($_category[0]->name) : '';

            $items[] = array(
                'sku' => esc_attr($_product->get_sku()),
                'name' => esc_html($_product->get_name()),
                'category' => $_category_name,
                'price' => esc_attr($_product->get_price()),
                'quantity' => intval($cart_item['quantity']),
            );
        }

        wp_localize_script('kepixel-checkout-tracking', 'wpKepixelCheckoutData', array(
            'items' => $items,
            'cartTotal' => WC()->cart->total,
        ));
    }
}
add_action('wp_head', 'kepixel_add_tracking_to_checkout_page', 999);

/**
 * Track user registration
 */
function kepixel_track_user_registration($user_id) {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    // Set a cookie to indicate that the user just registered
    // This will be used to load the registration tracking script on the next page load
    setcookie('kepixel_user_registered', '1', time() + 3600, '/');
}
add_action('user_register', 'kepixel_track_user_registration');

/**
 * Add Kepixel's registration tracking script (JS) if user just registered
 */
function kepixel_add_registration_tracking() {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    // Check if the user just registered
    if (isset($_COOKIE['kepixel_user_registered']) && $_COOKIE['kepixel_user_registered'] === '1') {
        wp_enqueue_script('kepixel-registration-tracking', plugins_url('/js/kepixel-registration-tracking.js', __FILE__), array('jquery'), null, true);

        // Clear the cookie
        setcookie('kepixel_user_registered', '', time() - 3600, '/');
    }
}
add_action('wp_head', 'kepixel_add_registration_tracking', 999);

/**
 * Add Kepixel's search tracking script (JS) to search results page
 */
function kepixel_add_tracking_to_search_page() {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    if (is_search()) {
        wp_enqueue_script('kepixel-search-tracking', plugins_url('/js/kepixel-search-tracking.js', __FILE__), array('jquery'), null, true);

        // Get the search query
        $search_query = get_search_query();

        wp_localize_script('kepixel-search-tracking', 'wpKepixelSearchData', array(
            'searchQuery' => esc_js($search_query),
        ));
    }
}
add_action('wp_head', 'kepixel_add_tracking_to_search_page', 999);

/**
 * Add Kepixel's page tracking script (JS) to all pages
 */
function kepixel_add_page_tracking() {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    wp_enqueue_script('kepixel-page-tracking', plugins_url('/js/kepixel-page-tracking.js', __FILE__), array('jquery'), null, true);

    // Get page data
    global $post;
    $page_id = is_singular() ? $post->ID : '';
    $page_name = is_singular() ? $post->post_title : '';

    // Determine page category and type
    $page_category = '';
    $page_type = '';

    if (is_front_page()) {
        $page_category = 'home';
        $page_type = 'home';
    } elseif (is_page()) {
        $page_category = 'page';
        $page_type = 'page';
    } elseif (is_single()) {
        $page_category = 'post';
        $page_type = 'post';

        // Get post categories
        $categories = get_the_category();
        if (!empty($categories)) {
            $page_category = $categories[0]->name;
        }
    } elseif (is_category()) {
        $page_category = 'category';
        $page_type = 'category';

        // Get category name
        $category = get_queried_object();
        $page_name = $category->name;
    } elseif (is_tag()) {
        $page_category = 'tag';
        $page_type = 'tag';

        // Get tag name
        $tag = get_queried_object();
        $page_name = $tag->name;
    } elseif (is_search()) {
        $page_category = 'search';
        $page_type = 'search';
        $page_name = 'Search Results';
    } elseif (is_archive()) {
        $page_category = 'archive';
        $page_type = 'archive';
    } elseif (is_404()) {
        $page_category = 'error';
        $page_type = '404';
        $page_name = 'Page Not Found';
    }

    wp_localize_script('kepixel-page-tracking', 'wpKepixelPageData', array(
        'pageId' => esc_js($page_id),
        'pageName' => esc_js($page_name),
        'pageCategory' => esc_js($page_category),
        'pageType' => esc_js($page_type),
    ));
}
add_action('wp_head', 'kepixel_add_page_tracking', 999);

/**
 * Add Kepixel's list tracking script (JS) to archive, category, and tag pages
 */
function kepixel_add_list_tracking() {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    if (is_archive() || is_category() || is_tag() || is_tax()) {
        wp_enqueue_script('kepixel-list-tracking', plugins_url('/js/kepixel-list-tracking.js', __FILE__), array('jquery'), null, true);

        // Get list data
        $list_id = '';
        $list_name = '';
        $list_category = '';
        $list_type = '';

        if (is_category()) {
            $category = get_queried_object();
            $list_id = 'category_' . $category->term_id;
            $list_name = $category->name;
            $list_category = 'category';
            $list_type = 'category';
        } elseif (is_tag()) {
            $tag = get_queried_object();
            $list_id = 'tag_' . $tag->term_id;
            $list_name = $tag->name;
            $list_category = 'tag';
            $list_type = 'tag';
        } elseif (is_tax()) {
            $term = get_queried_object();
            $list_id = 'tax_' . $term->term_id;
            $list_name = $term->name;
            $list_category = $term->taxonomy;
            $list_type = 'taxonomy';
        } elseif (is_author()) {
            $author = get_queried_object();
            $list_id = 'author_' . $author->ID;
            $list_name = $author->display_name;
            $list_category = 'author';
            $list_type = 'author';
        } elseif (is_date()) {
            if (is_day()) {
                $list_id = 'day_' . get_the_date('Y-m-d');
                $list_name = get_the_date();
                $list_category = 'day';
                $list_type = 'date';
            } elseif (is_month()) {
                $list_id = 'month_' . get_the_date('Y-m');
                $list_name = get_the_date('F Y');
                $list_category = 'month';
                $list_type = 'date';
            } elseif (is_year()) {
                $list_id = 'year_' . get_the_date('Y');
                $list_name = get_the_date('Y');
                $list_category = 'year';
                $list_type = 'date';
            }
        } else {
            $list_id = 'archive';
            $list_name = 'Archive';
            $list_category = 'archive';
            $list_type = 'archive';
        }

        wp_localize_script('kepixel-list-tracking', 'wpKepixelListData', array(
            'listId' => esc_js($list_id),
            'listName' => esc_js($list_name),
            'listCategory' => esc_js($list_category),
            'listType' => esc_js($list_type),
        ));
    }
}
add_action('wp_head', 'kepixel_add_list_tracking', 999);

/**
 * Add Kepixel's wishlist tracking script (JS) to all pages
 */
function kepixel_add_wishlist_tracking() {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    // Load the wishlist tracking script on all pages
    wp_enqueue_script('kepixel-wishlist-tracking', plugins_url('/js/kepixel-wishlist-tracking.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_head', 'kepixel_add_wishlist_tracking', 999);

/**
 * Add Kepixel's app tracking script (JS) to all pages
 */
function kepixel_add_app_tracking() {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    // Load the app tracking script on all pages
    wp_enqueue_script('kepixel-app-tracking', plugins_url('/js/kepixel-app-tracking.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_head', 'kepixel_add_app_tracking', 999);

/**
 * Add Kepixel's contact tracking script (JS) to all pages
 */
function kepixel_add_contact_tracking() {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    // Load the contact tracking script on all pages
    wp_enqueue_script('kepixel-contact-tracking', plugins_url('/js/kepixel-contact-tracking.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_head', 'kepixel_add_contact_tracking', 999);

/**
 * Track user login
 */
function kepixel_track_user_login($user_login, $user) {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    // Set a cookie to indicate that the user just logged in
    // This will be used to load the login tracking script on the next page load
    setcookie('kepixel_user_logged_in', '1', time() + 3600, '/');
}
add_action('wp_login', 'kepixel_track_user_login', 10, 2);

/**
 * Add Kepixel's login tracking script (JS) if user just logged in
 */
function kepixel_add_login_tracking() {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    // Check if the user just logged in
    if (isset($_COOKIE['kepixel_user_logged_in']) && $_COOKIE['kepixel_user_logged_in'] === '1') {
        wp_enqueue_script('kepixel-login-tracking', plugins_url('/js/kepixel-login-tracking.js', __FILE__), array('jquery'), null, true);

        // Clear the cookie
        setcookie('kepixel_user_logged_in', '', time() - 3600, '/');
    }
}
add_action('wp_head', 'kepixel_add_login_tracking', 999);

/**
 * Add Kepixel's custom tracking script (JS) to all pages
 */
function kepixel_add_custom_tracking() {
    $enable_tracking = get_option('kepixel_enable_tracking', true);

    // Only add tracking if tracking is enabled
    if (!$enable_tracking) {
        return;
    }

    // Load the custom tracking script on all pages
    wp_enqueue_script('kepixel-custom-tracking', plugins_url('/js/kepixel-custom-tracking.js', __FILE__), array('jquery'), null, true);
}
add_action('wp_head', 'kepixel_add_custom_tracking', 999);
