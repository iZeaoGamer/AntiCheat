<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/06/13
 * Time: 13:39
 */

namespace AntiCheat;

use AntiCheat\Task\CheckPlayerTask;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerToggleFlightEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener
{
    private $banapi;
    protected $spamplayers = [];

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->banapi = $this->getServer()->getPluginManager()->getPlugin("BanAPI");
        $this->getScheduler()->scheduleRepeatingTask(new CheckPlayerTask($this), 20);
    }

    public function onToggleFlight(PlayerToggleFlightEvent $event): void
    {
        $player = $event->getPlayer();
        if (!$player->isOp()) {
            if ($event->isFlying()) {
                $this->banapi->addBan($player->getName(), $player->getAddress(), "Flying(飛行)", "AntiCheat", true);
            } else {
                $this->banapi->addBan($player->getName(), $player->getAddress(), "Flying(飛行)", "AntiCheat", true);
            }
        }
    }

    public function onReceive(DataPacketReceiveEvent $event)
    {
        $packet = $event->getPacket();
        if ($packet instanceof LoginPacket) {
            if ($packet->serverAddress === "mcpeproxy.tk" or $packet->serverAddress === "165.227.79.111") {
                $this->banapi->addBan($packet->username, $packet->serverAddress, "PROXY(プロキシ)", "AntiCheat", true);
            }
            if ($packet->clientId === 0) {
                $this->banapi->addBan($packet->username, $packet->serverAddress, "Toolbox(ツール)", "AntiCheat", true);
            }
        }
    }

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $event)
    {
        $player = $event->getPlayer();
        $cooldown = microtime(true);
        if (isset($this->spamplayers[$player->getName()])) {
            if (($cooldown - $this->spamplayers[$player->getName()]['cooldown']) < 2) {
                $player->sendMessage("§7クールダウン中です。");
                $event->setCancelled(true);
            }
        }
        $this->spamplayers[$player->getName()]["cooldown"] = $cooldown;
    }

    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();
        if ($event instanceof EntityDamageByEntityEvent and $entity instanceof Player) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                if ($damager->getGamemode() === Player::CREATIVE or $damager->getInventory()->getItemInHand()->getId() === Item::BOW) {
                    return;
                }
                if ($damager->distance($entity) > 3.9) {
                    $event->setCancelled(true);
                }
            }
        }
    }
}
