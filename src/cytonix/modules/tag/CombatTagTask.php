<?php namespace cytonix\modules\tag;

use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class CombatTagTask extends Task {

    public function onRun() : void {
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            $session = Manager::getSessionManager()->getSession($player);
            if ($session->getCombatTag() > 0) {
                $session->setCombatTag($session->getCombatTag() - 1);
                if ($session->getCombatTag() < 1) {
                    $player->sendMessage(FormatUtils::PREFIX_GOOD . "You are no longer combat tagged.");
                }
            }
        }
    }

}