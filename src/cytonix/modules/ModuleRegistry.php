<?php namespace cytonix\modules;

use cytonix\Main;
use cytonix\modules\commands\CoordsCommand;
use cytonix\modules\commands\RedeemCommand;
use cytonix\modules\commands\StylusCommand;
use cytonix\modules\commands\SudoCommand;
use cytonix\modules\commands\TestCommand;
use cytonix\modules\gems\commands\GemGiveCommand;
use cytonix\modules\gems\GemListener;
use cytonix\modules\tag\CombatTagTask;
use cytonix\modules\teleportation\FlyCommand;
use cytonix\modules\teleportation\SpawnCommand;
use pocketmine\Server;

class ModuleRegistry {

    public static function init() : void {
        Server::getInstance()->getCommandMap()->registerAll("CytonixCore", [
            new SudoCommand(),
            new TestCommand(),
            new StylusCommand(),
            new SpawnCommand(),
            new FlyCommand(),
            new RedeemCommand(),
            new CoordsCommand(),
            new GemGiveCommand()
        ]);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new CombatTagTask(), 20);
        Server::getInstance()->getPluginManager()->registerEvents(new GemListener(), Main::getInstance());
    }

}