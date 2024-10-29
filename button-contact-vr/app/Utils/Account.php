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

namespace BZContactButton\Utils;

class Account
{
    private static $data = null;

    /**
     * Initialize account data
     *
     * @return void
     */
    private static function initialize()
    {
        // Register options
        register_setting(BZ_CONTACT_BUTTON_NAME, BZ_CONTACT_BUTTON_NAME . "_account");

        // Load settings
        self::$data = get_option(BZ_CONTACT_BUTTON_NAME . "_account", []);
    }

    /**
     * Load account data
     *
     * @return array
     */
    public static function getData(): array
    {
        // Initialize
        if (is_null(self::$data)) {
            self::initialize();
        }

        return self::$data;
    }

    /**
     * Get an account setting
     *
     * @param string $key Account data key
     * @param any $default Default setting value
     *
     * @return any
     */
    public static function getSetting(string $key, $default = null)
    {
        // Initialize
        if (is_null(self::$data)) {
            self::initialize();
        }

        return self::isset($key) ? self::$data[$key] : $default;
    }

    /**
     * Is a account setting set?
     *
     * @param string $key Setting key
     *
     * @return bool Returns if the setting was set
     */
    public static function isset(string $key): bool
    {
        // Initialize
        if (is_null(self::$data)) {
            self::initialize();
        }

        return isset(self::$data[$key]) && !empty(self::$data[$key]);
    }

    /**
     * Sync to database changes
     *
     * @return bool
     */
    public static function syncToDatabase(object $data): bool
    {
        // Build new account object
        self::$data = [
            "name" => isset($data->user->fullName) ? $data->user->fullName : "Unknown",
            "uid" => isset($data->user->id) ? $data->user->id : "Unknown",
            "site_licensed" => isset($data->site->licensed) ? $data->site->licensed : false,
            "site_id" => isset($data->site->id) ? $data->site->id : -1,
            "site_timezone" => isset($data->site->timezone) ? $data->site->timezone : null,
            "plan_name" => isset($data->plan->name) ? $data->plan->name : "Free forever",
            "plan_id" => isset($data->plan->id) ? $data->plan->id : null,
            "plan_cancelled" => isset($data->plan->cancelled) ? $data->plan->cancelled : false,
        ];

        // Save account data
        update_option(BZ_CONTACT_BUTTON_NAME . '_account', self::$data);

        return true;
    }

    /**
     * Empty account settings
     */
    public static function emptyAccountSettings()
    {
        self::$data = [];

        update_option(BZ_CONTACT_BUTTON_NAME . '_account', self::$data);
    }
}
