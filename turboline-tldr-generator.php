<?php
/**
 * Plugin Name: Turboline TLDR Generator
 * Description: Easily generate TLDR summaries of your blog posts using advanced AI—boost readability, engagement, and SEO.
 * Version: 1.0
 * Author: Turboline
 * Author URI: https://turboline.ai/
 */

defined('ABSPATH') || exit;

// Plugin constants
define('TLDR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TLDR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TLDR_META_KEY', 'turboline_tldr_meta_field');

// Include class files
require_once TLDR_PLUGIN_DIR . 'inc/class.tldr.php';        // TLDR_API
require_once TLDR_PLUGIN_DIR . 'inc/class.admin.tldr.php';  // Admin settings

// Init main plugin class
new TLDR_Plugin();

// Add settings link to plugin list
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'tldr_add_settings_link');
function tldr_add_settings_link($links)
{
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=tldr-settings')) . '">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script(
        'turboline-tldr-js-admin',
        TLDR_PLUGIN_URL . 'assets/js/turboline-tldr.js',
        ['jquery', 'wp-color-picker'],
        '1.0',
        true
    );
});

add_action('wp_enqueue_scripts', 'turboline_enqueue_assets');
function turboline_enqueue_assets()
{
    if (!is_singular()) {
        return;
    }

    wp_enqueue_style(
        'turboline-tldr-css',
        TLDR_PLUGIN_URL . '/assets/css/turboline-tldr.css', // Path to CSS file
        array(),
        '1.0.0',
        'all'
    );

    wp_enqueue_style('wp-color-picker');

    wp_enqueue_script(
        'turboline-tldr-js',
        TLDR_PLUGIN_URL . 'assets/js/turboline-tldr.js',
        ['jquery'],
        '1.0',
        true
    );

    wp_localize_script('turboline-tldr-js', 'TurbolineAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('turboline_nonce'),
    ]);
}
// Register shortcode
add_shortcode('turboline_tldr', 'turboline_tldr_shortcode');

/**
 * Shortcode callback: outputs TLDR excerpt with optional regenerate button.
 */
function turboline_tldr_shortcode()
{
    if (!is_singular()) {
        return '';
    }

    $post_id = get_the_ID();
    $excerpt = get_post_meta($post_id, TLDR_META_KEY, true);
    $border_color = get_option('tldr_border_color') ?? "#000";
    // Generate excerpt if missing
    if (empty($excerpt)) {
        $content = get_post_field('post_content', $post_id);
        $excerpt = TLDR_API::generate_excerpt($content);
        update_post_meta($post_id, TLDR_META_KEY, $excerpt);
    }

    ob_start();
    ?>
    <div class="turboline-tldr-wrap" style="border-color:<?php echo $border_color ?>">
    <div class="icon-box">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M12 8V9M12 11.5V16M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#6CBC6E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

    </div>
    <div>
    <h3>tl;dr</h3>
        <div class="turboline-tldr-inner"><?php echo esc_html($excerpt); ?></div>
        <?php if (current_user_can('manage_options')): ?>
            <button class="regenerate-tldr-btn" data-post-id="<?php echo esc_attr($post_id); ?>">
                <i class="fa-solid fa-rotate"></i>
            </button>
        <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_action('wp_ajax_turboline_regenerate_tldr', 'turboline_handle_ajax_regeneration');

function turboline_handle_ajax_regeneration()
{
    check_ajax_referer('turboline_nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Unauthorized');
    }

    $post_id = intval($_POST['post_id']);
    $content = get_post_field('post_content', $post_id);

    if (!$content) {
        wp_send_json_error('Invalid post content');
    }

    $excerpt = TLDR_API::generate_excerpt($content);
    update_post_meta($post_id, TLDR_META_KEY, $excerpt);

    wp_send_json_success(['excerpt' => $excerpt]);
}


add_action('save_post', 'turboline_auto_regenerate_on_save', 20, 2);
function turboline_auto_regenerate_on_save($post_id, $post)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (strpos($post->post_content, '[turboline_tldr]') === false) {
        return;
    }

    $excerpt = TLDR_API::generate_excerpt($post->post_content);
    update_post_meta($post_id, TLDR_META_KEY, $excerpt);
}