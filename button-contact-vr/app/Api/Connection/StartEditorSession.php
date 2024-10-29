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
use BZContactButton\Utils\PermissionCheck;

/**
 * Start Editor Session API
 * Creates an editor session
 *
 * @endpoint /wp-json/bz_contact_button/editor_start_session
 * @methods POST
 */
class StartEditorSession
{
    /**
     * Register route
     */
    public function registerRoute()
    {
        register_rest_route('bz_contact_button', '/editor_start_session', [
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
                'callback' => [$this, 'startSession'],
                'permission_callback' => function () {
                    return PermissionCheck::hasPermission();
                }
            ]
        ]);
    }

    /**
     * Start editor session
     */
    public function startSession()
    {
        // Request token
        $result = ApiRequest::post("/request-editor-session");

        // Handle errors
        if (is_a($result, 'WP_Error')) {
            return $result;
        }

        return $result;
    }
}
