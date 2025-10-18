<?php

/**
 * Plugin Name: Call / Chat / Contact Button
 * Plugin URI: https://buttonizer.io
 * Description: Powerful platform to create chat buttons for WhatsApp, Messenger, Live Chat, Zalo, and 40+ other actions.
 * Version: 5.0.4
 * Author: Buttonizer
 * Author URI: https://buttonizer.io
 * License: GPLv2
 *
 * SOFTWARE LICENSE INFORMATION
 *
 * Copyright (c) 2017 Buttonizer, all rights reserved.
 *
 * This file is part of Buttonizer
 *
 * For detailed information regarding to the licensing of
 * this software, please review the license.txt or visit:
 * https://buttonizer.io/license/
 */

if (! defined('ABSPATH')) {
    exit;
}

// Is legacy plugin user
$legacyUser = get_option("button_contact_legacy", 'undefined') === "yes";

// Nothing defined yet, check if any option is defined
if (get_option("button_contact_legacy", 'undefined') === "undefined") {
    require_once __DIR__ . "/legacy/legacy-detector.php";
}

// Load legacy version if detected / chosen
if ($legacyUser || defined("BZ_CONTACT_BUTTON_USE_LEGACY")) {
    define('BZ_CONTACT_BUTTON_MAIN_FILE', __FILE__);

    // Load in legacy
    require_once __DIR__ . "/legacy/plugin.php";
} else {
    // Define current version
    define('BZ_CONTACT_BUTTON_VERSION', '5.0.4');
    define('BZ_CONTACT_BUTTON_PLUGIN_FILE', __FILE__);

    // Autoloader
    require_once __DIR__ . "/app/autoloader.php";

    // Get environment vars
    require_once __DIR__ . "/EnvVars.php";

    // Get environment vars
    require_once __DIR__ . "/init.php";
}
