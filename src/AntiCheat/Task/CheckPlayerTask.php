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

    public $plugin;
    
    public function __construct(Main $plugin)
        $this->plugin = $plugin;
    {
        $this->banapi = $this->owner->getServer()->getPluginManager()->getPlugin("BanAPI");
    }

    public function onRun(int $currentTick)
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            if ($player->isOp()) return;
            if (!$player->isFlying()) return;
           $player->close("", "Flight");
        }
    }
}
