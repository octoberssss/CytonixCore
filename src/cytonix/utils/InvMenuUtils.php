<?php namespace cytonix\utils;

use cytonix\Main;
use pocketmine\inventory\Inventory;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\ClosureTask;

class InvMenuUtils {

    public static function errorAt(Inventory $inventory, int $position, string $err) : void {
        $old = $inventory->getItem($position);
        $error = VanillaItems::RED_DYE();
        $error->setCustomName("§r§l§cError!");
        $error->setLore([$err]);
        $inventory->setItem($position, $error);
        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($position, $inventory, $old) {
            if ($inventory !== null && count($inventory->getViewers()) > 0) {
                $inventory->setItem($position, $old);
            }
        }), 40);
    }

}