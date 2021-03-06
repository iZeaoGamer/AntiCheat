<?php
/**
 * Created by PhpStorm.
 * User: InkoHX
 * Date: 2018/08/23
 * Time: 15:22
 */

namespace AntiCheat\Task;


use AntiCheat\Main;

class CheckPlayerTask extends PluginTask
{
    protected $banapi;
    
    public function __construct(Main $plugin)
    {
        $this->banapi = $this->owner->getServer()->getPluginManager()->getPlugin("BanAPI");
    }

    public function onRun(int $currentTick)
    {
        foreach ($this->owner->getServer()->getOnlinePlayers() as $player) {
            if ($player->isOp() || $player->hasPermission("pmessentials.fly") return;
            if (!$player->isFlying()) return;
           $player->close("", "Flight");
        }
    }
}
