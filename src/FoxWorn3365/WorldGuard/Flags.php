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
 * File: /Flags.php
 * Description: Manage and have a list of all available flags
 */

namespace FoxWorn3365\WorldGuard;

use pocketmine\utils\TextFormat;

class Flags {
    protected ?RegionManager $region;
    public static array $flags = [
        'block_break' => 'BlockBreak',
        'block_place' => 'BlockPlace',
        'block_burn' => 'BlockBurn',
        'block_form' => 'BlockForm',
        'block_melt' => 'BlockMelt',
        'block_item_pickup' => 'BlockItemPickup',
        'block_spread' => 'BlockSpread',
        'block_teleport' => 'BlockTeleport',
        'block_update' => 'BlockUpdate',
        'brew_item' => 'BrewItem',
        'chest_pair' => 'ChestPair',
        'leaves_decay' => 'LeavesDecay',
        'sign_change' => 'SignChange',
        'entity_combust' => 'EntityCombust',
        'entity_damage' => 'EntityDamage',
        'entity_death' => 'EntityDeath',
        'entity_despawn' => 'EntityDespawn',
        'entity_explode' => 'EntityExplode',
        'entity_motion' => 'EntityMotion',
        'entity_spawn' => 'EntitySpawn',
        'entity_teleport' => 'EntityTeleport',
        'craft_item' => 'CraftItem',
        'furnace_burn' => 'FurnaceBurn',
        'furnace_smelt' => 'FurnaceSmelt',
        'inventory_open' => 'InventoryOpen',
        'inventory_transaction' => 'InventoryTransaction',
        'player_bed_enter' => 'PlayerBedEnter',
        'player_block_pick' => 'PlayerBlockPick',
        'player_bucket_fill' => 'PlayerBucketFill',
        'player_bucket_empty' => 'PlayerBucketEmpty',
        'player_chat' => 'PlayerChat',
        'player_drop_item' => 'PlayerDropItem',
        'player_edit_book' => 'PlayerEditBook',
        'player_emote' => 'PlayerEmote',
        'player_entity_interact' => 'PlayerEntityInteract',
        'player_exhaust' => 'PlayerExhaust',
        'player_experience_change' => 'PlayerExperienceChange',
        'player_interact' => 'PlayerInteract',
        'player_item_consume' => 'PlayerItemConsume',
        'player_item_use' => 'PlayerItemUse',
        'player_item_held' => 'PlayerItemHeld',
        'player_move' => 'PlayerMove'
    ];

    function __construct(RegionManager $region) {
        $this->region = $region;
    }

    public static function iterator(callable $do) : void {
        foreach (self::$flags as $flag => $event) {
            if (!$do($flag)) {
                break;
            }
        }
    }

    public static function get() : array {
        return self::$flags;
    }

    public static function getValues() : array {
        $data = [];
        foreach (self::$flags as $_k => $value) {
            $data[] = $value;
        }
        return $data;
    }

    public static function getKeys() : array {
        $data = [];
        foreach (self::$flags as $key => $_v) {
            $data[] = $key;
        }
        return $data;
    }

    public static function has(string $appearanceflag) : bool {
        foreach (self::$flags as $flag => $_v) {
            if ($flag === $appearanceflag) {
                return true;
            }
        }
        return false;
    }

    public function generate() : string {
        $chat = "";
        foreach (self::$flags as $flag => $_v) {
            if ($this->hasFlag($flag)) {
                $chat .= TextFormat::GREEN . $flag . TextFormat::WHITE . ', ';
            } else {
                $chat .= TextFormat::RED . $flag . TextFormat::WHITE . ', ';
            }
        }
        return $chat;
    }

    public function hasFlag(string $flag) : bool {
        foreach ($this->region->getFlags() as $regionflag) {
            if ($flag === $regionflag) {
                return true;
            }
        }
        return false;
    }
}