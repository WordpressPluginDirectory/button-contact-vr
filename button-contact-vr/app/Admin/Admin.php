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

namespace BZContactButton\Admin;

use BZContactButton\Api\Settings\MigrateToStandalone;
use BZContactButton\Utils\Account;
use BZContactButton\Utils\ManifestParser;
use BZContactButton\Utils\Editor;
use BZContactButton\Utils\PermissionCheck;
use BZContactButton\Utils\Settings;

# No script kiddies
defined('ABSPATH') or die('No script kiddies please!');

class Admin
{
    private static $adminStyles = ["dashicons", "common", "admin-menu", "dashboard", "nav-menus", "site-icon", "l10n"];

    /**
     * Admin constructor.
     */
    public function __construct()
    {
        // Add to admin menu
        add_action('admin_menu', [$this, 'pluginAdminMenu']);

        // Lets do some admin stuff for Buttonizer
        add_action('admin_enqueue_scripts', [$this, 'adminAssets']);

        // Enable modules
        add_filter('script_loader_tag', [$this, 'addModuleToScriptTag'], 10, 3);

        // Plugin information, add links
        add_filter("plugin_action_links_" . BZ_CONTACT_BUTTON_BASE_NAME, function ($actions) {
            $links = [
                '<a href="' . admin_url('admin.php?page=bz_button_contact#/support') . '">' . __('Support', 'button-contact-vr') . '</a>',
                '<a href="' . admin_url('admin.php?page=bz_button_contact#/editor') . '">' . __('Edit buttons', 'button-contact-vr') . '</a>',
                '<a href="' . admin_url('admin.php?page=bz_button_contact#/settings') . '">' . __('Settings', 'button-contact-vr') . '</a>',
            ];

            return array_merge($actions, $links);
        });

        // Redirect old plugin to updated version
        add_action('admin_init', function () {
            if ((isset($_GET["page"]) && $_GET["page"] === "contact_vr_setting")) {
                wp_redirect(admin_url("admin.php?page=bz_button_contact"));
            }
        });


        if (!defined('DISABLE_NAG_NOTICES') || (defined('DISABLE_NAG_NOTICES') && !DISABLE_NAG_NOTICES)) {
            add_action('admin_notices', [$this, 'generateAdminNotice']);
        }
    }

    /**
     * Create Admin menu
     */
    public function pluginAdminMenu()
    {

        if (!PermissionCheck::hasPermission()) return;

        // Admin menu
        add_menu_page('Contact Button', 'Contact Button', 'read', 'bz_button_contact', [$this, 'page'], plugins_url('/assets/images/wp-icon.png', BZ_CONTACT_BUTTON_PLUGIN_DIR), 81);

        // Add submenu
        add_submenu_page('bz_button_contact', 'Edit buttons',  __('Edit buttons', 'button-contact-vr'), 'read', 'admin.php?page=bz_button_contact#/editor');

        // Add support link
        add_submenu_page('bz_button_contact', __('I need support', 'button-contact-vr'),  __('I need support', 'button-contact-vr'), 'read', 'admin.php?page=bz_button_contact#/support');

        // Add community link
        add_submenu_page('bz_button_contact', __('Community', 'button-contact-vr'),  __('Community', 'button-contact-vr'), 'read', 'https://r.buttonizer.io/support/community?referral=wp-contact-button-plugin-menu');

        // Add knowledge base link
        add_submenu_page('bz_button_contact', __('Knowledge base', 'button-contact-vr'),  __('Knowledge base', 'button-contact-vr'), 'read', 'https://r.buttonizer.io/support/knowledgebase?referral=wp-contact-button-plugin-menu');

        // Add old plugin redirect page
        add_submenu_page('', "Setting redirect",  "Setting redirect", 'read', 'contact_vr_setting', [$this, 'plugin_update_redirect_placeholder']);
    }

    public function plugin_update_redirect_placeholder()
    {
        wp_redirect(admin_url("admin.php?page=bz_button_contact"));
    }

