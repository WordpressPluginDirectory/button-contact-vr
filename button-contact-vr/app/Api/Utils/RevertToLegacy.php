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

namespace BZContactButton\Api\Utils;

use BZContactButton\Utils\PermissionCheck;

/**
 * Reverts to previous version
 *
 * @endpoint /wp-json/bz_contact_button/revert_to_legacy
 * @methods POST
 */
class RevertToLegacy
{
    /**
     * Register route
     */
    public function registerRoute()
    {
        register_rest_route('bz_contact_button', '/revert_to_legacy', [
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
                'callback' => [$this, 'revert'],
                'permission_callback' => function () {
                    return PermissionCheck::hasPermission(true);
                }
            ]
        ]);
    }

    /**
     * Delete backup
     */
    public function revert()
    {
        update_option('button_contact_legacy', "yes");

        return [
            'status' => 'success'
        ];
    }
}
