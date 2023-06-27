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
 * File: /Core.php
 * Description: The "heart" of the plugin, manage all events
 */

declare(strict_types=1);

namespace FoxWorn3365\WorldGuard;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\InvMenu;
use pocketmine\item\Item;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\level\Position;

// Events
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent as PacketEvent;
use pocketmine\event\Event;

// Block events
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockMeltEvent;
use pocketmine\event\block\BlockFormEvent;
use pocketmine\event\block\BlockItemPickupEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\block\BlockTeleportEvent;
use pocketmine\event\block\BlockUpdateEvent;

// Entity events
use pocketmine\event\entity\EntityCombustEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\entity\EntityTeleportEvent;

// Inventory events
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\FurnaceSmeltEvent;
use pocketmine\event\inventory\FurnaceBurnEvent;
use pocketmine\event\inventory\CraftItemEvent;

// Player events
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\player\PlayerEmoteEvent;
use pocketmine\event\player\PlayerEditBookEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerBlockPickEvent;
use pocketmine\event\player\PlayerBedEnterEvent;

// Others
use pocketmine\event\block\BrewItemEvent;
use pocketmine\event\block\ChestPairEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\block\SignChangeEvent;

// Custom
use FoxWorn3365\WorldGuard\RegionManager;
use FoxWorn3365\WorldGuard\utils\Validator;
use FoxWorn3365\WorldGuard\utils\Factory;

class Core extends PluginBase implements Listener {
    protected object $regions;
    protected object $positioning;
    protected Translator $lan;
    protected Config $config;

    protected string $defaultConfig = "IyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjCiMjICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAjIwojIyAgXCAgICAgICAgICAgICAgICAvICAgICstLS0tLS0tICAgIyMKIyMgICBcICAgICAgICAgICAgICAvICAgICB8ICAgICAgICAgICMjCiMjICAgIFwgICAgICAgICAgICAvICAgICAgfCAgICAgICAgICAjIwojIyAgICAgXCAgICAvXCAgICAvICAgICAgIHwgICAtLS0rICAgIyMKIyMgICAgICBcICAvICBcICAvICAgICAgICB8ICAgICAgfCAgICMjCiMjICAgICAgIFwvICAgIFwvICAgICAgICAgKy0tLS0tLSsgICAjIwojIyAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIyMKIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjCiMgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIwojICAgIFdvcmxkR3VhcmQgZm9yIFBvY2tldE1pbmUtTVAgICAgICMKIyAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAjCiMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIwojIEF1dGhvcjogRm94V29ybjMzNjUKIyAgICAgICB8IGh0dHBzOi8vZ2l0aHViLmNvbS9Gb3hXb3JuMzM2NQojIEdpdEh1YjogaHR0cHM6Ly9naXRodWIuY29tL0ZveFdvcm4zMzY1L1dvcmxkR3VhcmQKIyBSZWxhc2VkIHVuZGVyIHRoZSBBR1BMIExpY2Vuc2UKIyBodHRwczovL2dpdGh1Yi5jb20vRm94V29ybjMzNjUvV29ybGRHdWFyZC9MSUNFTlNFCiMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIwoKZW5hYmxlZDogdHJ1ZSAgIyBXaGV0aGVyIHRoZSBwbHVnaW4gaXMgZW5hYmxlZApsYW5ndWFnZTogZW4gICAjIFNlbGVjdCB0aGUgbGFuZ3VhZ2UKCmRlbmllZC1tZXNzYWdlOiBTb3JyeSBidXQgeW91IGNhbm5vdCBkbyB0aGlzIGFjdGlvbiEKCmN1c3RvbS1kZW5pZWQtbWVzc2FnZToKICBibG9ja19wbGFjZTogU29ycnkgYnV0IHlvdSBjYW5ub3QgcGxhY2UgYmxvY2tzIGhlcmUh";

    protected const AUTHOR = "FoxWorn3365";
    protected const VERSION = "0.9.5-pre";

