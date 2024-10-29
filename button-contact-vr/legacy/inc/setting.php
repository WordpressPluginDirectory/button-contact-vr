<!--  * Version: 2.0 -->
<div class="wrap">
    <h2>Advanced settings</h2>
    <h2 class="nav-tab-wrapper">
        <a href="admin.php?page=contact_vr" class="nav-tab">Button contact</a>
        <a href="admin.php?page=contact_vr_showroom" class="nav-tab">Showroom</a>
        <a href="admin.php?page=contact_vr_contact_form" class="nav-tab">Contact form (beta)</a>
        <a href="admin.php?page=contact_vr_all_in_one" class="nav-tab">All in one)</a>
        <a href="admin.php?page=contact_vr_setting" class="nav-tab nav-tab-active">Setting</a>
    </h2>
    <?php if (isset($_GET['settings-updated'])) { ?>
        <div id="message" class="updated">
            <p><strong><?php esc_html_e('Settings saved.', 'button-contact-vr') ?></strong></p>
        </div>
    <?php } ?>

    <form method="post" action="options.php">
        <?php settings_fields('pzf-settings-group-setting'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Size scale</th>
                <td>
                    <input placeholder="1" type="number" name="setting_size" value="<?php echo esc_attr(get_option('setting_size')); ?>" step="0.1" />
                    <i>Default 1</i><br>
                    <i>E.g: 0.8 - 0.9 - 1.1 - 1.2</i>
                </td>
            </tr>
            <tr valign="top">
                <th rowspan="2" scope="row">Location</th>
                <td style="padding-bottom: 0;">
                    <select id="pzf_location" name="pzf_location">
                        <option value="left" <?php selected(get_option('pzf_location'), 'left'); ?>>Left</option>
                        <option value="right" <?php selected(get_option('pzf_location'), 'right'); ?>>Right</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    Bottom <input placeholder="0%" type="number" min="0" max="100" name="pzf_location_bottom" value="<?php echo esc_attr(get_option('pzf_location_bottom')); ?>" step="0.1" /> <i>unit %</i>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Turn off effects</th>
                <td>
                    <input id="pzf_off_effects" name="pzf_off_effects" type="checkbox" value="1" <?php checked(get_option('pzf_off_effects'), '1'); ?> />
                </td>
            </tr>

            <tr valign="top">
                <th rowspan="2" scope="row">Hide on</th>
                <td style="padding-bottom: 0;">
                    <input id="pzf_hide_desktop" name="pzf_hide_desktop" type="checkbox" value="1" <?php checked(get_option('pzf_hide_desktop'), '1'); ?> /> Desktop
                </td>
            </tr>
            <tr>
                <td><input id="pzf_hide_mobile" name="pzf_hide_mobile" type="checkbox" value="1" <?php checked(get_option('pzf_hide_mobile'), '1'); ?> /> Mobile
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>

    <hr />

    <div style="max-width: 1200px;">
        <h2>Try our new version</h2>
        <p style="line-height: 1em;">Ready to try something new? Buttonizer has officially taken over maintenance of this plugin and supports over <b>40+ button actions</b>, including WhatsApp, Zalo, FB Messenger, and many more.</p>
        <p style="line-height: 1em;">All of this power is already included in this plugin. Simply click "Try now" below, and you can always switch back to this legacy version if you prefer. <a href="https://r.buttonizer.io/support/knowledgebase/2617?utm_source=knowledgebase-link&utm_medium=wp-contact-button-plugin" target="_blank">Learn more here</a></p>
        <a href="?page=contact_vr_setting&try_new_version=yes" class="button">Try now</a>
    </div>
    <hr style="margin-top: 15px" />

    <h2><?php esc_html_e('Support', 'button-contact-vr'); ?></h2>
    <p>
        <?php
        echo wp_kses(
            sprintf(
                // translators: %s will be replaced with the actual link
                __('For submitting any support queries, feedback, bug reports or feature requests, please visit <a %s>this link</a>.', 'button-contact-vr'),
                'href="https://wordpress.org/plugins/button-contact-vr/" target="_blank"'
            ),
            ["a" => ["href" => [], "target" => []]]
        );
        ?>
    </p>
</div>
