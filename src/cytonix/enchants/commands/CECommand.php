<?php namespace cytonix\enchants\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\enchants\EnchantManager;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\player\Player;

class CECommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "ce", "Enchant a item");
        $this->setPermission("cytonix.ce");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("enchant"));
        $this->registerArgument(1, new RawStringArgument("level"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player || !$this->testPermission($sender)) {
            return;
        }
        $enchant = $args["enchant"];
        if (is_numeric($enchant)) {
            $flip = array_flip(EnchantManager::NAME_TO_ID);
            if (!isset($flip[$enchant])) {
                $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no enchant with that id.");
                return;
            }
        } else {
            if (!isset(EnchantManager::NAME_TO_ID[$enchant])) {
                $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no enchant with that name.");
                return;
            }
            $enchant = EnchantManager::NAME_TO_ID[$enchant];
        }
        $enchant = EnchantmentIdMap::getInstance()->fromId($enchant);
        $item = $sender->getInventory()->getItemInHand();
        if (!$item instanceof Durable) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "This item cannot be enchanted");
            return;
        }
        $level = $args["level"];
        if (is_nan($level)) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "The level must be a number.");
            return;
        }
        $level = abs($level);
        $item->addEnchantment(new EnchantmentInstance($enchant, $level));
        $item = Manager::getEnchantManager()->loreItem($item);
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Item successfully enchanted.");
    }

}


