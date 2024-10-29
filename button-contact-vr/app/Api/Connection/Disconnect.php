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

namespace BZContactButton\Api\Connection;

use BZContactButton\Utils\ApiRequest;
use BZContactButton\Utils\Account;
use BZContactButton\Utils\PermissionCheck;
use BZContactButton\Utils\Settings;

/**
 * Disconnect API
 * Invalidates External Access Token and removes data
 *
 * @endpoint /wp-json/bz_contact_button/disconnect
 * @methods POST
 */
class Disconnect
{
    private static $continueIfStatus = ["buttonizer_token_expired",  "buttonizer_api_request_failed", "buttonizer_api_server_error", "buttonizer_api_not_reachable"];

    /**
     * Register route
     */
    public function registerRoute()
    {
        register_rest_route('bz_contact_button', '/disconnect', [
            [
                'methods'  => ['POST'],
                'args' => [
                    'nonce' => [
                        'validate_callback' => function ($value) {
                            return wp_verify_nonce($value, 'wp_rest');
                        },
                        'required' => true
                    ],
                ],
                'callback' => [$this, 'disconnect'],
                'permission_callback' => function () {
                    return PermissionCheck::hasPermission();
                }
            ]
        ]);
    }

    /**
     * Disconnect
     */
    public function disconnect()
    {
        // API request
        $result = ApiRequest::post("/disconnect");

        // Handle errors
        if (is_a($result, 'WP_Error') && !in_array($result->get_error_code(), self::$continueIfStatus)) {
            return $result;
        }

        // Set last synced at
        Settings::setSetting("last_synced_at", null);
        Settings::setSetting("finished_setup", false);
        Settings::setSetting("site_id", null);

        // Save synced info
        Settings::saveUpdatedSettings();

        // Remove API token
        ApiRequest::deleteApiToken();

        // Erase account settings
        Account::emptyAccountSettings();

        return [
            'status' => 'success'
        ];
    }
}
