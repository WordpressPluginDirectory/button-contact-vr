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

use BZContactButton\Utils\Settings;

class ApiRequest
{
    /**
     * @var string|null Buttonizer API External Access Token
     */
    private static $token = null;

    public function __construct() {}

    /**
     * @param string $token Buttonizer API External Access Token
     *
     * @return void
     */
    public static function saveApiToken(string $token)
    {
        // Temporary save token if we need it later on
        self::$token = $token;

        // Save site connection token
        register_setting(BZ_CONTACT_BUTTON_NAME,  BZ_CONTACT_BUTTON_NAME . '_site_connection');
        update_option(BZ_CONTACT_BUTTON_NAME . '_site_connection', $token);

        Settings::setSetting('token_expiration', new \DateTime("+5 month"), true);
    }


    /**
     * @param string $token Buttonizer API External Access Token
     *
     * @return string|null External Access Token or null
     */
    public static function getApiToken()
    {
        if (self::$token) return self::$token;

        // Connect Site ID
        $token = get_option(BZ_CONTACT_BUTTON_NAME . '_site_connection');

        // Token not available. Get from transient instead
        if (!$token) {
            $token = get_transient(BZ_CONTACT_BUTTON_NAME . '_site_connection');

            // Save transient token in option and remove transient
            if (!$token) {
                return false;
            }

            register_setting(BZ_CONTACT_BUTTON_NAME,  BZ_CONTACT_BUTTON_NAME . '_site_connection');
            update_option(BZ_CONTACT_BUTTON_NAME . '_site_connection', $token);

            Settings::setSetting('token_expiration', new \DateTime("+1 month"), true);

            delete_transient(BZ_CONTACT_BUTTON_NAME . '_site_connection');
        }

        // Temporary save token if we need it later on
        self::$token = $token;

        return $token;
    }

    /**
     * Delete stored API External Access Token
     *
     * @return boolean If the key was removed or not
     */
    public static function deleteApiToken()
    {
        // Remove token from cache
        self::$token = null;

        // Delete site connection token in options
        delete_option(BZ_CONTACT_BUTTON_NAME . '_site_connection');

        // Delete site connection token
        delete_transient(BZ_CONTACT_BUTTON_NAME . '_site_connection');

        return;
    }

    /**
     * Sends a POST request to the Buttonizer API
     *
     * @param string $path Endpoint
     * @param array $body Request body
     *
     * @return object
     */
    public static function post(string $path = '/', array $body = [], array $headers = [], $withAuthorization = true, $refreshtokenCheck = true)
    {
        // Add Authorization headers to request
        if ($withAuthorization) {
            // Always check if we have to refresh their token
            if ($refreshtokenCheck) {
                $refreshTokenResult = self::refreshToken();

                // Handle token refresh errors
                if (is_a($refreshTokenResult, 'WP_Error')) {
                    return $refreshTokenResult;
                }
            }

            // Get token
            $token = self::getApiToken();

            // Token not available
            if (!$token) {
                return new \WP_Error('buttonizer_token_expired', 'Your sites authorization token has expired and was not automatically renewed, please reconnect your site.', [
                    'status' => 401
                ]);
            }

            // Add External Authorization header
            $headers['x-external-authorization'] = "Bearer " . $token;
        }

        // Build request
        $args = [
            'body' => $body,
            'headers' => $headers,
            "timeout" => defined("BZ_CONTACT_BUTTON_API_TIMEOUT") ? BZ_CONTACT_BUTTON_API_TIMEOUT : 20
        ];

        // Execute
        $response = wp_remote_post(BZ_CONTACT_BUTTON_API_URI . ($withAuthorization ? '/external' : '') . $path, $args);

        // Get status
        $status = wp_remote_retrieve_response_code($response);

        // Get body
        $body = wp_remote_retrieve_body($response);

        // Request failed
        if ($status < 200 || $status >= 300) {
            $errorStatus = "buttonizer_api_request_failed";
            $errorMessage = "Could not fetch data, site was deleted, or the sites authorization token was invalidated. Please contact us if the error persists.";

            // Our API failed. Tell this
            if ($status === 500) {
                $errorStatus = "buttonizer_api_server_error";
                $errorMessage = "Sorry, there was an internal server issue on our Buttonizer API. This is an bug on our side, please try again later or contact us if the error persists.";
            }

            // Token was expired
            if ($status === 401) {
                $errorStatus = "buttonizer_token_expired";
                $errorMessage = "Your sites authorization token has expired and was not automatically renewed, please reconnect your site.";
            }

            // Request failed or API is down :(
            if ($status === "" && $body === "") {
                $errorStatus = "buttonizer_api_not_reachable";
                $errorMessage = "The request has failed because the Buttonizer API wasn't reachable. Maybe your provider has blocked outgoing requests to our API or the Buttonizer API is down for maintenance. Please try again later or contact us if the error persists.";
                $status = 400;
            }

            return new \WP_Error($errorStatus, $errorMessage, [
                'status' => $status,
                'message' => $body
            ]);
        }

        // Parse result
        $result = json_decode($body);

        // Make sure expected data is available
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_Error('buttonizer_parse_error', 'Unable parsing received data (JSON error: ' . json_last_error() . ')', [
                'status' => 500
            ]);
        }

        // Make sure to always return an object
        if (is_array($result)) {
            return new \stdClass;
        }

        // Return object
        return $result;
    }

    /**
     * Only refresh token if required
     */
    public static function refreshToken()
    {
        $tokenExpiration = Settings::getSetting('token_expiration');

        // Token will not expire anytime soon
        if (new \DateTime('+3 month') <= $tokenExpiration) {
            return null;
        }

        // Generate a refresh token
        $result = self::post("/session_refresh", [], [], true, false);

        // Handle errors
        if (is_a($result, 'WP_Error')) {
            return $result;
        }

        // Update token
        self::saveApiToken($result->token);

        return true;
    }
}
