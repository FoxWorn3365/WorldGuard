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
 * File: /RegionManager.php
 * Description: A simple class to manage Regions
 */

namespace FoxWorn3365\WorldGuard;

use pocketmine\player\Player;

class RegionManager {
    protected object $config;
    protected ?string $name = null;
    protected string $basedir;

    function __construct(string $basedir) {
        $this->basedir = $basedir;
        $this->config = new \stdClass;
    }

    public function exists(string $name) : bool {
        if (@$this->getAll()->{$name} !== null) {
            return true;
        }
        return false;
    }

    public function create(string $name, object $from, object $to, Player|string $playerOrWorld, string $playername = null) : self|bool {
        if ($this->name !== null) {
            return false;
        }
        if ($this->exists($name)) {
            return false;
        }
        $this->name = $name;
        if ($playerOrWorld instanceof Player) {
            $world = $playerOrWorld->getWorld()->getFolderName();
            $playername = $playerOrWorld->getName();
        } else {
            $world = $playerOrWorld;
            $playername = $playername;
        }
        $this->config->world = $world;
        $this->config->from = $from;
        $this->config->to = $to;
        $this->config->players = [];
        $this->config->players[] = $playername;
        $this->config->flags = [];
        $this->config->enabled = true;
        $this->config->selfSigned = "coa93J_internalRegionDev1";
        $this->save();
        return $this;
    }

    protected function getAll() : object {
        if (file_exists("{$this->basedir}regions.json")) {
            return json_decode(file_get_contents("{$this->basedir}regions.json"));
        }
        return new \stdClass;
    }

    protected function get() : ?object {
        if ($this->name === null) {
            return null;
        }
        return @$this->getAll()->{$this->name};
    }

    public function save() : void {
        $data = $this->getAll();
        $data->{$this->name} = $this->config;
        $this->put($data);
    }

    protected function put(string|object $data) : void {
        if (gettype($data) == 'object') {
            $data = json_encode($data);
        }
        file_put_contents("{$this->basedir}regions.json", $data);
    }

    public function delete() : void {
        // The end S12E24
        $data = $this->getAll();
        $data->{$this->name} = null;
        unset($data->{$this->name});
        $this->put($data);
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function getPlayers() : array {
        return $this->config->players;
    }

    public function addPlayer(Player|string $player) : void {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $this->config->players[] = $player;
        $this->save();
    }

    public function removePlayer(Player|string $player) : void {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        
        for ($a = 0; $a < count($this->config->players); $a++) {
            if ($this->config->players[$a] == $player) {
                $this->config->players[$a] = null;
            }
        }
        $this->save();
    }

    public function getFlags() : array {
        return $this->config->flags;
    }

    public function addFlag(string $flag) : void {
        // First, validate the flag
        if (Flags::has($flag)) {
            $this->config->flags[] = $flag;
        }
        $this->save();
    }

    public function removeFlag(string $flag) : void {
        for ($a = 0; $a < count($this->config->flags); $a++) {
            if ($this->config->flags[$a] == $flag) {
                $this->config->flags[$a] = null;
            }
        }
        $this->save();
    }

    public function hasFlag(string $flag) : bool {
        if (in_array($flag, $this->config->flags)) {
            return true;
        }
        return false;
    }

    public function hasPlayer(Player|string $player) : bool {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        if (in_array($player, $this->config->players)) {
            return true;
        }
        return false;
    }

    public function getFrom() : ?object {
        return $this->config->from;
    }

    public function getTo() : ?object {
        return $this->config->to;
    }

    public function getWorld() : ?string {
        return $this->config->world;
    }

    public function import(object $object) : ?self {
        if ($object->selfSigned !== "coa93J_internalRegionDev1") {
            return null;
        } else {
            $this->config = new \stdClass;
            $this->config->name = $object->name;
            $this->name = $object->name;
            $this->config->world = $object->world;
            $this->config->from = $object->from;
            $this->config->to = $object->to;
            $this->config->selfSigned = $object->selfSigned;
            $this->config->flags = $object->flags;
            $this->config->players = $object->players;
            return $this;
        }
    }

    public function up() : object {
        $values = new \stdClass;
        foreach ($this->getAll() as $name => $data) {
            $data->name = $name;
            $object = new RegionManager($this->basedir);
            $object->import($data);
            $values->{$name} = $object;
        }
        return $values;
    }
}