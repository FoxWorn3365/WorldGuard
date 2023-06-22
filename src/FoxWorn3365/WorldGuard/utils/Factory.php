<?php
/*
 * WorldGuard for PocketMine-MP
 * Copyright (C) 2023-now FoxWorn3365 (https://github.com/FoxWorn3365)
 * 
 * This file is apart of the WorldGuard for PMMP project!
 * 
 * Contributors:
 * 
 * Relased under the AGPL-3.0 license (https://github.com/FoxWorn3365/WorldGuard/blob/main/LICENSE)
 * Please respect the license!
 * 
 * File: /utils/Factory.php
 * STATIC CLASS
 * Description: Kida useless, generate object with a cool function
 */

namespace FoxWorn3365\WorldGuard\utils;

final class Factory {
    public static function object(array $values) : object {
        return (object)$values;
    }

    public static function inventer(array $array) : array {
        $data = [];
        foreach ($array as $key => $value) {
            $data[$value] = $key;
        }
        return $data;
    }
}