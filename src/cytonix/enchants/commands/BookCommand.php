<?php namespace cytonix\enchants\commands;

use cytonix\enchants\types\CytonixEnchant;
use cytonix\Manager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\player\Player;

class BookCommand extends Command {

    public function __construct() {
        parent::__construct("book", "receive a random book (id, lv, chance)");
        $this->setPermission("cytonix.ce");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if(!$sender instanceof Player || !$this->testPermission($sender)) {
            return;
        }
        $id = 60;
        $level = 2;
        $chance = 50;
        if (count($args) > 2) {
            $id = $args[0];
            $level = $args[1];
            $chance = $args[2];
        }
        /** @var CytonixEnchant $enchantment */
        $enchantment = EnchantmentIdMap::getInstance()->fromId($id);
        $sender->getInventory()->addItem(
            Manager::getEnchantManager()->makeEnchantmentBook(
                $enchantment,
                $level,
                $chance
            )
        );
    }

}