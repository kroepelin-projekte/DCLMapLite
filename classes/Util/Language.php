<?php

/**
 * Trait Language
 *
 * Provides a utility function to fetch language-specific translations
 * using the dependency injection container's language service.
 */

namespace KPG\DML\classes\Util;

trait Language {

    public static function getLang(string $lang_var): string {
        $plugin_slot_id = "copg_pgcp";
        $plugin_id = "kpg_dclmap_lite";
        global $DIC;
        return $DIC->language()->txt($plugin_slot_id."_".$plugin_id.'_'.$lang_var);
    }
}