<div class="wrap">
    <h2>Button contact VR</h2>

    <h2 class="nav-tab-wrapper">
        <a href="admin.php?page=contact_vr" class="nav-tab nav-tab-active">Button contact</a>
        <a href="admin.php?page=contact_vr_showroom" class="nav-tab">Showroom</a>
        <a href="admin.php?page=contact_vr_contact_form" class="nav-tab">Contact form (beta)</a>
        <a href="admin.php?page=contact_vr_all_in_one" class="nav-tab">All in one </a>
        <a href="admin.php?page=contact_vr_setting" class="nav-tab">Setting</a>
    </h2>

    <?php if (isset($_GET['settings-updated'])) { ?>
        <div id="message" class="updated">
            <p><strong><?php esc_html_e('Settings saved.', 'button-contact-vr') ?></strong></p>
        </div>
    <?php } ?>

    <form method="post" action="options.php">
        <?php settings_fields('pzf-settings-group'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Hotline</th>
                <td><input placeholder="0123 456 789" type="text" name="pzf_phone" value="<?php echo esc_attr(get_option('pzf_phone')); ?>" pattern="[0-9]{5,25}" title="Valid phone number from 5 to 25 digits" />
                    <label for="pzf_color_phone">
                        <input id="pzf_color_phone" class="my-color-field" name="pzf_color_phone" type="text" value="<?php echo esc_attr(get_option('pzf_color_phone')); ?>"
                            pattern="^#([A-Fa-f0-9]{6})$" title="Color must be in the format #RRGGBB" maxlength="7" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Hotline 2</th>
                <td><input placeholder="0123 456 789" type="text" name="pzf_phone2" value="<?php echo esc_attr(get_option('pzf_phone2')); ?>" pattern="[0-9]{5,25}" title="Valid phone number from 5 to 25 digits" />
                    <label for="pzf_color_phone2">
                        <input id="pzf_color_phone2" class="my-color-field" name="pzf_color_phone2" type="text" value="<?php echo esc_attr(get_option('pzf_color_phone2')); ?>" pattern="^#([A-Fa-f0-9]{6})$" title="Color must be in the format #RRGGBB" maxlength="7" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Hotline 3</th>
                <td>
                    <input placeholder="0123 456 789" type="text" name="pzf_phone3" value="<?php echo esc_attr(get_option('pzf_phone3')); ?>" pattern="[0-9]{5,25}" title="Valid phone number from 5 to 25 digits" />
                    <label for="pzf_color_phone3">
                        <input id="pzf_color_phone3" class="my-color-field" name="pzf_color_phone3" type="text" value="<?php echo esc_attr(get_option('pzf_color_phone3')); ?>" pattern="^#([A-Fa-f0-9]{6})$" title="Color must be in the format #RRGGBB" maxlength="7" />
                    </label>
                </td>
            </tr>
            <tr valign="top" style=" border-bottom: 1px dashed #bfbfbf; ">
                <th scope="row" valign="top">Hotline bar (show/hide)</th>
                <td>
                    <label for="pzf_phone_bar">
                        <input id="pzf_phone_bar" name="pzf_phone_bar" type="checkbox" value="1" <?php echo esc_attr(get_option('pzf_phone_bar')) == '1' ? 'checked="checked"' : '' ?> />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Zalo</th>
                <td><input placeholder="0123 456 789" type="text" name="pzf_zalo" value="<?php echo esc_attr(get_option('pzf_zalo')); ?>" pattern="\d{5,25}" title="Valid phone number from 5 to 25 digits" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Telegram</th>
                <td><input placeholder="Link telegram" type="url" name="pzf_telegram" value="<?php echo esc_attr(get_option('pzf_telegram')); ?>" title="URL hợp lệ" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Instagram</th>
                <td><input placeholder="Link instagram" type="url" name="pzf_instagram" value="<?php echo esc_attr(get_option('pzf_instagram')); ?>" title="URL hợp lệ" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Youtube</th>
                <td><input placeholder="Link youtube" type="url" name="pzf_youtube" value="<?php echo esc_attr(get_option('pzf_youtube')); ?>" title="URL hợp lệ" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Tiktok</th>
                <td><input placeholder="Link tiktok" type="url" name="pzf_tiktok" value="<?php echo esc_attr(get_option('pzf_tiktok')); ?>" title="URL hợp lệ" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Link fanpage</th>
                <td><input placeholder="Link fanpage" type="url" name="pzf_linkfanpage" value="<?php echo esc_attr(get_option('pzf_linkfanpage')); ?>" title="URL hợp lệ" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Link messenger</th>
                <td><input placeholder="Link messenger" type="url" name="pzf_linkmessenger" value="<?php echo esc_attr(get_option('pzf_linkmessenger')); ?>" title="URL hợp lệ" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Whatsapp</th>
                <td><input placeholder="0123456789" type="text" name="pzf_whatsapp" value="<?php echo esc_attr(get_option('pzf_whatsapp')); ?>" pattern="\d{5,25}" title="Valid phone number from 5 to 25 digits" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Viber</th>
                <td><input placeholder="0123 456 789" type="text" name="pzf_viber" value="<?php echo esc_attr(get_option('pzf_viber')); ?>" pattern="\d{5,25}" title="Valid phone number from 5 to 25 digits" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Link map</th>
                <td>
                    <input placeholder="Link google map" type="url" name="pzf_linkggmap" value="<?php echo esc_attr(get_option('pzf_linkggmap')); ?>" title="URL hợp lệ" />
                    <label for="pzf_color_linkggmap">
                        <input id="pzf_color_linkggmap" class="my-color-field" name="pzf_color_linkggmap" type="text" value="<?php echo esc_attr(get_option('pzf_color_linkggmap')); ?>" pattern="^#([A-Fa-f0-9]{6})$" title="Color must be in the format #RRGGBB" maxlength="7" />
                    </label>
                </td>
            </tr>
            <tr valign="top" style=" border-bottom: 1px dashed #bfbfbf; ">
                <th scope="row">Contact link</th>
                <td>
                    <input placeholder="/lien-he/" type="url" name="pzf_contact_link" value="<?php echo esc_attr(get_option('pzf_contact_link')); ?>" title="URL hợp lệ" />
                    <label for="pzf_color_contact">
                        <input id="pzf_color_contact" class="my-color-field" name="pzf_color_contact" type="text" value="<?php echo esc_attr(get_option('pzf_color_contact')); ?>" pattern="^#([A-Fa-f0-9]{6})$" title="Color must be in the format #RRGGBB" maxlength="7" />
                    </label>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>

    <hr />

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
