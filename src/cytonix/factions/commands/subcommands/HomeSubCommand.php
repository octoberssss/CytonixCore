<?php namespace cytonix\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use cytonix\Manager;
use cytonix\utils\FormatUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class HomeSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("home", "Goto a faction home.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("home"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "Please use this command in-game.");
            return;
        }
        $faction = Manager::getSessionManager()->getSession($sender)->getFaction();
        if ($faction == "") {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You must be in a faction to do this.");
            return;
        }
        $faction = Manager::getFactionManager()->getFactionFromName($faction);
        if (!$faction->canInvitePlayer($sender->getName())) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "You cannot goto faction homes.");
            return;
        }
        $home = $args["home"];
        if (is_null($pos = $faction->getHome($home))) {
            $sender->sendMessage(FormatUtils::PREFIX_BAD . "There is no home with that name.");
            return;
        }
        $sender->sendMessage(FormatUtils::PREFIX_GOOD . "Warped to home.");
        $sender->teleport($pos);
    }

}