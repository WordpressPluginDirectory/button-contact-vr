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

class PermissionCheck
{
    private static $cachedPermission = null;
    private static $cachedUserPermissions = null;

    /**
     * Does the user have access to Buttonizer?
     */
    public static function hasPermission($adminOnly = false)
    {
        // Always grand admins
        if (\is_user_logged_in() && current_user_can(is_multisite() ? 'manage_options' : 'activate_plugins')) {
            return true;
        }

        // User was not an admin
        // Deny as we apparently require admin permission
        if ($adminOnly) {
            return false;
        }

        // Use previous cached permission
        if (self::$cachedPermission !== null) {
            return self::$cachedPermission;
        }

        // By default, do not grant any permission
        $grant = false;

        // Check for additional permissions
        if (!$adminOnly && \is_user_logged_in() && Settings::isset("additional_permissions")) {
            // Loop through additional permissions
            foreach (Settings::getSetting("additional_permissions", []) as $permission) {
                if ($grant) continue;

                $grant = current_user_can($permission);
            }
        }

        self::$cachedPermission = $grant;
        return $grant;
    }

    /**
     *
     */
    public static function getUserRoles()
    {
        // Already loaded user permissions
        if (self::$cachedUserPermissions) {
            return self::$cachedUserPermissions;
        }

        // If not logged in, add guest role in roles
        if (!\is_user_logged_in()) return ["guest"];

        self::$cachedUserPermissions = get_userdata(get_current_user_id())->roles;

        return self::$cachedUserPermissions;
    }
}
