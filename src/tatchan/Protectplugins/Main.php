<?php

declare(strict_types=1);

namespace tatchan\Protectplugins;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        new Config($this->getDataFolder() . "config.yml",Config::DETECT,["plugins" => []]);
        $plugins = $this->getConfig()->get("plugins");
        $this->getLogger()->notice("監視対象:" . implode(",",$plugins));

        $this->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function (/** @noinspection PhpUnusedParameterInspection */ int $currentTick) use ($plugins): void {

            $plugindisable = [];
            foreach ($plugins as $pluginname) {
                $plugin = $this->getServer()->getPluginManager()->getPlugin($pluginname);
                if($plugin === null){
                    $this->getLogger()->critical($pluginname . "が読み込まれていません");
                    $plugindisable[] = $pluginname;
                }elseif (!$this->getServer()->getPluginManager()->isPluginEnabled($plugin)){
                    $this->getLogger()->critical($pluginname . "が無効になっています");
                    $plugindisable[] = $pluginname;
                }
            }
            if(count($plugindisable) > 0){
                $this->getLogger()->notice("プラグインが正常に動作していないためサーバーを終了します");
                $this->getLogger()->notice("読み込まれていないプラグイン:" . implode(",",$plugindisable));
                $this->getServer()->shutdown();
            }
        }), 1, 20);

    }
}
