<?php
/**
 * Created by PhpStorm.
 * User: PCink
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
        parent::__construct($plugin);
        $this->banapi = $this->owner->getServer()->getPluginManager()->getPlugin("BanAPI");
    }

    public function onRun(int $currentTick)
    {
        foreach ($this->owner->getServer()->getOnlinePlayers() as $player) {
            if ($player->isOp()) return;
            if (!$player->isFlying()) return;
            $this->banapi->addBan($player->getName(), "Flying(飛行)", "AntiCheat", true);
        }
    }
}