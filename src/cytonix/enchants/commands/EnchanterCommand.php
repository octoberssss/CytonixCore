<?php namespace cytonix\enchants\commands;

use cytonix\Manager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EnchanterCommand extends Command {

    public function __construct() {
        parent::__construct("enchanter", "Open the enchanter menu.");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) {
            return;
        }
        Manager::getEnchantManager()->openEnchanterMenu($sender);
    }

}