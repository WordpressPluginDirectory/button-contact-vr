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

define('BZ_CONTACT_BUTTON_NAME', 'bz_contact_button');
define('BZ_CONTACT_BUTTON_DIR', dirname(__FILE__));
define('BZ_CONTACT_BUTTON_APP_DIR', __DIR__ . "/app");
define('BZ_CONTACT_BUTTON_SLUG', basename(BZ_CONTACT_BUTTON_DIR));
define('BZ_CONTACT_BUTTON_PLUGIN_DIR', __FILE__);
define("BZ_CONTACT_BUTTON_BASE_NAME", plugin_basename(BZ_CONTACT_BUTTON_PLUGIN_FILE));
define('BZ_CONTACT_BUTTON_LAST_MIGRATION', 7);
define('BZ_CONTACT_BUTTON_LAST_TOUR_UPDATE', 250);
define('BZ_CONTACT_BUTTON_LAST_CHANGELOG_UPDATE', 260);

if (!defined("BZ_CONTACT_BUTTON_API_URI")) {
    define("BZ_CONTACT_BUTTON_API_URI", "https://api.buttonizer.io");
}

// DEBUG ONLY
if (defined("BZ_CONTACT_BUTTON_DEBUG") && BZ_CONTACT_BUTTON_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ERROR);
}

# No script kiddies
defined('ABSPATH') or die('No script kiddies please!');
