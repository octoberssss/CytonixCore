<?php namespace cytonix\kits\commands;

use cytonix\Manager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CreateKitCommand extends Command {

    public function __construct() {
        parent::__construct("createkit", "Create a kit");
        $this->setPermission("cytonix.kits");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player || !$this->testPermission($sender)) {
            return;
        }
        Manager::getKitManager()->addKit($sender);
    }

}