    public function onLoad() : void {
        $this->regions = new \stdClass;
        $this->positioning = new \stdClass;
        // Import all regions 
        $this->regions = (new RegionManager($this->getDataFolder()))->up();
    }

    public function onEnable() : void {
        // Create the config folder if it does not exists
        @mkdir($this->getDataFolder());

        // Register event listener
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // Load the config
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            file_put_contents($this->getDataFolder() . "config.yml", base64_decode($this->defaultConfig));
        }

        // Open the config
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        // Load the language pack
        $this->lan = new Translator($this->config->get('language', 'en'));
    }

    public function onPlayerJoin(PlayerJoinEvent $event) : void {
        $this->positioning->{$event->getPlayer()->getName()} = new \stdClass;
        $this->positioning->{$event->getPlayer()->getName()}->p = false;
        $this->positioning->{$event->getPlayer()->getName()}->a = null;
        $this->positioning->{$event->getPlayer()->getName()}->n = null;
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool{
        if (!($sender instanceof Player)) {
            $sender->sendMessage($this->lan->get("no-console"));
            return false;
        }

        // Empty treath
        if ($args == []) {
            $args[0] = "";
            $args[1] = "";
            $args[2] = "";
        }

        if ($args == null) {
            $args[0] = "";
            $args[1] = "";
            $args[2] = "";
        }

        if ($command->getName() === "worldguard") {
            $this->baseCommand($sender, $args);
            return true;
        } elseif ($command->getName() === "region") {
            $this->regionCommand($sender, $args);
            return true;
        } elseif ($command->getName() === "flags") {
            $this->flagCommand($sender, $args);
            return true;
        } else {
            return false;
        }
    }

    protected function baseCommand(Player $sender, array $args) : void {
        if ($args[0] === "info" || empty($args[0])) {
            $sender->sendMessage("§2§lWorld§d§lGuard§r\n§lVersion " . self::VERSION . " by " . self::AUTHOR . "\n\n§rGitHub: https://github.com/FoxWorn3365/WorldGuard\n\n§r§cPlease submit any type of bug or question on GitHub Issues!");
        } elseif ($args[0] === "pos1") {
            if (@gettype(@$this->positioning->{$sender->getName()}->n) !== 'string') {
                $sender->sendMessage("§c" . $this->lan->get("region-defining-error"));
                return;
            }
            $this->positioning->{$sender->getName()}->p = true;
            $this->positioning->{$sender->getName()}->a = Factory::object(['x' => $sender->getPosition()->getX(), 'z' => $sender->getPosition()->getZ()]);
            $sender->sendMessage("§a" . $this->lan->get('first-point-defined') . " X:{$sender->getPosition()->getX()}, Z:{$sender->getPosition()->getZ()}");
        } elseif ($args[0] === "pos2") {
            if (@$this->positioning->{$sender->getName()}->p && @$this->positioning->{$sender->getName()}->a instanceof \stdClass && @$this->positioning->{$sender->getName()}->n !== null) {
                // yee, let's set the second pos and then define the area by the cached name!
                $name = $this->positioning->{$sender->getName()}->n;
                $region = new RegionManager($this->getDataFolder());
                // We need to do this shit to create the region status ew
                $region = $region->create($name, $this->positioning->{$sender->getName()}->a, Factory::object(['x' => $sender->getPosition()->getX(), 'z' => $sender->getPosition()->getZ()]), $sender);
                if ($region instanceof RegionManager) {
                    $this->regions->{$name} = $region;
                    $sender->sendMessage("§a" . $this->lan->get('second-point-defined') . " X:{$sender->getPosition()->getX()}, Z:{$sender->getPosition()->getZ()}");
                    $sender->sendMessage("§a" . $this->lan->get('region-created', ['rname' => $name]) . " §7/flags {$name} <FLAG> !");
                } else {
                    $sender->sendMessage("§c" . $this->lan->get('error-already') . " '{$name}'!");
                }
                // Reset the object
                $this->positioning->{$sender->getName()} = new \stdClass;
                $this->positioning->{$sender->getName()}->n = null;
                $this->positioning->{$sender->getName()}->a = null;
                $this->positioning->{$sender->getName()}->p = false;
            } else {
                $sender->sendMessage("§c" . $this->lan->get('not-defined-region'));
            }
        } else {
            $sender->sendMessage("§2§lWorld§d§lGuard§r\n§lVersion " . self::VERSION . " by " . self::AUTHOR . "\n\n§rGitHub: https://github.com/FoxWorn3365/WorldGuard\n\n§r§cPlease submit any type of bug or question on GitHub Issues!");
        }
    }

    protected function regionCommand(Player $sender, array $args) : void {
        $name = @$args[1];
        if (empty($args[0])) {
            $sender->sendMessage("§c" . $this->lan->get('error-region-command-args') . "\n§rUsage: /region [create|list|info|delete] <REGION>");
            return;
        } elseif ($args[0] === "list") {
            $message = $this->lan->get('all-regions');
            foreach ($this->regions as $name => $region) {
                $message .= "\n{$name} (World: {$region->getWorld()})";
            } 
            $sender->sendMessage($message);
        } elseif ($args[0] === "create") {
            if (empty($name)) {
                $sender->sendMessage("§c" . $this->lan->get('no-name-defined') . "\n§rUsage: /region create <NAME>");
                return;
            }
            // Define the region
            if (@$this->positioning->{$sender->getName()}->n !== null) {
                $sender->sendMessage("§c" . $this->lan->get('already-defining-error'));
                return;
            }
            $this->positioning->{$sender->getName()}->n = $name;
            $sender->sendMessage("§a" . $this->lan->get('create-success'));
        } elseif ($args[0] === "info") {
            if (empty($name)) {
                $sender->sendMessage("§c" . $this->lan->get('no-name-defined') . "\n§rUsage: /region info <NAME>");
                return;
            } 
            // Get config and generate info
            if (@$this->regions->{$name} === null) {
                $sender->sendMessage("§c" . $this->lan->get('region-not-found', ['rname' => $name]));
                return;
            } 
            // Now get config
            $region = $this->regions->{$name};
            $sender->sendMessage("Region: §l{$name}§r\nWorld: {$region->getWorld()}\nPlayers: " .implode("\n - ", $region->getPlayers()) ."\nFlags:\n" . implode(' ,', $region->getFlags()));
        } elseif ($args[0] === "flags") {
            if (empty($name)) {
                $sender->sendMessage("§c" . $this->lan->get('no-name-defined') . "\n§rUsage: /region flags <NAME>\nAlias: /flags <NAME>");
                return;
            } 
            // Get config and generate info
            if (@$this->regions->{$name} === null) {
                $sender->sendMessage("§c" . $this->lan->get('region-not-found', ['rname' => $name]));
                return;
            } 
            // Now get flags
            $manager = new Flags($this->regions->{$name});
            $sender->sendMessage("Flags for region '{$name}':\n{$manager->generate()}");
        } elseif ($args[0] === "delete" || $args[0] === "remove") {
            // Delete a region
            if (empty($name)) {
                $sender->sendMessage("§c" . $this->lan->get('no-name-defined') . "\n§rUsage: /region remove <NAME>");
                return;
            } 
            // Get config and generate info
            if (@$this->regions->{$name} === null) {
                $sender->sendMessage("§c" . $this->lan->get('region-not-found', ['rname' => $name]));
                return;
            } 

            $this->regions->{$name}->delete();
            $sender->sendMessage("§a" . $this->lan->get('region-deleted', ['rname' => $name]));
        } elseif ($args[0] === "player" && !empty($args[1]) && !empty($args[2])) {
            if (empty($name)) {
                $sender->sendMessage("§c" . $this->lan->get('no-name-defined') . "\n§rUsage: /region player <REGION NAME> <PLAYER NAME>");
                return;
            } 
            // Check presence
            if (@$this->regions->{$name} === null) {
                $sender->sendMessage("§c" . $this->lan->get('region-not-found', ['rname' => $name]));
                return;
            } 

            // Add or remove a player
            $player = $args[2];
            if ($this->regions->{$name}->hasPlayer($player)) {
                $this->regions->{$name}->removePlayer($player);
                $sender->sendMessage("§c" . $this->lan->get('player-removed', ['pname' => $player]));
            } else {
                $this->regions->{$name}->addPlayer($player);
                $sender->sendMessage("§c" . $this->lan->get('player-added', ['pname' => $player]));
            }
        } else {
            $sender->sendMessage("Usage: /rg [info|create|list|flags|delete|player] <REGION>");
        }
    }

    protected function flagCommand(Player $sender, array $args) : void {
        if (empty($args[0])) {
            // List of all tags
            $sender->sendMessage($this->lan->get('available-tags') . ":\n" . implode(', ', Flags::getKeys()));
        } else {
            $name = $args[0];
            if (@$this->regions->{$name} === null) {
                $sender->sendMessage("§c" . $this->lan->get('region-not-found', ['rname' => $name]));
                return;
            } 
            // Now check the other args
            if (@empty($args[1])) {
                // List of used tags from the region
                $manager = new Flags($this->regions->{$name});
                $sender->sendMessage("Flags for region '{$name}':\n{$manager->generate()}");
            } else {
                for ($a = 1; $a < count($args); $a++) {
                    $flag = $args[$a];
                    if (Flags::has($flag)) {
                        if ($this->regions->{$name}->hasFlag($flag)) {
                            // Remove the flag
                            $this->regions->{$name}->removeFlag($flag);
                            $sender->sendMessage($this->lan->get('flag-removed', ['flagname' => $flag]) . " '{$name}'!");
                        } else {
                            // Add the flag
                            $this->regions->{$name}->addFlag($flag);
                            $sender->sendMessage($this->lan->get('flag-added', ['flagname' => $flag]) . " '{$name}'!");
                        }
                    } else {
                        $sender->sendMessage('§c' . $this->lan->get('flag-added', ['flagname' => $flag]));
                    }
                }
            }
        }
    }

    // Event Handler 
    protected function eventHandler($event) : void {
        $name = $event->getEventName();
        $executor = null;
        $eventName = explode('\\', $name)[3];

        $player = null;
        $position = null;

        if (method_exists($event, 'getPlayer')) {
            $player = $event->getPlayer();
            if ($event->getPlayer()->hasPermission("worldguard.bypass")) {
                return;
            }
        }

        //var_dump($name);
        switch(explode('\\', $name)[2]) {
            case 'block': 
                if ($name === 'pocketmine\event\block\BlockPlaceEvent') {
                    $position = $event->getBlockAgainst()->getPosition();
                } else {
                    $position = $event->getBlock()->getPosition();
                }
                break;
            case 'entity':
                $position = $event->getEntity()->getPosition();
                $executor = "kill";
                break;
            case 'inventory':
                if ($name === 'pocketmine\event\inventory\FurnaceBurnEvent' || $name === 'pocketmine\event\inventory\FurnaceSmeltEvent') {
                    $position = $event->getBlock()->getPosition();
                } elseif ($name === 'pocketmine\event\inventory\InventoryTransactionEvent') {
                    $position = $event->getTransaction()->getSource()->getPosition();
                } else {
                    $position = $event->getPlayer()->getPosition();
                }
                break;
            case 'player':
                if ($name === 'pocketmine\event\player\PlayerExperienceChangeEvent') {
                    $position = $event->getEntity()->getPosition();
                } else {
                    $position = $event->getPlayer()->getPosition();
                }
                break;
        }

        foreach ($this->regions as $name => $region) {
            if (@$player !== null && Validator::member($region, $player)) {
                // If it's a region member we can ignore this!
                return;
            }
            if ($region->getWorld() === $position->getWorld()->getFolderName()) {
                // Validate pos plz
                if (Validator::position($region, $position->getX(), $position->getZ()) && Validator::flag($region, $eventName)) {
                    // Inside area, let's cancel
                    if (@$this->config->get('custom-denied-message', new \stdClass)->{Validator::convert($eventName)} !== null && @$player !== null) {
                        $player->sendMessage($this->config->get('custom-denied-message')->{Validator::convert($eventName)});
                    } elseif (@$player !== null) {
                        $player->sendMessage($this->config->get('denied-message', '§cSorry but you cannot do this!'));
                    }
                    if (method_exists($event, 'cancel')) {
                        $event->cancel();
                    } elseif ($executor !== null) {
                        $event->{$executor}();
                    }
                }
            }
        }
    }

    // Event listener manager
    // BLOCK EVENTS
    public function onBlockPlace(BlockPlaceEvent $event) : void { $this->eventHandler($event); }
    public function onBlockUpdate(BlockUpdateEvent $event) : void { $this->eventHandler($event); }
    public function onBlockTeleport(BlockTeleportEvent $event) : void { $this->eventHandler($event); }
    public function onBlockSpread(BlockSpreadEvent $event) : void { $this->eventHandler($event); }
    public function onBlockItemPickup(BlockItemPickupEvent $event) : void { $this->eventHandler($event); }
    public function onBlockForm(BlockFormEvent $event) : void { $this->eventHandler($event); }
    public function onBlockBurn(BlockBurnEvent $event) : void { $this->eventHandler($event); }
    public function onBlockBreak(BlockBreakEvent $event) : void { $this->eventHandler($event); }

    // ENTITY EVENTS
    public function onEntityCombust(EntityCombustEvent $event) : void { $this->eventHandler($event); }
    public function onEntityDamage(EntityDamageEvent $event) : void { $this->eventHandler($event); }
    public function onEntityDeath(EntityDeathEvent $event) : void { $this->eventHandler($event); }
    public function onEntityDespawn(EntityDespawnEvent $event) : void { $this->eventHandler($event); }
    public function onEntityExplode(EntityExplodeEvent $event) : void { $this->eventHandler($event); }
    public function onEntityMotion(EntityMotionEvent $event) : void { $this->eventHandler($event); }
    public function onEntitySpawn(EntitySpawnEvent $event) : void { $this->eventHandler($event); }
    public function onEntityTeleport(EntityTeleportEvent $event) : void { $this->eventHandler($event); }

    // INVENTORY EVENTS
    public function onCraftItem(CraftItemEvent $event) : void { $this->eventHandler($event); }
    public function onFurnaceBurn(FurnaceBurnEvent $event) : void { $this->eventHandler($event); }
    public function onFurnaceSmelt(FurnaceSmeltEvent $event) : void { $this->eventHandler($event); }
    public function onInventoryOpen(InventoryOpenEvent $event) : void { $this->eventHandler($event); }
    public function onInventoryTransaction(InventoryTransactionEvent $event) : void { $this->eventHandler($event); }

    // PLAYER EVENTS
    public function onPlayerMove(PlayerMoveEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerItemHeld(PlayerItemHeldEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerItemUse(PlayerItemUseEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerItemConsume(PlayerItemConsumeEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerInteract(PlayerInteractEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerExperienceChange(PlayerExperienceChangeEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerExhaust(PlayerExhaustEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerEntityInteract(PlayerEntityInteractEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerEmote(PlayerEmoteEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerEditBook(PlayerEditBookEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerDropItem(PlayerDropItemEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerChat(PlayerChatEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerBucketEmpty(PlayerBucketEmptyEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerBucketFill(PlayerBucketFillEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerBlockPick(PlayerBlockPickEvent $event) : void { $this->eventHandler($event); }
    public function onPlayerBedEnter(PlayerBedEnterEvent $event) : void { $this->eventHandler($event); }
}