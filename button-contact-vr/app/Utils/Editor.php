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

class Editor
{
    public static function getEditorLanguage()
    {
        switch (get_locale()) {
            case "nl_NL":
            case "nl_BE":
                return "nl";

            case "it_IT":
                return "it";

            case "pt_BR":
                return "pt_br";

            case "ro_RO":
                return "ro_ro";

            case "tr_TR":
                return "tr_tr";

            case "es_ES":
                return "es";

            default:
                return 'en';
        }
    }
}