    public function adminAssets()
    {
        // Only add our assets to our own admin
        if (!isset($_GET['page']) || $_GET['page'] !== "bz_button_contact") return;

        // Require media manager
        wp_enqueue_media();

        // Get latest files
        $manifest = new ManifestParser(BZ_CONTACT_BUTTON_DIR . "/assets/app/manifest.json", plugins_url('assets/app', BZ_CONTACT_BUTTON_PLUGIN_FILE));

        // Get dashboard scripts
        $script = $manifest->getEntrypoint("index.html", false);

        // Get dashboard style
        $styles = $manifest->getStyles("index.html", false);

        // Get imports
        $imports = $manifest->getImports("index.html", false);

        // Add script
        wp_register_script('buttonizer_admin_js', $script['url'], [], md5(BZ_CONTACT_BUTTON_VERSION), true);

        // From script
        wp_deregister_style('forms');

        // Current user
        $current_user = wp_get_current_user();
        $current_user = $current_user->data;

        // Localize script
        wp_localize_script('buttonizer_admin_js', 'buttonizer_admin', [
            'admin' => admin_url('admin.php'),
            'isAdmin' => \is_user_logged_in() && current_user_can(is_multisite() ? 'manage_options' : 'activate_plugins'),
            'baseUrl' => get_site_url('/'),
            'adminBase' => substr(admin_url(), 0, -1),
            'assetsPath' => plugins_url('/assets', BZ_CONTACT_BUTTON_PLUGIN_DIR),
            'api' => get_rest_url(),
            'nonce' => wp_create_nonce('wp_rest'),
            'isPlain' => get_option('permalink_structure') === "",
            'version' => BZ_CONTACT_BUTTON_VERSION,
            'locale' => Editor::getEditorLanguage(),
            'actionLock' => $this->getActionLock(),
            'requestReview' => $this->requestForReview(),
            'displayCachingPluginBanner' => $this->cachingPluginDetected(),
            'beforeMigrate' => $this->getBeforeMigrate(),
            'hasMigrated' => Settings::getSetting("has_migrated", false),
            'hasLicense' => Account::getSetting("site_licensed", false),
            'account' => Account::getData(),
            'security' => wp_create_nonce("save_buttonizer"),
            'plugin_slug' => BZ_CONTACT_BUTTON_NAME,
            'settings' => [
                'adminTopBarButtonEnabled' => Settings::getSetting("admin_top_bar_show_button", true),
                'canSendErrors' => Settings::getSetting("can_send_errors", false),
                'accessRoles' => Settings::getSetting("additional_permissions", []),
                'googleAnalytics' => Settings::getSetting("google_analytics", null),
                'waitUntilConsent' => Settings::getSetting("wait_until_consent", false)
            ],
            'available_roles' => $this->getRoles(),
            'site' => [
                'domain' => wp_parse_url(get_site_url(), PHP_URL_HOST),
                'name' => get_bloginfo('name'),
                'user' => [
                    "email" => $current_user->user_email,
                    'firstName' => $current_user->first_name ?? $current_user->display_name ?? $current_user->user_nicename ?? "",
                    'lastName' => $current_user->last_name ?? ""
                ]
            ]
        ]);

        wp_enqueue_script('buttonizer_admin_js');

        // Register all script imports
        foreach ($imports as $key => $importSscript) {
            wp_register_script('buttonizer_admin_js_' . $key, $importSscript['url'], ["buttonizer_admin_js"], md5(BZ_CONTACT_BUTTON_VERSION), true);
            wp_enqueue_script('buttonizer_admin_js_' . $key);
        }

        // Register all styles
        foreach ($styles as $key => $style) {
            wp_register_style('buttonizer_admin_css_' . $key, $style['url'], self::$adminStyles, md5(BZ_CONTACT_BUTTON_VERSION));
            wp_enqueue_style('buttonizer_admin_css_' . $key);
        }
    }

    /**
     * @param string $tag
     * @param string $handle
     * @param string $src
     */
    public function addModuleToScriptTag($tag, $handle, $src)
    {
        // Add module to script tag
        if (strpos($handle, 'buttonizer_admin_js') === 0) {
            $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
        }

        return $tag;
    }


    public function page()
    {
        require_once __DIR__ . "/AdminTemplate.php";
    }

