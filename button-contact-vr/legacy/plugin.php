<?php

/**
 * Legacy plugin code
 */

define('PZF_FILE', __FILE__);
define('PZF_NAME', basename(PZF_FILE));
define('PZF_BASE_NAME', plugin_basename(PZF_FILE));
define('PZF_PATH', plugin_dir_path(PZF_FILE));
define('PZF_URL', plugin_dir_url(PZF_FILE));

if (! defined('ABSPATH')) {
    exit;
}

function register_mysettings()
{
    // Group 1: Phone numbers and colors
    register_setting('pzf-settings-group', 'pzf_phone', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_phone2', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_phone3', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_color_phone', [
        'sanitize_callback' => 'sanitize_hex_color_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_color_phone2', [
        'sanitize_callback' => 'sanitize_hex_color_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_color_phone3', [
        'sanitize_callback' => 'sanitize_hex_color_with_limit'
    ]);

    // Group 2: Social media links
    register_setting('pzf-settings-group', 'pzf_linkfanpage', [
        'sanitize_callback' => 'esc_url_raw'
    ]);
    register_setting('pzf-settings-group', 'pzf_linkmessenger', [
        'sanitize_callback' => 'esc_url_raw'
    ]);
    register_setting('pzf-settings-group', 'pzf_whatsapp', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_zalo', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_telegram', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_instagram', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_youtube', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_tiktok', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_viber', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);

    // Group 3: Contact link and colors
    register_setting('pzf-settings-group', 'pzf_contact_link', [
        'sanitize_callback' => 'esc_url_raw'
    ]);
    register_setting('pzf-settings-group', 'pzf_color_contact', [
        'sanitize_callback' => 'sanitize_hex_color_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_linkggmap', [
        'sanitize_callback' => 'esc_url_raw'
    ]);
    register_setting('pzf-settings-group', 'pzf_color_linkggmap', [
        'sanitize_callback' => 'sanitize_hex_color_with_limit'
    ]);

    // Group 4: Facebook integration
    register_setting('pzf-settings-group', 'pzf_id_fanpage', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'pzf_color_fb', [
        'sanitize_callback' => 'sanitize_hex_color_with_limit'
    ]);

    // Group 5: Other settings
    register_setting('pzf-settings-group', 'pzf_phone_bar', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group', 'logged_in_greeting', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);


    register_setting('pzf-settings-group-setting', 'setting_size', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group-setting', 'pzf_location', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group-setting', 'pzf_location_bottom', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group-setting', 'pzf_hide_mobile', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group-setting', 'pzf_hide_desktop', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf-settings-group-setting', 'pzf_off_effects', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);


    register_setting('pzf_settings_all_in_one', 'pzf_enable_all_in_one', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf_settings_all_in_one', 'pzf_note_all_in_one', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf_settings_all_in_one', 'pzf_note_bar_all_in_one', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf_settings_all_in_one', 'pzf_color_all_in_one', [
        'sanitize_callback' => 'sanitize_hex_color_with_limit'
    ]);
    register_setting('pzf_settings_all_in_one', 'pzf_icon_all_in_one', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf_settings_all_in_one', 'pzf_hide_default_all_in_one', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);


    register_setting('pzf_settings_contact_form', 'pzf_enable_contact_form', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
    register_setting('pzf_settings_contact_form', 'pzf_color_contact_form', [
        'sanitize_callback' => 'sanitize_hex_color_with_limit'
    ]);
    register_setting('pzf_settings_contact_form', 'pzf_bg_contact_form', [
        'sanitize_callback' => 'sanitize_hex_color_with_limit'
    ]);
    register_setting('pzf_settings_contact_form', 'pzf_max_w_contact_form', [
        'sanitize_callback' => 'sanitize_text_field_with_limit'
    ]);
}

// Sanitize and limit the length of a text field
function sanitize_text_field_with_limit($value)
{
    $value = wp_strip_all_tags($value); // Remove HTML tags
    return substr($value, 0, 255); // Limit to 255 characters
}

// Sanitize and limit the length of a hex color field
function sanitize_hex_color_with_limit($value)
{
    $value = sanitize_hex_color($value); // Sanitize hex color
    return substr($value, 0, 7); // Limit to 7 characters
}


require_once PZF_PATH . '/inc/button-contact.php';

// add menu admin wp
function pzf_create_menu()
{
    add_menu_page('Button contact VR', 'Button contact', 'administrator', 'contact_vr', 'pzf_settings_page', plugins_url('/img/icon.png', __FILE__), 100);
    add_action('admin_init', 'register_mysettings');

    // add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )
    add_submenu_page('contact_vr', 'Contact form', 'Contact form', 'administrator', 'contact_vr_contact_form', 'pzf_settings_contact_form', 10);
    add_submenu_page('contact_vr', 'Showroom', 'Showroom', 'administrator', 'contact_vr_showroom', 'pzf_settings_showroom', 20);
    add_submenu_page('contact_vr', 'All in one', 'All in one', 'administrator', 'contact_vr_all_in_one', 'pzf_settings_all_in_one', 30);
    add_submenu_page('contact_vr', 'Setting', 'Setting', 'administrator', 'contact_vr_setting', 'pzf_settings_page_setting', 40);
}
add_action('admin_menu', 'pzf_create_menu');

// Try new version
function try_new_version()
{
    // Go to new version
    if (
        // Check correct permissions
        current_user_can(is_multisite() ? 'manage_options' : 'activate_plugins') &&

        // Validate correct page
        (isset($_GET["page"]) && $_GET["page"] === "contact_vr_setting") &&

        // Check try new version variable exists
        (isset($_GET["try_new_version"]) && $_GET["try_new_version"] === "yes")
    ) {
        update_option("button_contact_legacy", "no");

        // Redirect succesful
        wp_redirect(admin_url("admin.php?page=bz_button_contact"));
        exit;
    }
}

add_action('admin_init', 'try_new_version');


// add link setting -  add setting: 4.2
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links');
function add_action_links($actions)
{
    $mylinks = array(
        '<a href="' . admin_url('admin.php?page=contact_vr') . '">' . esc_html__('Settings', 'button-contact-vr') . '</a>',
    );
    $actions = array_merge($actions, $mylinks);
    return $actions;
}
// Register and enqueue custom admin CSS
function custom_admin_css()
{
    wp_register_style('custom-admin', plugins_url('css/style-admin.css', __FILE__), [], 1);
    wp_enqueue_style('custom-admin');
}
add_action('admin_enqueue_scripts', 'custom_admin_css');

// add backend
function pzf_settings_page()
{
    include PZF_PATH . '/inc/admin.php';
}
function pzf_settings_page_setting()
{
    include PZF_PATH . '/inc/setting.php';
}
function pzf_settings_all_in_one()
{
    include PZF_PATH . '/inc/all-in-one.php';
}
function pzf_settings_contact_form()
{
    include PZF_PATH . '/inc/contact-form.php';
}
function pzf_settings_showroom()
{
    include PZF_PATH . '/inc/showroom.php';
}
// end backend
PZF::instance();
