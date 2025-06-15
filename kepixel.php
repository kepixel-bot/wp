<?php
defined('ABSPATH') || die();

/**
 * Plugin Name: Kepixel
 * Description: Kepixel is an analytics, statistics plugin for WordPress and eCommerce tracking with WooCommerce. Optimize your sales with powerful analytics!
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: kepixel-bot
 * Author URI: https://www.kepixel.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kepixel
 * Domain Path: /languages
 */

/**
 * Load translations
 */
function kepixel_load_textdomain()
{
    load_plugin_textdomain('kepixel', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'kepixel_load_textdomain');

/**
 * Add settings link to plugins page
 */
function kepixel_add_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=kepixel">' . __('Settings', 'kepixel') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'kepixel_add_settings_link');

/**
 * Register settings page
 */
function kepixel_add_settings_page()
{
    add_options_page(
        __('Kepixel Settings', 'kepixel'),
        __('Kepixel', 'kepixel'),
        'manage_options',
        'kepixel',
        'kepixel_render_settings_page'
    );
}
add_action('admin_menu', 'kepixel_add_settings_page');

/**
 * Render settings page
 */
function kepixel_render_settings_page()
{
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('kepixel_options');
            do_settings_sections('kepixel');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register settings
 */
function kepixel_register_settings()
{
    register_setting('kepixel_options', 'kepixel_app_id', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ));

    register_setting('kepixel_options', 'kepixel_enable_tracking', array(
        'type' => 'boolean',
        'sanitize_callback' => 'rest_sanitize_boolean',
        'default' => true
    ));

    add_settings_section(
        'kepixel_settings_section',
        __('Kepixel Settings', 'kepixel'),
        'kepixel_settings_section_callback',
        'kepixel'
    );

    add_settings_field(
        'kepixel_app_id',
        __('App ID', 'kepixel'),
        'kepixel_app_id_callback',
        'kepixel',
        'kepixel_settings_section'
    );

    add_settings_field(
        'kepixel_enable_tracking',
        __('Enable Tracking', 'kepixel'),
        'kepixel_enable_tracking_callback',
        'kepixel',
        'kepixel_settings_section'
    );
}
add_action('admin_init', 'kepixel_register_settings');

/**
 * Settings section callback
 */
function kepixel_settings_section_callback()
{
    echo '<p>' . __('Enter your Kepixel App ID below. You can find this in your Kepixel account.', 'kepixel') . '</p>';
}

/**
 * App ID field callback
 */
function kepixel_app_id_callback()
{
    $app_id = get_option('kepixel_app_id');
    echo '<input type="text" id="kepixel_app_id" name="kepixel_app_id" value="' . esc_attr($app_id) . '" class="regular-text">';
}

/**
 * Enable Tracking field callback
 */
function kepixel_enable_tracking_callback()
{
    $enable_tracking = get_option('kepixel_enable_tracking', true);
    echo '<input type="checkbox" id="kepixel_enable_tracking" name="kepixel_enable_tracking" value="1" ' . checked(1, $enable_tracking, false) . '>';
    echo '<label for="kepixel_enable_tracking">' . __('Enable tracking on this site', 'kepixel') . '</label>';
}

/**
 * Check if WooCommerce is installed and activated
 */
function kepixel_is_woocommerce_installed()
{
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    return is_plugin_active('woocommerce/woocommerce.php');
}

/**
 * Display an admin notice if Kepixel App ID is not set
 */
function kepixel_app_id_not_set()
{
    // Only show this notice in the admin area
    if (!is_admin()) {
        return;
    }

    // Get the current screen
    $screen = get_current_screen();
    // Skip on some screens to avoid too many notices
    if ($screen && in_array($screen->id, ['plugins', 'update-core'])) {
        return;
    }

    $app_id = get_option('kepixel_app_id');
    if (empty($app_id)) {
        echo '<div class="notice notice-warning">';
        echo '<p>';
        echo 'Kepixel ';
        esc_html_e('requires an App ID to function properly. Please', 'kepixel');
        echo ' <a href="' . admin_url('options-general.php?page=kepixel') . '">';
        esc_html_e('set your App ID', 'kepixel');
        echo '</a> ';
        esc_html_e('to enable tracking.', 'kepixel');
        echo '</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'kepixel_app_id_not_set');

if (kepixel_is_woocommerce_installed()) {
    require_once plugin_dir_path(__FILE__) . 'includes/class-kepixel.php'; // Include Kepixel class
} else {
    /**
     * Display an admin notice if WooCommerce is not installed or activated
     */
    function kepixel_woocommerce_not_detected() {
        $user_id = get_current_user_id();

        // Skip if user has dismissed the notice
        if (get_user_meta($user_id, 'kepixel_woo_notice_dismissed', true)) {
            return;
        }

        ?>
        <div class="notice notice-info is-dismissible kepixel-notice">
            <p>
                <?php
                echo 'Kepixel ';
                esc_html_e('can work with', 'kepixel');
                echo ' <a href="https://woocommerce.com/" target="_blank">WooCommerce</a>.';
                ?>
            </p>
        </div>
        <?php
    }
    add_action('admin_notices', 'kepixel_woocommerce_not_detected');

    /**
     * Output JS to capture dismissal and send AJAX
     */
    function kepixel_notice_dismiss_script() {
        ?>
        <script>
            jQuery(document).ready(function($) {
                $('.kepixel-notice').on('click', '.notice-dismiss', function() {
                    $.post(ajaxurl, {
                        action: 'kepixel_dismiss_notice',
                        nonce: '<?php echo wp_create_nonce("kepixel_dismiss_nonce"); ?>'
                    });
                });
            });
        </script>
        <?php
    }
    add_action('admin_footer', 'kepixel_notice_dismiss_script');

    /**
     * AJAX handler to save dismissal state
     */
    function kepixel_dismiss_notice_callback() {
        check_ajax_referer('kepixel_dismiss_nonce', 'nonce');

        $user_id = get_current_user_id();
        update_user_meta($user_id, 'kepixel_woo_notice_dismissed', 1);

        wp_send_json_success();
    }
    add_action('wp_ajax_kepixel_dismiss_notice', 'kepixel_dismiss_notice_callback');
}

