<?php

/**
 * Trait Color
 *
 * Provides utility methods related to color manipulation.
 */

namespace KPG\DML\classes\Util;

trait Color{
    public function rgbToHex($r, $g, $b) {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}