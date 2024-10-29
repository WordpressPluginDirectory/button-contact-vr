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

namespace BZContactButton\Api\Settings;

use BZContactButton\Utils\ApiRequest;
use BZContactButton\Utils\Account;
use BZContactButton\Utils\PermissionCheck;
use BZContactButton\Utils\Settings;

/**
 * Migration API
 *
 * @endpoint /wp-json/bz_contact_button/migrate
 * @methods POST
 */
class MigrateToStandalone
{
    /**
     * Register route
     */
    public function registerRoute()
    {
        register_rest_route('bz_contact_button', '/migrate', [
            [
                'methods'  => ['POST'],
                'args' => [
                    'skip' => [
                        'required' => false,
                        'type' => "boolean"
                    ],
                    'siteId' => [
                        'required' => false,
                        'type' => "string"
                    ],
                    'authorization' => [
                        'required' => false,
                        'type' => "string"
                    ],
                    'nonce' => [
                        'validate_callback' => function ($value) {
                            return wp_verify_nonce($value, 'wp_rest');
                        },
                        'required' => true
                    ],
                ],
                'callback' => [$this, 'migrate'],
                'permission_callback' => function () {
                    return PermissionCheck::hasPermission(true); // Only Admins/Super admins can migrate
                }
            ]
        ]);
    }

    /**
     * Start migrating
     */
    public function migrate($request)
    {
        // Skipping migration to standalone
        if ($request->get_param('skip') === true) {
            Settings::setSetting("installed_at", new \DateTime('now'));
            Settings::setSetting("force_legacy", true);
            Settings::saveUpdatedSettings();

            return [
                'status' => 'OK'
            ];
        }

        if ($request->get_param('unskip') === true) {
            Settings::setSetting("force_legacy", false);
            Settings::saveUpdatedSettings();

            return [
                'status' => 'OK'
            ];
        }

        // Migrate data before uploading to standalone
        if (Settings::getSetting("migration_version") !== BZ_CONTACT_BUTTON_LAST_MIGRATION) {
            (new \Buttonizer\Legacy\Utils\Update())->selfMigrate(Settings::getSetting("migration_version"));
        }

        // Connect Site ID
        $siteId = $request->get_param('siteId');

        // Connect Authorization key
        $authorization = $request->get_param('authorization');

        // Data
        $args = array(
            "siteId" => $siteId,
            "authorization" => $authorization,
            "platform" => "wordpress",
            "requestToken" => true,
        );

        // Connect Authorization key
        $shouldMigrate = $request->get_param('shouldMigrate');

        if ($shouldMigrate) {
            $args["migrationData"] = $this->getData();
        }

        // Sync data
        $result = ApiRequest::post("/auth/tokens/create-token", $args, [], false);

        // Handle errors
        if (is_a($result, 'WP_Error')) {
            return $result;
        }

        // Make sure access token is available
        if (!$result->token) {
            return new \WP_Error('buttonizer_no_token', 'Missing connection token in response', [
                'status' => 500
            ]);
        }

        // Make a backup
        self::makeBackup();

        // Save site connection token
        ApiRequest::saveApiToken($result->token);

        // Get pre-migration settings
        $originalCanSendErrors = Settings::getSetting("can_send_errors", false);
        $originalUserPermissions = Settings::getSetting("permissions", false);
        $originalAdminBarSetting = Settings::getSetting("admin_top_bar_show_button", true);
        $originalGoogleAnalyticsEnabled = Settings::getSetting("google_analytics_enabled", false);
        $originalGoogleAnalytics = Settings::getSetting("google_analytics", null);

        // Save new settings
        Settings::empty();
        Settings::setSetting("force_legacy", false);
        Settings::setSetting("finished_setup", true);
        Settings::setSetting("has_migrated", true);
        Settings::setSetting("can_send_errors", $originalCanSendErrors);
        Settings::setSetting("admin_top_bar_show_button", $originalAdminBarSetting);
        Settings::setSetting("installed_at", new \DateTime('now'));
        Settings::setSetting("last_synced_at", new \DateTime('now'));
        Settings::setSetting("site_id", $siteId);
        Settings::setSetting("include_page_data", isset($result->account->site->licensed) && $result->account->site->licensed);

        // Migrate Google Analytics setting
        if ($originalGoogleAnalyticsEnabled && $originalGoogleAnalytics) {
            Settings::setSetting("google_analytics", $originalGoogleAnalytics);
        }

        // Migrate plugin permissions
        if ($originalUserPermissions) {
            Settings::setSetting("permissions", $originalUserPermissions);
        }

        Settings::saveUpdatedSettings();

        // Sync account settings
        Account::syncToDatabase($result->account);

        return [
            'status' => 'success',
            'data' => Account::getData()
        ];
    }

