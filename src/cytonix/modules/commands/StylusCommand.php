<?php namespace cytonix\modules\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class StylusCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "stylus", "Use # for line break");
        $this->setPermission("cytonix.stylus");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("(name/lore)"));
        $this->registerArgument(1, new RawStringArgument("value"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player || !$this->testPermission($sender)) {
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        if ($item->getId() == 0) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Cannot change values on air.");
            return;
        }
        $type = $args["(name/lore)"];
        $value = str_replace("#", "\n", $args["value"]);
        switch($type) {
            case "name":
                $sender->getInventory()->setItemInHand($item->setCustomName($value));
                break;
            case "lore":
                $sender->getInventory()->setItemInHand($item->setLore([$value]));
                break;
            default:
                $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please specify name or lore.");
        }
    }

}