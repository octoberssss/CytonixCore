<?php namespace cytonix\kits\commands;

use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KitsCommand extends Command {

    public function __construct() {
        parent::__construct("kits", "Open the kits menu");
        $this->setAliases(["kit"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please use this command in-game.");
            return;
        }
        Manager::getKitManager()->openKitMenu($sender);
    }

}