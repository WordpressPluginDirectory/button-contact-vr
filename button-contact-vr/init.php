<?php
/*
 * SOFTWARE LICENSE INFORMATION
 *
 * Copyright (c) 2017 Buttonizer, all rights reserved.
 *
 * This file is part of Buttonizer
 *
 * For detailed information regarding to the licensing of
 * this software, please review the license.txt or visit:
 * https://buttonizer.pro/license/
 */

use BZContactButton\Admin\Admin;
use BZContactButton\Utils\Settings;
use BZContactButton\Api\Api;
use BZContactButton\Utils\PermissionCheck;

/**
 * Get current languages
 */
function bcbGetCurrentLanguage()
{
    // Polylang
    if (function_exists("pll_current_language")) {
        return pll_current_language("slug");
    }

    // Weglot
    if (function_exists("weglot_get_current_language")) {
        return weglot_get_current_language();
    }

    // WMPL
    $currentLanguage = apply_filters('wpml_current_language', NULL);

    // Try to fall back on current language
    if (!$currentLanguage) return substr(get_bloginfo('language'), 0, 2);

    return $currentLanguage;
}

/**
 * Custom language
 *
 * Automatically redirects to the page in current language
 */
function bz_button_contact_redirect_to_page()
{
    // Validate params
    if (!isset($_GET['page_id']) || !is_numeric($_GET['page_id']) || !isset($_GET['is_bz_button_contact_redirect'])) {
        return;
    }

    $id = $_GET['page_id'];
    $page = null;

    // Polylang
    if (function_exists("pll_get_post")) {
        $page = pll_get_post($id);
    }

    // Check WPML translated page
    if (!$page && $wpmlObject = apply_filters('wpml_object_id', $id)) {
        $page = $wpmlObject;
    }

    // Redirect if post or page was found
    if ($pageUrl = get_the_permalink($page ?? $id)) {
        // Check if the page was redirected
        if (!wp_redirect($pageUrl, 302, 'Buttonizer')) {
            // Make sure to receive a safe redirect URL
            $redirectUrl = wp_validate_redirect(wp_sanitize_redirect($pageUrl), false);

            // Only redirect if it's a safe and allowed host
            if ($redirectUrl) {
                header("Location: " . $redirectUrl, true, 302);
            }

            exit("A redirect was cancelled.");
        }
        exit;
    }
}

/*
 * Contact Button Admin Dashboard
 */
if (is_admin()) {
    // Load Admin page
    new Admin();
}

/**
 * Redirect to page in correct language
 */
add_action('template_redirect', 'bz_button_contact_redirect_to_page', 0);

/**
 * Add Buttonizer scripts
 */
// Add page data
add_action('wp_head', function () {
    if (Settings::getSetting("site_id")) {
        // Get current page language
        $pageData = [
            "language" => bcbGetCurrentLanguage()
        ];

        // Add Buttonize page data
        if (Settings::getSetting("include_page_data", false)) {
            // Get page categories
            $pageCategories = array_map(function ($category) {
                return $category->cat_ID;
            }, get_the_category());

            // Collect page data
            $pageData = array_merge([
                "page_id" => get_the_ID(),
                "categories" => $pageCategories,
                "is_frontpage" => is_front_page(),
                "is_404" => is_404(),
                "user_roles" => PermissionCheck::getUserRoles()
            ], $pageData);
        }

        // Define page data
        $buttonizerData = "if(!window._buttonizer) { window._buttonizer = {}; };var _buttonizer_page_data = " . json_encode($pageData) . ";window._buttonizer.data = { ..._buttonizer_page_data, ...window._buttonizer.data };";

        echo '<script type="text/javascript">' . $buttonizerData . '</script>';
    }
}, 10);

// Add integration script
add_action('wp_footer', function () {
    if (Settings::getSetting("site_id")) {
        // Buttonizer integration script
        $buttonizerSnippet = "(function(n,t,c,d){if(t.getElementById(d)){return}var o=t.createElement('script');o.id=d;(o.async=!0),(o.src='https://cdn.buttonizer.io/embed.js'),(o.onload=function(){window.Buttonizer?window.Buttonizer.init(c):window.addEventListener('buttonizer_script_loaded',()=>window.Buttonizer.init(c))}),t.head.appendChild(o)})(window,document,'" . Settings::getSetting("site_id") . "','buttonizer_script')";

        // GDPR Compliance check
        if (Settings::getSetting("wait_until_consent", false)) {
            $buttonizerSnippet = "// Buttonizer snippet container
function enableButtonizer() {" . $buttonizerSnippet . "};

// Buttonizer consent given, load content
if(window.buttonizer_consent_given){ enableButtonizer(); }";
        }

        echo '<script type="text/javascript">' . $buttonizerSnippet . '</script>';
    }
}, 11);

// Validator only available after WP 4.9
function BcbIsValidUUID($uuid)
{
    $regex = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

    return (bool) preg_match($regex, $uuid);
}

// Buttonizer widget shortcode
function bcbWidgetShortcode($atts)
{
    // Get attributes
    $atts = shortcode_atts(
        array(
            'id' => '',
        ),
        $atts
    );

    // Make sure the ID exists and is a valid UUID
    if (!isset($atts['id']) || !is_string($atts['id']) || !BcbIsValidUUID($atts['id'])) return "";

    return '<div class="buttonizer-inline-widget" data-buttonizer-widget-id="' . $atts['id'] . '"></div>';
};

function bcbInitFunction()
{
    if (!shortcode_exists("buttonizer")) {
        add_shortcode('buttonizer', 'bcbWidgetShortcode');
    }
}

add_action('init', 'bcbInitFunction');

// Add admin menu
add_action('admin_bar_menu', function ($bar) {
    Admin::wordpressAdminBar($bar);
}, 100);

/**
 * Initialize Buttonizer API endpoints
 */
add_action('rest_api_init', function () {
    new Api();
});
