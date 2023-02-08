<?php namespace cytonix\kits\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Main;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EditKitCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "editkit", "Edit a kit");
        $this->setPermission("cytonix.kits");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("kit"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player || !$this->testPermission($sender)) {
            return;
        }
        if (is_null($kit = Manager::getKitManager()->getKitFromName($args["kit"]))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no kit with that name.");
            return;
        }
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Opening form...");
        $kit->openEditForm($sender);
    }

}