    /**
     * Lock the screen to a specific action
     *
     * @return string lock
     */
    public function getActionLock(): string
    {
        // Set up Buttonizer
        if (Settings::getSetting("finished_setup", false) === false) {
            return "setup";
        }

        return "no-lock";
    }

    /**
     * Migration status
     *
     * @return string lock
     */
    public function getBeforeMigrate()
    {
        return null;
    }

    public static function wordpressAdminBar($admin_bar)
    {
        // Only show to admins and when enabled
        if (
            // Check permission
            !PermissionCheck::hasPermission() ||

            // Admin bar disabled
            filter_var(Settings::getSetting('admin_top_bar_show_button', true), FILTER_VALIDATE_BOOLEAN, ['options' => ['default' => false]]) === false
        ) {
            return;
        }

        $admin_bar->add_menu(array(
            'id' => 'bz_button_contact',
            'title' => '<img src="' . plugins_url('/assets/images/wp-icon.png', BZ_CONTACT_BUTTON_PLUGIN_DIR) . '" style="vertical-align: text-bottom; opacity: 0.7; display: inline-block;" />',
            'href' => admin_url() . 'admin.php?page=bz_button_contact#/', // (!is_admin() ? '#' . urlencode($_SERVER["REQUEST_URI"]) : '')
            'meta' => [],
        ));

        // Buttonizer buttons
        $admin_bar->add_menu(array(
            'id' => 'bz_button_contact_buttons',
            'parent' => 'bz_button_contact',
            'title' => __('Edit buttons', 'button-contact-vr'),
            'href' => admin_url() . 'admin.php?page=bz_button_contact#/editor', // (!is_admin() ? '#' . urlencode($_SERVER["REQUEST_URI"]) : '')
            'meta' => array(),
        ));

        // Settings
        $admin_bar->add_menu(array(
            'id' => 'bz_button_contact_settings',
            'parent' => 'bz_button_contact',
            'title' => __('Settings', 'button-contact-vr'),
            'href' => admin_url() . 'admin.php?page=bz_button_contact#/settings',
            'meta' => array(),
        ));

        // Add support link
        $admin_bar->add_menu(array(
            'id' => 'bz_button_contact_support',
            'parent' => 'bz_button_contact',
            'title' => __('I need support', 'button-contact-vr'),
            'href' => admin_url() . 'admin.php?page=bz_button_contact#/support',
            'meta' => array(),
        ));

        // Settings
        $admin_bar->add_menu(array(
            'id' => 'bz_button_contact_knowledgebase',
            'parent' => 'bz_button_contact',
            'title' => __('Knowledge base', 'button-contact-vr'),
            'href' => "https://r.buttonizer.io/support/knowledgebase",
            'meta' => [
                "target" => "_blank",
                "title" => __('Find out everything you need to know about Buttonizer', 'button-contact-vr')
            ],
        ));
    }

    /**
     * Get roles for Buttonizer permission setting
     */
    private function getRoles()
    {
        $roles = [];

        foreach (wp_roles()->get_names() as $id => $role) {
            $roles[] = [
                'id'    => $id,
                'name' => $role
            ];
        }

        return $roles;
    }

    /**
     * Decide if we want to ask this user for a review
     *
     * We have so many ideas to work on...
     *
     * Hope you'd like to support us, but we don't want to bother you too much either :)
     */
    public function requestForReview()
    {
        try {
            // We already have asked for a review and they have clicked
            // We're happy humans, so we won't ask again
            if (Settings::getSetting("review_marked_as_done", false) === true) {
                return false;
            }

            // Get current time
            $currentTime = new \DateTime();

            // User requested to remind them later
            if (Settings::getSetting("review_reminding_since", null) !== null) {
                $remindFrom = Settings::getSetting("review_reminding_since", $currentTime);

                // Get the difference between today and the installed at
                $difference = ($currentTime)->diff($remindFrom);

                // Don't show yet, borrow them some time
                if ($difference->days <= 31) {
                    return false;
                }
            }

            /**
             * @var DateTime
             */
            $installDate = Settings::getSetting("installed_at", $currentTime);

            // Get the difference between today and the installed at
            $difference = ($currentTime)->diff($installDate);

            // Show after 9 days
            return $difference->days >= 9;
        } catch (\Error $e) {
            return false;
        }
    }

