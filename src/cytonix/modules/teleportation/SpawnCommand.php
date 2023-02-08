<?php namespace cytonix\modules\teleportation;

use cytonix\utils\FormatUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class SpawnCommand extends Command {

    public function __construct() {
        parent::__construct("spawn", "Warp to spawn");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            return;
        }
        $sender->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "You have been teleported to spawn.");
    }

}