<?php

// Define variable
$legacyDetected = false;

// Legacy options
$listOfOptions = array(
    "pzf_phone",
    "pzf_phone2",
    "pzf_phone3",
    "pzf_linkfanpage",
    "pzf_linkmessenger",
    "pzf_whatsapp",
    "pzf_zalo",
    "pzf_telegram",
    "pzf_instagram",
    "pzf_youtube",
    "pzf_tiktok",
    "pzf_viber",
    "pzf_linkggmap",
    "pzf_contact_link"
);

// Loop through the options and try to detect legacy plugin
foreach ($listOfOptions as $option) {
    // Check options one by one
    if (get_option($option, "") !== "") {
        $legacyDetected = true;
        break;
    }
}

// Update legacy setting
update_option('button_contact_legacy', $legacyDetected ? "yes" : "no");

// Legacy plugin settings were detected, always use legacy plugin
if ($legacyDetected) {
    define("BZ_CONTACT_BUTTON_USE_LEGACY", "YES");
}