    /**
     * Talk to the Buttonizer API and start migrating data
     */
    private function getData()
    {
        // Register settings
        register_setting('buttonizer', 'buttonizer_buttons');
        register_setting('buttonizer', 'buttonizer_buttons_published');
        register_setting('buttonizer', 'buttonizer_rules');
        register_setting('buttonizer', 'buttonizer_rules_published');
        register_setting('buttonizer', 'buttonizer_schedules');
        register_setting('buttonizer', 'buttonizer_schedules_published');
        register_setting('buttonizer', 'buttonizer_has_changes');

        // Check if the user has changes
        // If that's not the case we'll skip these to
        // prevent sending lot's of unnecessary data
        $hasChanges = get_option("buttonizer_has_changes", false);

        return json_encode([
            'settings' => Settings::getSettings(),
            // Migrate groups & buttons
            "groups" => get_option("buttonizer_buttons", []),
            "groups_published" => $hasChanges ? get_option("buttonizer_buttons_published", []) : null,

            // Migrate time schedules (if exists)
            "time_schedules" => get_option("buttonizer_schedules", []),
            "time_schedules_published" => $hasChanges ? get_option("buttonizer_schedules_published", []) : null,

            // Migrate time schedules (if exists)
            "page_rules" => get_option("buttonizer_rules", []),
            "page_rules_published" => $hasChanges ? get_option("buttonizer_rules_published", []) : null,
        ]);
    }

    /**
     * Clean-up pre-3.0 data
     *
     * But we'll keep it, because stuff can go wrong and we don't like that :)
     */
    private static function makeBackup(): void
    {
        // Register special backup settings
        register_setting('buttonizer', 'buttonizer_buttons_backup_30');
        register_setting('buttonizer', 'buttonizer_buttons_published_backup_30');
        register_setting('buttonizer', 'buttonizer_has_changes_backup_30');
        register_setting('buttonizer', 'buttonizer_rules_backup_30');
        register_setting('buttonizer', 'buttonizer_rules_published_backup_30');
        register_setting('buttonizer', 'buttonizer_schedules_backup_30');
        register_setting('buttonizer', 'buttonizer_schedules_published_backup_30');
        register_setting('buttonizer', 'buttonizer_settings_backup_30');

        // Save restorable data
        update_option('buttonizer_buttons_backup_30', get_option('buttonizer_buttons'));
        update_option('buttonizer_buttons_published_backup_30', get_option('buttonizer_buttons_published'));
        update_option('buttonizer_has_changes_backup_30', get_option('buttonizer_has_changes'));
        update_option('buttonizer_rules_backup_30', get_option('buttonizer_rules'));
        update_option('buttonizer_rules_published_backup_30', get_option('buttonizer_rules_published'));
        update_option('buttonizer_schedules_backup_30', get_option('buttonizer_schedules'));
        update_option('buttonizer_schedules_published_backup_30', get_option('buttonizer_schedules_published'));
        update_option('buttonizer_settings_backup_30', get_option('buttonizer_settings'));

        // Delete old options
        delete_option('buttonizer_buttons');
        delete_option("buttonizer_buttons_published");
        delete_option("buttonizer_has_changes");
        delete_option("buttonizer_rules");
        delete_option("buttonizer_rules_published");
        delete_option("buttonizer_schedules");
        delete_option("buttonizer_schedules_published");
    }

    /**
     * Remove deprecated code
     */
    public static function getReadyForMigration()
    {
        register_setting('buttonizer', 'buttonizer_buttons');
        $groups = get_option("buttonizer_buttons", []);

        // Don't do anything
        if (count($groups) === 0) {
            return null;
        }

        $migrationSteps = "";

        foreach ($groups as $buttonGroup) {
            if (!isset($buttonGroup['buttons'])) continue;

            foreach ($buttonGroup['buttons'] as $button) {
                if (isset($button['type']) && $button['type'] === "javascript_pro" && isset($button['action']) && $button['action'] !== "") {
                    $migrationSteps .=
                        'window.addEventListener("buttonizer_button_clicked", event => {
  if(event.detail.button_id === "' . $button['id'] . '") {
    ' . rawurldecode($button['action']) . '
  }
});

';
                }
            }
        }

        return $migrationSteps !== "" ? $migrationSteps : null;
    }
}
