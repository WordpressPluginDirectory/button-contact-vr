<!-- Version: 3.0 -->
<div class="wrap">
    <h2>All in one</h2>
    <h2 class="nav-tab-wrapper">
        <a href="admin.php?page=contact_vr" class="nav-tab">Button contact</a>
        <a href="admin.php?page=contact_vr_showroom" class="nav-tab">Showroom</a>
        <a href="admin.php?page=contact_vr_contact_form" class="nav-tab">Contact form (beta)</a>
        <a href="admin.php?page=contact_vr_all_in_one" class="nav-tab nav-tab-active">All in one</a>
        <a href="admin.php?page=contact_vr_setting" class="nav-tab">Setting</a>
    </h2>

    <?php if (isset($_GET['settings-updated'])): ?>
        <div id="message" class="updated">
            <p><strong><?php esc_html_e('Settings saved.', 'button-contact-vr'); ?></strong></p>
        </div>
    <?php endif; ?>

    <form method="post" action="options.php">
        <?php settings_fields('pzf_settings_all_in_one'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Enable:</th>
                <td>
                    <input id="pzf_enable_all_in_one" name="pzf_enable_all_in_one" type="checkbox" value="1"
                        <?php checked(get_option('pzf_enable_all_in_one'), '1'); ?> />
                </td>
            </tr>
            <!--             <tr valign="top">
                <th scope="row">Note:</th>
                <td>
                    <input placeholder="contact" type="text" name="pzf_note_all_in_one"
                           value="<?php echo esc_attr(get_option('pzf_note_all_in_one')); ?>" />
                    <br>
                    <i>Show on hover</i>
                </td>
            </tr>         -->
            <!--             <tr valign="top">
                <th scope="row">Note (show/hide)</th>
                <td>
                    <input id="pzf_note_bar_all_in_one" name="pzf_note_bar_all_in_one" type="checkbox" value="1"
                           <?php checked(get_option('pzf_note_bar_all_in_one'), '1'); ?> />
                </td>
            </tr>  -->
            <tr valign="top">
                <th scope="row">Color all in one:</th>
                <td>
                    <input id="pzf_color_all_in_one" class="my-color-field" name="pzf_color_all_in_one" type="text" value="<?php echo esc_attr(get_option('pzf_color_all_in_one')); ?>" pattern="^#([A-Fa-f0-9]{6})$" title="Color must be in the format #RRGGBB" maxlength="7" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Style icon:</th>
                <td>
                    <ul id="style-icon-vr">
                        <?php
                        $icons = range(1, 5);
                        $color = get_option('pzf_color_all_in_one') ?: '#2196f3';
                        foreach ($icons as $icon): ?>
                            <li>
                                <label for="pzf_icon_all_in_one<?php echo esc_attr($icon); ?>">
                                    <span style="background: <?php echo esc_attr($color); ?>">
                                        <img src="<?= esc_url(plugin_dir_url(BZ_CONTACT_BUTTON_MAIN_FILE)) ?>/legacy/img/icon<?= esc_attr($icon) ?>.png">
                                    </span>
                                    <input id="pzf_icon_all_in_one<?php echo esc_attr($icon); ?>" name="pzf_icon_all_in_one" type="radio" value="<?php echo esc_attr($icon); ?>"
                                        <?php checked(get_option('pzf_icon_all_in_one'), $icon); ?> />
                                    icon <?php echo esc_attr($icon); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Item show/hidden:</th>
                <td>
                    <input id="pzf_hide_default_all_in_one" name="pzf_hide_default_all_in_one" type="checkbox" value="1"
                        <?php checked(get_option('pzf_hide_default_all_in_one'), '1'); ?> />
                    <i>Show</i><br>
                    <i>Default is hidden</i>
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

<!-- CSS Admin All in one -->
<style type="text/css">
    ul#style-icon-vr {
        margin: 0;
        padding: 0;
        list-style-type: none;
    }

    ul#style-icon-vr li {
        display: inline-block;
        margin-bottom: 0;
        margin-right: 15px;
        text-align: center;
    }

    ul#style-icon-vr li label {
        display: block;
        cursor: pointer;
    }

    ul#style-icon-vr li span {
        display: block;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        position: relative;
        margin: 0 auto;
        padding: 8px;
    }

    ul#style-icon-vr li span img {
        max-width: 27px;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
</style>
