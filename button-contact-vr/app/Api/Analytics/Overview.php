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

namespace BZContactButton\Api\Analytics;

use BZContactButton\Utils\ApiRequest;
use BZContactButton\Utils\PermissionCheck;

/**
 * Analytics API
 *
 * @endpoint /wp-json/bz_contact_button/analytics/overview
 * @methods POST
 */
class Overview
{
    /**
     * Register route
     */
    public function registerRoute()
    {
        register_rest_route('bz_contact_button', '/analytics/overview', [
            [
                'methods'  => ['POST'],
                'args' => [
                    'type' => [
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
                'callback' => [$this, 'getGraph'],
                'permission_callback' => function () {
                    return PermissionCheck::hasPermission();
                }
            ]
        ]);
    }

    /**
     * Sync data
     */
    public function getGraph($request)
    {
        $type = $request->get_param('type', 'weekly');
        $timezone = $request->get_param('timezone', 'site');

        // Sync data
        $result = ApiRequest::post("/analytics/overview?type=" . $type . "&timezone=" . $timezone);

        // Handle errors
        if (is_a($result, 'WP_Error')) {
            return $result;
        }

        return [
            'status' => 'success',
            'data' => $result
        ];
    }
}