    /**
     * Some times users have a caching plugin installed
     * In some situations, they'll need to clear the cache
     *
     * This prevents issues like:
     *  - Don't seeing Buttonizer in general
     *  - Only seeing Buttonizer when signed in as admin
     */
    public function cachingPluginDetected()
    {
        try {
            // Did the user already dismissed the banner?
            if (Settings::getSetting("dismissed_caching_plugin_banner", false) === true) {
                return false;
            }

            // Get current activated plugins
            $pluginList = array_map('dirname', get_option('active_plugins'));

            // Any of the plugins below
            if (
                in_array('litespeed-cache', $pluginList) ||
                in_array('w3-total-cache', $pluginList) ||
                in_array('wp-super-cache', $pluginList) ||
                in_array('wp-fastest-cache', $pluginList) ||
                in_array('wp-optimize', $pluginList)
            ) {
                return true;
            }

            return false;
        } catch (\Error $e) {
            return false;
        }
    }

    /**
     * Request a review and dismiss message logic
     */
    public function generateAdminNotice()
    {
        // Do not show review request
        // Make sure only to show the request to users where Buttonizer is visible to
        if (!PermissionCheck::hasPermission() || !$this->requestForReview()) return;

        // Get current screen
        $currentScreen = get_current_screen();

        // Only show on other WordPress pages
        // DO NOT show this message on other plugin their pages
        // We're friendly people and don't want to disturb the enduser that much (like some other plugins do)
        $adminPages = ["dashboard", "plugins", "plugin-install", "themes", "edit-page", "edit-post", "options-general"];

        // Verify if we can show our review message here
        if (!$currentScreen || ($currentScreen && !in_array($currentScreen->id, $adminPages))) return;

        // nonce and endpoint to pass to frontend
        $nonce = wp_create_nonce("wp_rest");
        $endPoint = get_rest_url() . 'bz_contact_button/settings?nonce=' . $nonce;

        // if permalink is plain, change the structure
        if (get_option('permalink_structure') === "") {
            $endPoint = substr(get_rest_url(), 0, -1) . urlencode('/bz_contact_button/settings') . '&nonce=' . $nonce;
        }

        // Show notice
        echo '
        <div id="buttonizer-admin-notice" class="notice notice-info">
            <p>Hey there! You\'re currently using <b>Buttonizer</b> for a while now and we really hope you like it! Would you like to review us on WordPress and share your experience? This way you support us developing new features for Buttonizer and spread the love!</p>

            <p><a href="https://wordpress.org/support/plugin/button-contact-vr/reviews/?filter=5&utm_source=wp-plugin-request-review-btn" target="_blank" onClick="buttonizerAdminNotice()" class="button button-primary"><span class="dashicons dashicons-star-filled" style="vertical-align: middle; font-size: 16px;"></span> Review buttonizer</a>&nbsp;&nbsp;<a href="javascript:void(0)" onClick="buttonizerAdminNotice()" class="button">Dismiss message</a>&nbsp;&nbsp;or&nbsp;&nbsp;<a href="https://r.buttonizer.io/feedback?utm_source=wp-plugin-request-review-btn" target="_blank">send us feedback</a></p>
        </div>

        <script>
            function buttonizerAdminNotice() {
                const notice = document.querySelector("#buttonizer-admin-notice");
                notice.style.height  = (notice.clientHeight - 2) + "px";
                notice.style.transition = "all 150ms ease-in-out";

                setTimeout(() => {
                    notice.style.opacity = 0;
                    notice.style.height = "0px";
                    notice.style.margin = "0px";
                }, 150)
                setTimeout(() => {
                    notice.remove();
                }, 500)

                fetch("' . esc_js($endPoint) . '", {
                    method: "POST",
                    headers: {
                      "Content-Type": "application/json",
                      "X-WP-Nonce": "' . esc_js($nonce) . '",
                    },
                    body: JSON.stringify({
                        data: {
                            markAsReviewed: true
                        }
                    }),
                  })
            }
        </script>

        ';
    }
}
