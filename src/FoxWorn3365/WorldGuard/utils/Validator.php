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
 * File: /utils/Validator.php
 * Description: Validate a block place position for a region and a flag for a region
 */

namespace FoxWorn3365\WorldGuard\utils;

use pocketmine\player\Player;

use FoxWorn3365\WorldGuard\RegionManager;
use FoxWorn3365\WorldGuard\Flags;

final class Validator {
    public static function position(RegionManager $region, int|float $x, int|float $z) : bool {
        if ($region->getFrom()->x <= $x && $region->getTo()->x >= $x) {
            // In region, check z
            if ($region->getFrom()->z <= $z && $region->getTo()->z >= $z) {
                return true;
            } elseif ($region->getFrom()->z >= $z && $region->getTo()->z <= $z) {
                return true;
            }
        } elseif ($region->getFrom()->x >= $x && $region->getTo()->x <= $x) {
            // In region, check z
            if ($region->getFrom()->z >= $z && $region->getTo()->z <= $z) {
                return true;
            } elseif ($region->getFrom()->z <= $z && $region->getTo()->z >= $z) {
                return true;
            }
        }
        return false;
    }

    public static function flag(RegionManager $region, string $flag) : bool {
        $flag = array_search(str_replace('Event', '', $flag), Flags::$flags);
        return $region->hasFlag($flag);
    }

    public static function member(RegionManager $region, Player|string $player) {
        if ($player instanceof Player) {
            $player = $player->getName();
        }

        return $region->hasPlayer($player);
    }

    public static function convert(string $event) : string|bool {
        return array_search(str_replace('Event', '', $event), Flags::$flags);;
    }
}